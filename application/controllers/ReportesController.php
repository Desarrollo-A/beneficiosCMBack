<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class ReportesController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->ch = $this->load->database('ch', TRUE);
		$this->load->database('default');
		$this->load->model('UsuariosModel');
		$this->load->model('ReportesModel');
		$this->load->model('EspecialistasModel');
		$this->load->model('GeneralModel');
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function citas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->citas($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function especialistas(){
		$data['data'] = $this->EspecialistasModel->especialistas()->result();
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
				"estatusCita" => $estatus,
				"modificadoPor" => $modificadoPor,
			);
			
			$response=$this->GeneralModel->updateRecord($this->schema_cm .'.PRUEBA_beneficiosCM.citas', $data, 'idCita', $idCita);
			echo json_encode(array("estatus" => true, "msj" => "Observación Registrada!" ), JSON_NUMERIC_CHECK);
				
		}else{

			echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

		}			
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getPacientes($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getResumenTerapias(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getResumenTerapias($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getCierrePacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getCierrePacientes($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getCierreIngresos(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getCierreIngresos($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getSelectEspe(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getSelectEspe($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getEspeUser(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getEspeUser($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	
	public function getAppointmentHistory(){

        $dt = $this->input->post('dataValue', true);
		$data['data'] = $this->ReportesModel->getAppointmentHistory($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));

    }
}
