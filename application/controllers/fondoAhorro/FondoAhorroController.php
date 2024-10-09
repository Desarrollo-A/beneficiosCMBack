<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class FondoAhorroController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->ch = $this->load->database('ch', TRUE);
		$this->load->database('default');
		$this->load->model('fondoAhorro/FondoAhorroModel');
		$this->load->model('GeneralModel');
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

	public function getFondo(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->FondoAhorroModel->getFondo($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function sendMail() {

		$this->input->post('dataValue', true);

		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$nombre = $this->input->post('dataValue[nombre]');
		$numEmpleado = $this->input->post('dataValue[numEmpleado]');
		$idContrato = $this->input->post('dataValue[idContrato]');
		$monto = $this->input->post('dataValue[ahorroFinal]');
		$FirstDay = $this->input->post('dataValue[FirstDay]');
		$dateNext =$this->input->post('dataValue[dateNext]');

		$fechaInicio = DateTime::createFromFormat('d-m-Y', $FirstDay);
		$fechaFin = DateTime::createFromFormat('d-m-Y', $dateNext);

		$this->ch->query("INSERT INTO ". $this->schema_cm .".fondosahorros (idContrato, fechaInicio, fechaFin, monto, esReinversion, estatusFondo, creadoPor, fechaCreacion, modificadoPor, fechaModificacion ) 
					VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())", 
					array($idContrato, $fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d'), $monto, 0, 1, $idUsuario, $idUsuario ));
				
		$this->ch->trans_complete();

		if ($this->ch->trans_status() === FALSE) {
			echo json_encode(array("estatus" => false, "msj" => "Ocurrió un error"), JSON_NUMERIC_CHECK);
		} 

		$data = [
			'nombre' => $nombre,
			'numEmpleado' => $numEmpleado,
			'monto' => $monto
		];

		$correo = ['programador.analista32@ciudadmaderas.com', 'programador.analista36@ciudadmaderas.com'];

		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'smtp.gmail.com';
		$config['smtp_user'] = 'no-reply@ciudadmaderas.com'; // testemail@ciudadmaderas.com // no-reply@ciudadmaderas.com
		$config['smtp_pass'] = '';
		$config['smtp_port'] = 465;
		$config['charset']   = 'utf-8';
		$config['mailtype']  = 'html';
		$config['newline']   = "\r\n"; 
		$config['smtp_crypto']   = 'ssl';
		
		$html_message = $this->load->view("email-fondo-ahorro-solicitud", $data, true);
				
		$this->load->library("email");
		$this->email->initialize($config);
		$this->email->from("no-reply@ciudadmaderas.com");
		$this->email->to($correo);
		$this->email->message($html_message);
		$this->email->subject("Solicitud fondo de ahorro");

		if ($this->email->send()) {
			echo json_encode(array("estatus" => true, "msj" => "Envío exitoso" ), JSON_NUMERIC_CHECK); 
		} else {
			echo json_encode(array("estatus" => false, "msj" => "Ocurrió un error"), JSON_NUMERIC_CHECK);
		}

	}

	public function getSolicitudes(){
		$data = $this->FondoAhorroModel->getSolicitudes()->result();

		$rs['result'] = count($data) > 0; 
		if ($rs['result']) {
			$rs['msg'] = '¡Listado de usuarios cargado exitosamente!';
			$rs['data'] = $data; 
		}else {
			$rs['msg'] = '¡No existen registros!';
		}

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($rs, JSON_NUMERIC_CHECK));
	}
}