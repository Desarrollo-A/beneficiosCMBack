<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class DashboardController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		//$this->load->database('default');
		//$this->ch = $this->load->database('ch', TRUE);

		$this->load->model('UsuariosModel');
		$this->load->model('ReportesModel');
		$this->load->model('EspecialistasModel');
		$this->load->model('DashModel');
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}
	public function index()
	{
		$this->usuarios();
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function citas(){
		$data['data'] = $this->ReportesModel->citas();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function especialistas(){
		$data['data'] = $this->EspecialistasModel->especialistas();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function citasCountStatus(){
		$data['data'] = $this->DashModel->citasCountStatus();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function totalStatusCitas(){
		$data['data'] = $this->DashModel->totalStatusCitas();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function estatusFechaAsistencia(){

		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaAsistencia($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function estatusFechaCancelada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaCancelada($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function estatusFechaPenalizada(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->estatusFechaPenalizada($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function fechaMinima(){
		$data['data'] = $this->DashModel->fechaMinima();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function citasAnual(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->citasAnual($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getPregunta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getPregunta($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getRespuestas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getRespuestas($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCountRespuestas(){
		$dt = $this->input->post('dataValue', true);
		
		$data['data'] = $this->DashModel->getCountRespuestas($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getMetas()
    {
        $dt = $this->input->post('dataValue', true);

		$data['data'] = $this->DashModel->getMetas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

	public function getMetaAdmin(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->EspecialistasModel->getMetaAdmin($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEsp(){
		$areas = $this->input->get('areas');
		$data = $this->DashModel->getEsp($areas);
		$this->json($data);
	}

	public function getCtDisponibles(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCtDisponibles($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCtAsistidas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCtAsistidas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCtCanceladas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCtCanceladas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCtPenalizadas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCtPenalizadas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCarrusel(){

		$data['data'] = $this->DashModel->getCarrusel()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getPacientes($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
	
	public function getCountModalidades(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCountModalidades($dt)->result();
		
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCountEstatusCitas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCountEstatusCitas($dt)->result();
		
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getCountPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->DashModel->getCountPacientes($dt)->result();
		
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
}
