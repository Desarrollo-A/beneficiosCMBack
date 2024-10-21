<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");
require 'vendor/autoload.php';

class BoletosController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->ch = $this->load->database('ch', TRUE);
        date_default_timezone_set('America/Mexico_City');
        $this->load->database('default');
        $this->load->model('beneficios/BoletosModel');
        $this->load->model('GeneralModel');
        $this->load->library("email");
        $this->load->library('GoogleApi');
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    // Funciones auxiliares para simplificar respuestas
    private function errorResponse($message)
    {
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode(["result" => false, "msg" => $message], JSON_NUMERIC_CHECK));
    }

    private function successResponse($message)
    {
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode(["result" => true, "msg" => $message], JSON_NUMERIC_CHECK));
    }

    public function getBoletos()
    {
        $dt = $this->input->post('dataValue', true);
        $data['data'] = $this->BoletosModel->getBoletos($dt)->result();
        echo json_encode($data, JSON_NUMERIC_CHECK);
    }

    public function getSolicitud()
    {
        $dt = $this->input->post('dataValue', true);
        $data['data'] = $this->BoletosModel->getSolicitud($dt)->result();
        echo json_encode($data, JSON_NUMERIC_CHECK);
    }

    public function nuevoEvento()
    {
        // Obtener y sanitizar inputs
        $titulo            = trim($this->form('titulo'));
        $descripcion       = trim($this->form('descripcion'));
        $fechaPartido       = $this->form('fechaPartido');
        $inicioPublicacion = $this->form('inicioPublicacion');
        $finPublicacion    = $this->form('finPublicacion');
        $estadio           = trim($this->form('estadio'));
        $boletos           = trim($this->form('boletos'));
        $sede             = json_decode($this->form('sedes'), true);
        $imagenPreview     = $this->file('imagenPreview');
        $imagen            = $this->file('imagen');
        $idUsuario         = (int) $this->form('idUsuario');
        $fecha             = date("Y-m-d H:i:s");

        // Preparar array para el evento
        $evento = [
            "titulo"            => $titulo,
            "descripcion"       => $descripcion,
            "fechaPartido"       => $fechaPartido,
            "lugarPartido"      => $estadio,
            "inicioPublicacion" => $inicioPublicacion,
            "finPublicacion"    => $finPublicacion,
            "horaPartido"       => '00:00',
            "sede"              => $sede,
            "limiteBoletos"     => $boletos,
            "estatus"           => 1,
            "creadoPor"         => $idUsuario,
            "modificadoPor"     => $idUsuario,
            "fechaModificacion" => $fecha,
            "fechaCreacion"     => $fecha,
        ];

        // Procesar la imagen (si existe)
        if ($imagen && $imagenPreview) {

            // Obtén las extensiones de los archivos
            $file_ext = strtolower(pathinfo($imagen->name, PATHINFO_EXTENSION));
            $file_ext2 = strtolower(pathinfo($imagenPreview->name, PATHINFO_EXTENSION));

            // Definir extensiones válidas
            $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];

            // Verifica que ambas extensiones sean válidas
            if (!in_array($file_ext, $valid_ext) || !in_array($file_ext2, $valid_ext)) {
                return $this->errorResponse("Formato de imagen no permitido.");
            }

            // Generar nombres únicos para ambas imágenes
            $file_name = "EventoBoletos_" . uniqid() . ".$file_ext";
            $file_name2 = "EventoBoletosPreview_" . uniqid() . ".$file_ext2";

            // Subir ambas imágenes
            if (!$this->upload($imagen->tmp_name, $file_name) || !$this->upload($imagenPreview->tmp_name, $file_name2)) {
                return $this->errorResponse("Error al subir las imágenes.");
            }

            // Asignar los nombres de las imágenes subidas al evento
            $evento['imagen'] = $file_name;
            $evento['imagenPreview'] = $file_name2;
        }

        // Insertar el evento en la base de datos
        $res = $this->GeneralModel->addRecordReturnId($this->schema_cm . ".boletos", $evento);

        if (!$res) {
            return $this->errorResponse("No se ha podido crear el evento.");
        }

        // Respuesta de éxito
        return $this->successResponse("Se ha creado el evento.");
    }

    public function updateEvento()
    {
        // Obtener y sanitizar inputs
        $id          = (int) $this->form('id');
        $titulo            = trim($this->form('titulo'));
        $descripcion       = trim($this->form('descripcion'));
        $fechaPartido       = $this->form('fechaPartido');
        $inicioPublicacion = $this->form('inicioPublicacion');
        $finPublicacion    = $this->form('finPublicacion');
        $estadio           = trim($this->form('estadio'));
        $boletos           = trim($this->form('boletos'));
        $sede             = json_decode($this->form('sedes'), true);
        $imagenPreview     = $this->file('imagenPreview');
        $imagen            = $this->file('imagen');
        $idUsuario         = (int) $this->form('idUsuario');
        $fecha             = date("Y-m-d H:i:s");

        // Construir el array de valores dinámicamente
        $values = [];
        if ($titulo !== null) $values["titulo"] = $titulo;
        if ($descripcion !== null) $values["descripcion"] = $descripcion;
        if ($fechaPartido !== null) $values["fechaPartido"] = $fechaPartido;
        if ($inicioPublicacion !== null) $values["inicioPublicacion"] = $inicioPublicacion;
        if ($finPublicacion !== null) $values["finPublicacion"] = $finPublicacion;
        if ($estadio !== null) $values["lugarPartido"] = $estadio;
        if ($boletos !== null) $values["limiteBoletos"] = $boletos;
        if ($sede !== null) $values["sede"] = $sede;
        if ($boletos !== null) $values["limiteBoletos"] = $boletos;
        $values["modificadoPor"] = $idUsuario;
        $values["fechaModificacion"] = $fecha;

        // Procesar la imagen (si existe)
        if ($imagen && $imagenPreview) {

            // Obtén las extensiones de los archivos
            $file_ext = strtolower(pathinfo($imagen->name, PATHINFO_EXTENSION));
            $file_ext2 = strtolower(pathinfo($imagenPreview->name, PATHINFO_EXTENSION));

            // Definir extensiones válidas
            $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];

            // Verifica que ambas extensiones sean válidas
            if (!in_array($file_ext, $valid_ext) || !in_array($file_ext2, $valid_ext)) {
                return $this->errorResponse("Formato de imagen no permitido.");
            }

            // Generar nombres únicos para ambas imágenes
            $file_name = "EventoBoletos_" . uniqid() . ".$file_ext";
            $file_name2 = "EventoBoletosPreview_" . uniqid() . ".$file_ext2";

            // Subir ambas imágenes
            if (!$this->upload($imagen->tmp_name, $file_name) || !$this->upload($imagenPreview->tmp_name, $file_name2)) {
                return $this->errorResponse("Error al subir las imágenes.");
            }

            // Asignar los nombres de las imágenes subidas al evento
            $values['imagen'] = $file_name;
            $values['imagenPreview'] = $file_name2;
        }

        // Actualizar el registro solo con los valores proporcionados
        $response["result"] = $this->GeneralModel->updateRecord($this->schema_cm . ".boletos", $values, "idBoleto", $id);

        if ($response["result"]) {
            $response["msg"] = "Se ha actualizado el evento de manera exitosa";
        } else {
            $response["msg"] = "Surgió un error al actualizar el evento";
        }

        // Devolver la respuesta como JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function updateEstatusBoletos()
    {
        $dataValue = $this->input->post("dataValue", true);
        $id = $dataValue["id"];

        $data = [
            "estatus" => intval($dataValue["estatus"])
        ];

        $updateRecord = $this->GeneralModel->updateRecord($this->schema_cm . '.boletos', $data, "idBoleto", $id);

        if ($updateRecord) {
            $response["result"] = true;
            $response["msg"] = "Se ha actualizado el estatus";
        } else {
            $response["result"] = false;
            $response["msg"] = "Ha ocurrido un error al actualizar";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    public function solicitudBoletos()
    {

        $dataValue = $this->input->post("dataValue", true);
        $id = $dataValue["id"];
        $idUsuario = $dataValue["idUsuario"];

        $data = [
            "idBoleto" => intval($id),
            "idBeneficiario" => intval($idUsuario),
            "creadoPor" => $idUsuario,
            "fechaModificacion" => date('Y-m-d H:i:s'),
            "fechaCreacion" => date('Y-m-d H:i:s'),
            "modificadoPor" => intval($idUsuario)
        ];

        $insertBatch = $this->GeneralModel->insertBatch($this->schema_cm . ".solicitudboletos", [$data]);

        if ($insertBatch) {
            $response["result"] = true;
            $response["msg"] = "Solicitud enviada";
        } else {
            $response["result"] = false;
            $response["msg"] = "Ha ocurrido un error";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    public function getSolicitudBoletos()
    {
        $data['data'] = $this->BoletosModel->getSolicitudBoletos()->result();
        echo json_encode($data, JSON_NUMERIC_CHECK);
    }

    public function updateSolicitudBoletos()
    {
        $dataValue = $this->input->post("dataValue", true);

        $id = $dataValue["id"];

        $data = [
            "solicitud" => intval($dataValue["estatus"])
        ];

        $updateRecord = $this->GeneralModel->updateRecord($this->schema_cm . '.solicitudboletos', $data, "idSolicitudBoletos", $id);

        if ($updateRecord) {
            $response["result"] = true;
            $response["msg"] = "Se ha actualizado el estatus";
        } else {
            $response["result"] = false;
            $response["msg"] = "Ha ocurrido un error al actualizar";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }
}
