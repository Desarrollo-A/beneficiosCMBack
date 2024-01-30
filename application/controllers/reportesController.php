<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class ReportesController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('UsuariosModel');
		$this->load->model('ReportesModel');
		$this->load->model('EspecialistasModel');
		$this->load->model('GeneralModel');
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data);
	}

	public function citas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->citas($dt)->result();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->EspecialistasModel->especialistas()->result();
		echo json_encode($data);
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
			
			$response=$this->GeneralModel->updateRecord('citas', $data, 'idCita', $idCita);
			echo json_encode(array("estatus" => true, "msj" => "Observación Registrada!" ));
				
		}else{

			echo json_encode(array("estatus" => false));

		}			
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getPacientes($dt)->result();
		echo json_encode($data);
	}
}
