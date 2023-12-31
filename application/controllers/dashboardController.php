<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class dashboardController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		$this->load->database('default');
		$this->load->model('usuariosModel');
		$this->load->model('reportesModel');
		$this->load->model('especialistasModel');
		$this->load->model('dashModel');
	}
	public function index()
	{
		$this->usuarios();
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}

	public function citas(){
		$data['data'] = $this->reportesModel->citas();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->especialistasModel->especialistas();
		echo json_encode($data);
	}

	public function citasCountStatus(){
		$data['data'] = $this->dashModel->citasCountStatus();
		echo json_encode($data);
	}

	public function totalStatusCitas(){
		$data['data'] = $this->dashModel->totalStatusCitas();
		echo json_encode($data);
	}

	public function estatusFechaAsistencia(){

		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->dashModel->estatusFechaAsistencia($dt);
		echo json_encode($data);
	}

	public function estatusFechaCancelada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->dashModel->estatusFechaCancelada($dt);
		echo json_encode($data);
	}

	public function estatusFechaPenalizada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->dashModel->estatusFechaPenalizada($dt);
		echo json_encode($data);
	}

	public function fechaMinima(){
		$data['data'] = $this->dashModel->fechaMinima();
		echo json_encode($data);
	}

	public function citasAnual(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->dashModel->citasAnual($dt);
		echo json_encode($data);
	}
}
