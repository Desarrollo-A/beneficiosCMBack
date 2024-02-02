<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class gestorController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('gestorModel');
		$this->load->model('generalModel');

		parent::__construct();
	}

    public function getOficinasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getOficinasVal($dt);
		echo json_encode($data);
	}

	public function getEspecialistasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getEspecialistasVal($dt);
		echo json_encode($data);
	}

	public function getSedeNone(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getSedeNone($dt);
		echo json_encode($data);
	}

	public function getSedeNoneEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getSedeNoneEsp($dt);
		echo json_encode($data);
	}

	public function insertAtxSede(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->insertAtxSede($dt);
	}

	public function updateModalidad(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$modalidad= $this->input->post('dataValue[modalidad]');

		$data = array(
			"tipoCita" => $modalidad,
		);

		$response=$this->generalModel->updateRecord('atencionXSede', $data, 'idAtencionXSede', $id);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function updateEspecialista(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$idEspe= $this->input->post('dataValue[espe]');

		$data = array(
			"idEspecialista" => $idEspe,
		);

		$response=$this->generalModel->updateRecord('atencionXSede', $data, 'idAtencionXSede', $id);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function getEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getEsp($dt);
		echo json_encode($data);
	}

	public function getAtencionXsedeEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->gestorModel->getAtencionXsedeEsp($dt);
		echo json_encode($data);
	}

}