<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class GestorController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('GestorModel');
		$this->load->model('GeneralModel');

	}

    public function getOficinasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getOficinasVal($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEspecialistasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getEspecialistasVal($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getSedeNone(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getSedeNone($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getSedeNoneEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getSedeNoneEsp($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function insertAtxSede(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertAtxSede($dt);
	}

	public function updateModalidad(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$modalidad= $this->input->post('dataValue[modalidad]');

		$data = array(
			"tipoCita" => $modalidad,
		);

		$response=$this->GeneralModel->updateRecord('atencionXSede', $data, 'idAtencionXSede', $id);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function updateEspecialista(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$idEspe= $this->input->post('dataValue[espe]');

		$data = array(
			"idEspecialista" => $idEspe,
		);

		$response=$this->GeneralModel->updateRecord('atencionXSede', $data, 'idAtencionXSede', $id);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function updateOficina(){

		$idOficina= $this->input->post('dataValue[idOfi]');
		$oficina= $this->input->post('dataValue[ofi]');
		$idSede= $this->input->post('dataValue[idSede]');
		$ubicacion= $this->input->post('dataValue[ubi]');
		$estatus= $this->input->post('dataValue[estatus]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		$data = array(
			"oficina" => $oficina,
			"idSede" => $idSede,	
			"ubicación" => $ubicacion,
			"estatus" => $estatus,
			"modificadoPor" => $modificadoPor,
		);

		$response=$this->GeneralModel->updateRecord('oficinas', $data, 'idOficina', $idOficina);
		echo json_encode(array("estatus" => true, "msj" => "Datos Actualizados!" ));
				
	}

	public function updateSede(){

		$idSede= $this->input->post('dataValue[idSed]');
		$sede= $this->input->post('dataValue[sede]');
		$abreviacion= $this->input->post('dataValue[abreviacion]');
		$estatus= $this->input->post('dataValue[estatus]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		$data = array(
			"sede" => $sede,
			"abreviacion" => $abreviacion,
			"estatus" => $estatus,
			"modificadoPor" => $modificadoPor,
		);

		$response=$this->GeneralModel->updateRecord('sedes', $data, 'idSede', $idSede);
		echo json_encode(array("estatus" => true, "msj" => "Datos Actualizados!" ));
				
	}

	public function getEsp(){
		$areas = $this->input->get('areas');
		$data = $this->GestorModel->getEsp($areas);
		// $this->output->set_content_type('application/json');
        // $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));

		$this->json($data);
	}

	public function getAtencionXsedeEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getAtencionXsedeEsp($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getOficinas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getOficinas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function insertOficinas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertOficinas($dt);
	}

	public function insertSedes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertSedes($dt);
	}

}