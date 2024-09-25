<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

class AsistenciaEvController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistenciaEv/AsistenciaEvModel');
        $this->load->model('GeneralModel');
        $this->load->library("email");
         $this->load->library('GoogleApi');
        $this->ch = $this->load->database('ch', TRUE);
        date_default_timezone_set('America/Mexico_City');
        $this->schema_cm = $this->config->item('schema_cm');
    }

    public function getasistenciaEvento()
    {
        $data['data'] = $this->AsistenciaEvModel->getasistenciaEvento();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getasistenciaEventoUser($idUsuario)
    {
        $data['data'] = $this->AsistenciaEvModel->getasistenciaEventoUser($idUsuario);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }
    public function postGenerarQr()
    {
        $idContrato = $this->input->post('dataValue[idContrato]');
        $idEvento = $this->input->post('dataValue[idEvento]');
    
        if ($idContrato !== null && $idEvento !== null) {
            $dataEvento = $this->AsistenciaEvModel->getEventoUser($idContrato, $idEvento);
    
            if (!empty($dataEvento)) {
                $dataQr = [
                    "idContrato" => $idContrato,
                    "idEvento" => $idEvento,
                    "estatusAsistencia" => '3'
                ];
                $jsonData = json_encode($dataQr);
                $base64Data = base64_encode($jsonData);
                $qrCode = QrCode::create($base64Data)
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                    ->setSize(300)
                    ->setMargin(10)
                    ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->setForegroundColor(new Color(0, 55, 100));
    
                $writer = new PngWriter();
                $logoPath = 'C:/xampp/htdocs/beneficiosCMBack/pruebas/logo.png'; 
    
                if (file_exists($logoPath)) {
                    $logo = new \Endroid\QrCode\Logo\Logo($logoPath, 60, 42); 
                } else {
                    $logo = null;
                }
    
                $tempDir = 'C:/xampp/htdocs/beneficiosCMBack/pruebas/temp/';
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $outputFile = $tempDir . 'qr_Evento_' . $idContrato . '.png';
                $result = $writer->write($qrCode, $logo);
                $result->saveToFile($outputFile);
    
                $this->sendMail($dataEvento[0], $outputFile);
    
                echo json_encode(array("estatus" => true, "msj" => "QR generado correctamente. Datos enviados a sendMail."));
            } else {
                echo json_encode(array("estatus" => false, "msj" => "Error al obtener los datos del evento."));
            }
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Error al recibir datos (ID's)."));
        }
    }
    public function sendMail($dataValue,$qrFilePath)
    {
        // var_dump($dataValue, $qrFilePath); exit; die;
        $num_empleado = $dataValue->num_empleado;
        $nombreCompleto = $dataValue->nombreCompleto;
        $titulo = $dataValue->titulo;
        $fechaEvento = $dataValue->fechaEvento;
        $horaEvento = $dataValue->horaEvento;
        $limiteRecepcion = $dataValue->limiteRecepcion;
        $ubicacion = $dataValue->ubicacion;
        $idContrato = $dataValue->idContrato;
        $idEvento = $dataValue->idEvento;
        $estatusAsistencia = $dataValue->estatusAsistentes;

        $data = [
            'num_empleado' => $num_empleado,
            'nombreCompleto' => $nombreCompleto,
            'titulo' => $titulo,
            'fechaEvento' => $fechaEvento,
            'horaEvento' => $horaEvento,
            'limiteRecepcion' => $limiteRecepcion,
            'ubicacion' => $ubicacion,  
            'idContrato' => $idContrato,
            'idEvento' => $idEvento,
            'estatusAsistencia' => $estatusAsistencia,
            'qrFilePath' => $qrFilePath
        ];

        $correo = ['programador.analista47@ciudadmaderas.com'];

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.gmail.com';
        $config['smtp_user'] = 'programador.analista47@ciudadmaderas.com';  
        $config['smtp_pass'] = 'oeix zkyh axmj tbrv';  
        $config['smtp_port'] = 465;
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";
        $config['smtp_crypto'] = 'ssl';

        $html_message = $this->load->view("email-event", $data, true);

        $this->load->library("email");
        $this->email->initialize($config);
        $this->email->from("programador.analista47@ciudadmaderas.com");  
        $this->email->to($correo);
        $this->email->message($html_message);
        $this->email->subject("Confirmación de asistencia a evento");
        $this->email->attach($qrFilePath); 

        if ($this->email->send()) {
            echo json_encode(array("estatus" => true, "msj" => "Envío exitoso"), JSON_NUMERIC_CHECK);
            
            if (file_exists($qrFilePath)) {
                unlink($qrFilePath); 
            }
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Ocurrió un error al enviar el correo"), JSON_NUMERIC_CHECK);
        }
    }
}