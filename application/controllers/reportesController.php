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
		$this->load->model('generalModel');

		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type,Origin, authorization, X-API-KEY,X-Requested-With,Accept,Access-Control-Request-Method');
        header('Access-Control-Allow-Method: GET, POST, PUT, DELETE,OPTION');
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}

	public function citas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->reportesModel->citas($dt)->result();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->especialistasModel->especialistas()->result();
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
			
			$response=$this->generalModel->updateRecord('citas', $data, 'idCita', $idCita);
			echo json_encode(array("estatus" => true, "msj" => "Observación Registrada!" ));
				
		}else{

			echo json_encode(array("estatus" => false));

		}			
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->reportesModel->getPacientes($dt)->result();
		echo json_encode($data);
	}
}
