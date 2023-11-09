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

	public function citas_count_status(){
		$data['data'] = $this->dashModel->citas_count_status();
		echo json_encode($data);
	}

	public function total_status_citas(){
		$data['data'] = $this->dashModel->total_status_citas();
		echo json_encode($data);
	}

	public function estatus_fecha_asistencia(){
		$dt = $this->input->post('dt', true);
		$data['data'] = $this->dashModel->estatus_fecha_asistencia($dt);
		echo json_encode($data);
	}

	public function estatus_fecha_cancelada(){
		$dt = $this->input->post('dt', true);
		$data['data'] = $this->dashModel->estatus_fecha_cancelada($dt);
		echo json_encode($data);
	}

	public function estatus_fecha_penalizada(){
		$dt = $this->input->post('dt', true);
		$data['data'] = $this->dashModel->estatus_fecha_penalizada($dt);
		echo json_encode($data);
	}

	public function fecha_minima(){
		$data['data'] = $this->dashModel->fecha_minima();
		echo json_encode($data);
	}

	public function citas_anual(){
		$dt = $this->input->post('dt', true);
		$data['data'] = $this->dashModel->citas_anual($dt);
		echo json_encode($data);
	}
}
