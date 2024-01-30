<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class DashboardController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		$this->load->database('default');
		$this->load->model('UsuariosModel');
		$this->load->model('ReportesModel');
		$this->load->model('EspecialistasModel');
		$this->load->model('DashModel');
	}
	public function index()
	{
		$this->usuarios();
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data);
	}

	public function citas(){
		$data['data'] = $this->ReportesModel->citas();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->EspecialistasModel->especialistas();
		echo json_encode($data);
	}

	public function citasCountStatus(){
		$data['data'] = $this->DashModel->citasCountStatus();
		echo json_encode($data);
	}

	public function totalStatusCitas(){
		$data['data'] = $this->DashModel->totalStatusCitas();
		echo json_encode($data);
	}

	public function estatusFechaAsistencia(){

		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaAsistencia($dt);
		echo json_encode($data);
	}

	public function estatusFechaCancelada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaCancelada($dt);
		echo json_encode($data);
	}

	public function estatusFechaPenalizada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaPenalizada($dt);
		echo json_encode($data);
	}

	public function fechaMinima(){
		$data['data'] = $this->DashModel->fechaMinima();
		echo json_encode($data);
	}

	public function citasAnual(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->citasAnual($dt);
		echo json_encode($data);
	}

	public function getPregunta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getPregunta($dt);
		echo json_encode($data);
	}

	public function getRespuestas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getRespuestas($dt);
		echo json_encode($data);
	}

	public function getCountRespuestas(){
		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->DashModel->getCountRespuestas($dt);
		echo json_encode($data);
	}

	public function getMetas()
    {
        $dt = $this->input->post('dataValue', true);

		$data['data'] = $this->DashModel->getMetas($dt)->result();
		echo json_encode($data);
    }
}
