<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class GeneralController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('GeneralModel');
	}

	public function usuarios(){
		$data['data'] = $this->GeneralModel->usuarios();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->GeneralModel->especialistas();
		echo json_encode($data);
	}

    public function usrCount(){
		$data['data'] = $this->GeneralModel->usrCount();
		echo json_encode($data);
	}

    public function citasCount(){
		$data['data'] = $this->GeneralModel->citasCount();
		echo json_encode($data);
	}

	public function getPuesto(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getPuesto($dt)->result();
		echo json_encode($data);
	}

	public function getSede(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getSede($dt)->result();
		echo json_encode($data);
	}

	public function getPacientes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getPacientes($dt)->result();
		echo json_encode($data);
	}

	public function getCtAsistidas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getCtAsistidas($dt)->result();
		echo json_encode($data);
	}

	public function getCtCanceladas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getCtCanceladas($dt)->result();
		echo json_encode($data);
	}

	public function getCtPenalizadas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getCtPenalizadas($dt)->result();
		echo json_encode($data);
	}

	public function getAppointmentHistory(){

        $dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GeneralModel->getAppointmentHistory($dt)->result();
		echo json_encode($data);

    }

	public function getEstatusPaciente(){
        $data['data'] = $this->GeneralModel->getEstatusPaciente()->result();
		echo json_encode($data);
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
							
					$response=$this->GeneralModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ));
								
				}else{

				echo json_encode(array("estatus" => false));

				}
				break;
			case 585:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusPsi" => $estatus,
					);
							
					$response=$this->GeneralModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ));
								
				}else{

				echo json_encode(array("estatus" => false));

				}
				break;
			case 158:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusQB" => $estatus,
					);
							
					$response=$this->GeneralModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ));
								
				}else{

				echo json_encode(array("estatus" => false));

				}
				break;
			case 686:
				if( !empty($id) && !empty($estatus) )
				{
					$data = array(
						"estatusGE" => $estatus,
					);
							
					$response=$this->GeneralModel->updateRecord('detallePaciente', $data, 'idDetallePaciente', $id);
					echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!" ));
								
				}else{

				echo json_encode(array("estatus" => false));

				}
				break;
		}			
	}
}
