<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarioController extends CI_Controller{
    public function __construct(){
		parent::__construct();

        $this->load->model('calendarioModel');
		$this->load->library('session');
		date_default_timezone_set('America/Mexico_City');
	}

	public function get_occupied(){
		$year = $this->input->post("year");
		$month = $this->input->post("month");
		$id_usuario = $this->input->post("idUsuario");

		$data = $this->calendarioModel->getOccupied($year, $month, $id_usuario);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function save_occupied(){
		$data = $this->input->post("data");

		$save = $this->calendarioModel->saveOccupied($data);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($save));
	}

	public function update_occupied(){
		$data = $this->input->post("data", true);

		$save = $this->calendarioModel->updateOccupied($data);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($save));
	}

	public function delete_occupied(){
		$id_unico = $this->input->post("data", true);
		
		$delete = $this->calendarioModel->deleteOccupied($id_unico);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($delete));
	}

	public function delete_date(){
		$id = $this->input->post("data", true);
		$delete = $this->calendarioModel->deleteDate($id);

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($delete));
	}

	function create_appointment(){
		$data = $this->input->post("data", true);

		$create = $this->calendarioModel->createAppointment( $data );

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($create));
	}

	function update_on_drop(){
		$data = $this->input->post("data", true);
		$oldStart = $data["oldStart"];
		$start = $data["start"];
		// Obtén la zona horaria actual
$currentTimeZone = new DateTimeZone('America/Mexico_City');

// Crea un objeto DateTime con la zona horaria actual
$currentDateTime = new DateTime('now', $currentTimeZone);

// Imprime el offset de la zona horaria actual
$offset = $currentDateTime->format('Y/m/d');


		
		if($oldStart > $offset){
			if($start > $offset){
				if($data["tipo"] === "cita"){
					$update = $this->calendarioModel->onDropAppointment($data);
				}
				else{
					$update = $this->calendarioModel->onDropOccupied($data);
				}
			}
			else{
				$update["status"] = false;
				$update["message"] = "No se pueden mover las fechas a un dia anterior o actual";
			}
		  }
		  else{
			$update["status"] = false;
			$update["message"] = "Las citas u horarios pasados no se pueden mover";
		  } 

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($update));
	}

	function getBeneficiosDisponibles(){
		$datosEmpleado = json_decode(file_get_contents('php://input'));
		print_r($datosEmpleado);
		echo '<br><br>';
		print_r($this->session->userdata());

		exit;
    	$dataButton = $this->calendarioModel->revisaCitas();
		$data['beneficios'] = $this->calendarioModel->getBeneficiosDisponibles();
		print_r(json_encode($data));
	}

	public function getBeneficiosPorSede(){
		$sede = $this->input->post('sede');
		
		$response['result'] = isset($sede);
		if ($response['result']) {
			$rs = $this->calendarioModel->getBeneficiosPorSede($sede);
			$data['result'] = count($rs) > 0; 
			if ($data['result']) {
				$response['msg'] = '¡Listado de beneficios cargado exitosamente!';
				$response['data'] = $rs; 
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parametros invalidos!";
		}
		
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}

	public function getEspecialistaPorBeneficioYSede(){
		$sede = $this->input->post('sede');
		$beneficio = $this->input->post('beneficio');

		$response['result'] = isset($sede, $beneficio);
		if ($response['result']) {
			$rs = $this->calendarioModel->getEspecialistaPorBeneficioYSede($sede, $beneficio);
			$data['result'] = count($rs) > 0; 
			if ($data['result']) {
				$response['msg'] = '¡Listado de especialistas cargado exitosamente!';
				$response['data'] = $rs; 
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
            $response['msg'] = "¡Parametros invalidos!";
        }

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}

	public function getModalidadesEspecialista(){
		$sede = $this->input->post('sede');
		$especialista = $this->input->post('especialista');

		$response['result'] = isset($sede, $especialista);
		if ($response['result']) {
			$rs = $this->calendarioModel->getModalidadesEspecialista($sede, $especialista);
			$data['result'] = count($rs) > 0; 
			if ($data['result']) {
				$response['msg'] = '¡Listado de modalidades cargado exitosamente!';
				$response['data'] = $rs; 
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
            $response['msg'] = "¡Parametros invalidos!";
        }

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}
}
