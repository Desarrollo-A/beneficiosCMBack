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
		$data['data'] = $this->encuestasModel->getRespuestas();
		echo json_encode($data);
	}

	public function encuestaCreate(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->encuestaCreate($dt);
	}

	public function encuestaMinima(){
		$data['data'] = $this->encuestasModel->encuestaMinima();
		echo json_encode($data);
	}

	public function getEncuesta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->getEncuesta($dt);
		echo json_encode($data);
	}

	public function getResp1(){
		$data['data'] = $this->encuestasModel->getResp1();
		echo json_encode($data);
	}

	public function getResp2(){
		$data['data'] = $this->encuestasModel->getResp2();
		echo json_encode($data);
	}

	public function getResp3(){
		$data['data'] = $this->encuestasModel->getResp3();
		echo json_encode($data);
	}

	public function getResp4(){
		$data['data'] = $this->encuestasModel->getResp4();
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
		$data['data'] = $this->encuestasModel->getPuestos();
		echo json_encode($data);
	}

	public function encuestaConstestada(){
		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->encuestaConstestada($dt);
		echo json_encode($data);
	}

}
