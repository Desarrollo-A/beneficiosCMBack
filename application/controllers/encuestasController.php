<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class encuestasController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('encuestasModel');
		$this->load->model('generalModel');

		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');

		date_default_timezone_set('America/Mexico_City');;
	}

	public function encuestaInsert(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->encuestaInsert($dt);
	}

	public function getRespuestas(){
		$data['data'] = $this->encuestasModel->getRespuestas()->result();
		echo json_encode($data);
	}

	public function encuestaCreate(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->encuestaCreate($dt);
	}

	public function encuestaMinima(){
		$data['data'] = $this->encuestasModel->encuestaMinima()->result();
		echo json_encode($data);
	}

	public function getEncuesta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->getEncuesta($dt)->result();
		echo json_encode($data);
	}

	public function getResp1(){
		$data['data'] = $this->encuestasModel->getResp1()->result();
		echo json_encode($data);
	}

	public function getResp2(){
		$data['data'] = $this->encuestasModel->getResp2()->result();
		echo json_encode($data);
	}

	public function getResp3(){
		$data['data'] = $this->encuestasModel->getResp3()->result();
		echo json_encode($data);
	}

	public function getResp4(){
		$data['data'] = $this->encuestasModel->getResp4()->result();
		echo json_encode($data);
	}

	public function getEncNotificacion(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEncNotificacion($dt);
		echo json_encode($data);
	}

	public function getEcuestaValidacion(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEcuestaValidacion($dt);
		echo json_encode($data);
	}

	public function getPuestos(){
		$data['data'] = $this->encuestasModel->getPuestos()->result();
		echo json_encode($data);
	}

	public function getEncuestasCreadas(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEncuestasCreadas($dt)->result();
		echo json_encode($data);
	}

	public function updateEstatus(){

		$idEncuesta= $this->input->post('dataValue[idEncuesta]');
		$estatus= $this->input->post('dataValue[estatus]');

		$data = array(
			"estatus" => $estatus
		);

		$response=$this->generalModel->updateRecord('encuestasCreadas', $data, 'idEncuesta', $idEncuesta);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function getEstatusUno(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEstatusUno($dt)->result();
		echo json_encode($data);
	}
}
