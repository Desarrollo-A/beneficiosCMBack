<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class encuestasController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('encuestasModel');
		$this->load->model('generalModel');

		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');

		date_default_timezone_set('America/Mexico_City');;
	}

	public function encuestaInsert(){

		$data = $this->input->post("dat");

		$items = json_decode($data, true);

		$datosValidos = true;

		if (isset($items)) {

			foreach ($items as $item) {
				if (!isset($item['pregunta'], $item['resp']) || empty($item['pregunta']) 
				|| is_null($item['resp']) || empty($item['resp']) || is_null($item['pregunta'])) {
					echo json_encode(array("estatus" => -5, "mensaje" => "Hay preguntas sin contestar!" ));
					$datosValidos = false;
					break; 
				}
			}

			if ($datosValidos) {

				foreach ($items as $item) {
					$pregunta = $item["pregunta"];
					$resp = $item["resp"];

					$query = $this->db->query("INSERT INTO encuestasContestadas (pregunta, respuesta, idEspecialista, fechaCreacion) VALUES (?, ?, 1, GETDATE() )", array($pregunta, $resp ));
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => 200, "mensaje" => "Encuesta Creada Correctamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => -5, "mensaje" => "Error Faltan Datos" ));
		}
	}

	public function getRespuestas(){
		$data['data'] = $this->encuestasModel->getRespuestas();
		echo json_encode($data);
	}

	public function encuestaCreate(){

		$data = $this->input->post("dat");
		
		$dataArray = json_decode($data, true);

		$datosValidos = true;

		if (isset($dataArray['area']) && isset($dataArray['items'])) {
			$area = $dataArray["area"];
			$items = $dataArray["items"];

			if (empty($area)) {
				echo json_encode(array("estatus" => -5, "mensaje" => "Error Hay Campos Vacios" ));
				$datosValidos = false;
			}

			foreach ($items as $item) {
				if (!isset($item['pregunta'], $item['respuesta']) || empty($item['pregunta']) 
				|| is_null($item['respuesta']) || empty($item['respuesta']) || is_null($item['pregunta'])) {
					echo json_encode(array("estatus" => -5, "mensaje" => "Error Hay campos vacios!" ));
					$datosValidos = false;
					break; 
				}
			}

			if ($datosValidos) {
				

				foreach ($items as $item) {
					$pregunta = $item["pregunta"];
					$respuesta = $item["respuesta"];
					$idEncuesta = $item["idEncuesta"];

					$query = $this->db->query("INSERT INTO encuestasCreadas (pregunta, respuestas, idArea, estatus, fechaCreacion, idEncuesta) VALUES (?, ?, ?, 1, GETDATE(), ?)", array($pregunta, $respuesta, $area, $idEncuesta));
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => 200, "mensaje" => "Encuesta Creada Correctamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => -5, "mensaje" => "Error Faltan Datos" ));
		}


	}

	public function encuestaMinima(){
		$data['data'] = $this->encuestasModel->encuestaMinima();
		echo json_encode($data);
	}

	public function getEncuesta(){
		$dt = $this->input->post('dataValue', true);
		/* var_dump(); */
		$data['data'] = $this->encuestasModel->getEncuesta($dt);
		echo json_encode($data);
	}

	public function getResp1(){
		$data['data'] = $this->encuestasModel->getResp1();
		echo json_encode($data);
	}

	public function getResp2(){
		$data['data'] = $this->encuestasModel->getResp2();
		echo json_encode($data);
	}

	public function getResp3(){
		$data['data'] = $this->encuestasModel->getResp3();
		echo json_encode($data);
	}

	public function getResp4(){
		$data['data'] = $this->encuestasModel->getResp4();
		echo json_encode($data);
	}



}
