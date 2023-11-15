<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class reportesController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		$this->load->model('reportesModel');
		$this->load->model('especialistasModel');
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}

	public function citas(){
		$dt = $this->input->post('ReportData', true);
		$data['data'] = $this->reportesModel->citas($dt);
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->especialistasModel->especialistas();
		echo json_encode($data);
	}

	public function observacion(){

		$idCita= $this->input->post('data[idCita]', true);
		$descripcion= $this->input->post('data[descripcion]', true);
		
		$data['data'] = $this->reportesModel->observacion($idCita, $descripcion);
		echo json_encode($data);
	}
}
