<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class ReportesController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		$this->load->model('reportesModel');
		$this->load->model('especialistasModel');
		$this->load->model('generalModel');
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function citas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->reportesModel->citas($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function especialistas(){
		$data['data'] = $this->especialistasModel->especialistas()->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function observacion(){

		$idCita= $this->input->post('dataValue[idCita]');
		$descripcion= $this->input->post('dataValue[descripcion]');
		$estatus= $this->input->post('dataValue[ests]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		if( !empty($idCita) && !empty($descripcion) )
		{
			$data = array(
				"observaciones" => $descripcion,
				"estatus" => $estatus,
				"modificadoPor" => $modificadoPor,
			);
			
			$response=$this->generalModel->updateRecord('citas', $data, 'idCita', $idCita);
			echo json_encode(array("estatus" => true, "msj" => "Observación Registrada!" ), JSON_NUMERIC_CHECK);
				
		}else{

			echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

		}			
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->reportesModel->getPacientes($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
}
