<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class EncuestasController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
		$this->load->database('default');
		$this->load->model('EncuestasModel');
		$this->load->model('GeneralModel');
		$this->load->library('email');
	}

	public function encuestaInsert(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->EncuestasModel->encuestaInsert($dt);
	}

	public function getRespuestas(){
		$data['data'] = $this->EncuestasModel->getRespuestas()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function encuestaCreate(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->EncuestasModel->encuestaCreate($dt);
	}

	public function encuestaMinima(){
		$data['data'] = $this->EncuestasModel->encuestaMinima()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEncuesta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->EncuestasModel->getEncuesta($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getResp1(){
		$data['data'] = $this->EncuestasModel->getResp1()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getResp2(){
		$data['data'] = $this->EncuestasModel->getResp2()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getResp3(){
		$data['data'] = $this->EncuestasModel->getResp3()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getResp4(){
		$data['data'] = $this->EncuestasModel->getResp4()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEncNotificacion(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->EncuestasModel->getEncNotificacion($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getPuestos(){
		$data['data'] = $this->EncuestasModel->getPuestos()->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEncuestasCreadas(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->EncuestasModel->getEncuestasCreadas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function updateEstatus(){

		$idEncuesta= $this->input->post('dataValue[idEncuesta]');
		$estatus= $this->input->post('dataValue[estatus]');
		$area= $this->input->post('dataValue[area]');

		$query_idEncuesta = $this->ch->query("SELECT * 
		FROM ". $this->schema_cm .".encuestascreadas WHERE idArea = $area AND estatus = 1");

        $idEnc = 0;
        foreach ($query_idEncuesta->result() as $row) {
            $idEnc = $row->idEncuesta;
        }

		$data_1 = array(
			"estatus" => 0,
		);

		$this->GeneralModel->updateRecord($this->schema_cm .'.encuestascreadas', $data_1, 'idEncuesta', $idEnc);

		$data_2 = array(
			"estatus" => $estatus
		);

		$this->GeneralModel->updateRecord($this->schema_cm .'.encuestascreadas', $data_2, 'idEncuesta', $idEncuesta);
		$this->ch->trans_complete();
				
		if ($this->ch->trans_status() === FALSE) {
			echo json_encode(array("estatus" => false, "msj" => "Error en actualizar el estatus"), JSON_NUMERIC_CHECK);
		} else {
			echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado!"), JSON_NUMERIC_CHECK);
		}
	}

	public function updateVigencia(){

		$idEncuesta= $this->input->post('dataValue[idEncuesta]');
		$vigencia= $this->input->post('dataValue[vigencia]');

		$data = array(
			"diasVigencia" => $vigencia
		);

		$this->GeneralModel->updateRecord($this->schema_cm .'.encuestasCreadas', $data, 'idEncuesta', $idEncuesta);
		echo json_encode(array("estatus" => true, "msj" => "Dato Actualizado!" ), JSON_NUMERIC_CHECK);
				
	}

	public function getEstatusUno(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->EncuestasModel->getEstatusUno($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function sendMail() {

		date_default_timezone_set('America/Mexico_City');

		$hoy = new DateTime();

		$actual = $hoy->format('Y-m-d');

		$trimActual = new DateTime($hoy->format('Y-m-01'));

		$trimPasado = clone $trimActual;
		$trimPasado->modify('-3 months');

		$trimProximo = clone $trimActual;
		$trimProximo->modify('+3 months');

		function primerLunes($fecha) {
			$diaSemana = $fecha->format('N'); 
			$diasLunes = (8 - $diaSemana) % 7;
			return $fecha->modify("+$diasLunes days");
		}

		$lunesPasado = primerLunes($trimPasado)->format('Y-m-d');
		$lunesActual = primerLunes($trimActual)->format('Y-m-d');
		$lunesProximo = primerLunes($trimProximo)->format('Y-m-d');

		$query = $this->db->query("SELECT * FROM cronConsulta WHERE proximaEjecucion = '$actual'");

		if ($query->num_rows() > 0) {

			$config['protocol']  = 'smtp';
			$config['smtp_host'] = 'smtp.gmail.com';
			$config['smtp_user'] = 'no-reply@ciudadmaderas.com';
			$config['smtp_pass'] = 'JDe64%8q5D';
			$config['smtp_port'] = 465;
			$config['charset']   = 'utf-8';
			$config['mailtype']  = 'html';
			$config['newline']   = "\r\n"; 

			$data = array(
				"proximaEjecucion" => $lunesProximo
			);
		
			$response = $this->GeneralModel->updateRecord($this->schema_cm .'.cronConsulta', $data, 'idCronConsulta', 1);

			$query = $this->db->query("SELECT DISTINCT ct.idPaciente, co.correo, ec.idEncuesta FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios co ON co.idUsuario = ct.idPaciente
			INNER JOIN encuestasCreadas ec ON ec.idArea = us.idPuesto
			WHERE estatusCita = 4 AND fechaFinal BETWEEN '$lunesPasado' AND '$lunesActual' AND ec.estatus = 1");

			$correo = '';
			$encuesta = '';
			foreach ($query->result() as $row) {
				$correo = $row->correo;
				$encuesta = $row->idEncuesta;

				$data["data"] = $encuesta;
			
				$html_message = $this->load->view("email-encuestas", $data, true);
					
					$this->load->library("email");
					$this->email->initialize($config);
					$this->email->from("no-reply@ciudadmaderas.com");
					$this->email->to($correo);
					$this->email->message($html_message);
					$this->email->subject("Encuesta Beneficios CM");

					$max_attempts = 2;

					for ($attempt = 1; $attempt <= $max_attempts; $attempt++) {
						if ($this->email->send()) {
							break; 
						} else {
							sleep(2);
						}
					}

					if ($attempt > $max_attempts) {

						echo "Intento $attempt: " . $this->email->print_debugger();
						
						$errorEnc = 'Correo: '.$correo.' ID encuesta: '.$encuesta.'';

						$queryLogsCron = $this->db->query("INSERT INTO logsCron (idProceso, fechaInicio, error) 
							VALUES (?, GETDATE(), ?)", 
							array(1, $errorEnc));
					}

			}
		
		}
	}

	public function getValidEncContestada(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->EncuestasModel->getValidEncContestada($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
}
