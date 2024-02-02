<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class GeneralController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('generalModel');
	}

	public function usuarios(){
		$data['data'] = $this->generalModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function especialistas(){
		$data['data'] = $this->generalModel->especialistas();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

    public function usrCount(){
		$data['data'] = $this->generalModel->usrCount();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

    public function citasCount(){
		$data['data'] = $this->generalModel->citasCount();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getPuesto(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getPuesto($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getSede(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getSede($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getPacientes($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getCtAsistidas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getCtAsistidas($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getCtCanceladas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getCtCanceladas($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getCtPenalizadas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getCtPenalizadas($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getAppointmentHistory(){

        $dt = $this->input->post('dataValue', true);
		$data['data'] = $this->generalModel->getAppointmentHistory($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);

    }

	public function getEstatusPaciente(){
        $data['data'] = $this->generalModel->getEstatusPaciente()->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
    }

	public function updateEstatusPaciente(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$estatus= $this->input->post('dataValue[estatus]');
		$area= $this->input->post('dataValue[area]');

		switch($area){
			case 537:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusNut" => $estatus,
					);
							
					$response=$this->generalModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ), JSON_NUMERIC_CHECK);
								
				}else{

				echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

				}
				break;
			case 585:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusPsi" => $estatus,
					);
							
					$response=$this->generalModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ), JSON_NUMERIC_CHECK);
								
				}else{

				echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

				}
				break;
			case 158:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusQB" => $estatus,
					);
							
					$response=$this->generalModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ), JSON_NUMERIC_CHECK);
								
				}else{

				echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

				}
				break;
			case 686:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusGE" => $estatus,
					);
							
					$response=$this->generalModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ), JSON_NUMERIC_CHECK);
								
				}else{

				echo json_encode(array("estatus" => false), JSON_NUMERIC_CHECK);

				}
				break;
		}			
	}

	public function getAtencionXsede(){
		$data['data'] = $this->generalModel->getAtencionXsede()->result();
		echo json_encode($data);
	}

	public function getSedes(){
		$data['data'] = $this->generalModel->getSedes()->result();
		echo json_encode($data);
	}

	public function getOficinas(){
		$data['data'] = $this->generalModel->getOficinas()->result();
		echo json_encode($data);
	}

	public function getModalidades(){
		$data['data'] = $this->generalModel->getModalidades()->result();
		echo json_encode($data);
	}

	public function getSinAsigSede(){
		$data['data'] = $this->generalModel->getSinAsigSede();
		echo json_encode($data);
	}
}
