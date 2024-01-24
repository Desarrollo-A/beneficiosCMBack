<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class encuestasController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('encuestasModel');
		$this->load->model('generalModel');

		$this->load->library('email');

		parent::__construct();
	}

	public function encuestaInsert(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->encuestaInsert($dt);
	}

	public function getRespuestas(){
		$data['data'] = $this->encuestasModel->getRespuestas()->result();
		echo json_encode($data);
	}

	public function encuestaCreate(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->encuestaCreate($dt);
	}

	public function encuestaMinima(){
		$data['data'] = $this->encuestasModel->encuestaMinima()->result();
		echo json_encode($data);
	}

	public function getEncuesta(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->encuestasModel->getEncuesta($dt)->result();
		echo json_encode($data);
	}

	public function getResp1(){
		$data['data'] = $this->encuestasModel->getResp1()->result();
		echo json_encode($data);
	}

	public function getResp2(){
		$data['data'] = $this->encuestasModel->getResp2()->result();
		echo json_encode($data);
	}

	public function getResp3(){
		$data['data'] = $this->encuestasModel->getResp3()->result();
		echo json_encode($data);
	}

	public function getResp4(){
		$data['data'] = $this->encuestasModel->getResp4()->result();
		echo json_encode($data);
	}

	public function getEncNotificacion(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEncNotificacion($dt);
		echo json_encode($data);
	}

	public function getEcuestaValidacion(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEcuestaValidacion($dt);
		echo json_encode($data);
	}

	public function getPuestos(){
		$data['data'] = $this->encuestasModel->getPuestos()->result();
		echo json_encode($data);
	}

	public function getEncuestasCreadas(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEncuestasCreadas($dt)->result();
		echo json_encode($data);
	}

	public function updateEstatus(){

		$idEncuesta= $this->input->post('dataValue[idEncuesta]');
		$estatus= $this->input->post('dataValue[estatus]');
		$vigencia= $this->input->post('dataValue[vigencia]');
		$area= $this->input->post('dataValue[area]');

		$query_idEncuesta = $this->db->query("SELECT * FROM encuestasCreadas WHERE idArea = $area AND estatus = 1");

        $idEnc = 0;
        foreach ($query_idEncuesta->result() as $row) {
            $idEnc = $row->idEncuesta;
        }

		$data_1 = array(
			"estatus" => 0,
		);

		$response_1=$this->generalModel->updateRecord('encuestasCreadas', $data_1, 'idEncuesta', $idEnc);

		$data_2 = array(
			"estatus" => $estatus,
			"diasVigencia" => $vigencia
		);

		$response_2=$this->generalModel->updateRecord('encuestasCreadas', $data_2, 'idEncuesta', $idEncuesta);
		echo json_encode(array("estatus" => true, "msj" => "Estatus Actualizado!" ));
				
	}

	public function updateVigencia(){

		$idEncuesta= $this->input->post('dataValue[idEncuesta]');
		$vigencia= $this->input->post('dataValue[vigencia]');

		$data = array(
			"diasVigencia" => $vigencia
		);

		$response=$this->generalModel->updateRecord('encuestasCreadas', $data, 'idEncuesta', $idEncuesta);
		echo json_encode(array("estatus" => true, "msj" => "Dato Actualizado!" ));
				
	}

	public function getEstatusUno(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->encuestasModel->getEstatusUno($dt)->result();
		echo json_encode($data);
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
		
			$response = $this->generalModel->updateRecord('cronConsulta', $data, 'idCronConsulta', 1);

			$query = $this->db->query("SELECT DISTINCT ct.idPaciente, co.correo, ec.idEncuesta FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios co ON co.idUsuario = ct.idPaciente
			INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
			WHERE estatusCita = 4 AND fechaFinal BETWEEN '$lunesPasado' AND '$lunesActual' AND ec.estatus = 1");

			$correo = '';
			$encuesta = '';
			foreach ($query->result() as $row) {
				$correo = $row->correo;
				$encuesta = $row->idEncuesta;
			
					$html_message = '<html>
						<head>
						<link href="'.base_url().'dist/css/email.css" rel="stylesheet" />
						</head>
						<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
						<center style="width: 100%; background-color: #f1f1f1;">
						<div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
						&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
						</div>
						<div style="max-width: 600px; margin: 0 auto;" class="email-container">
							<!-- BEGIN BODY -->
						<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
							<tr>
							<td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">
								<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="logo" style="text-align: center;">
											<h1>Beneficios CM</h1>
										</td>
									</tr>
								</table>
							</td>
							</tr><!-- end tr -->
							<tr>
							</tr><!-- end tr -->
									<tr>
							<td valign="middle" class="hero bg_white" >
								<table>
									<tr>
										<td>
											<div class="text" style="padding: 1em 2.5em 0 2.5em;">
												<h2 text-align: center;" style="text-align: center;">Tienes una encuesta disponible</h2>
												<h3 text-align: center;" style="text-align: center;">Haz click en el boton para contestarla</h3>
												<p text-align: center;" style="text-align: center;"><a href="#" >Encuesta</a></p>
											</div>
										</td>
									</tr>
								</table>
							</td>
							</tr><!-- end tr -->
						<!-- 1 Column Text + Button : END -->
						</table>
					
						</div>
					</center>
					</body>';
					
					$this->load->library("email");
					$this->email->initialize($config);
					$this->email->from("no-reply@ciudadmaderas.com");
					$this->email->to("programador.analista32@ciudadmaderas.com");
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

		$data['data'] = $this->encuestasModel->getValidEncContestada($dt);
		echo json_encode($data);
	}
}
