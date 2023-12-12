<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarioController extends CI_Controller{
    public function __construct(){
		parent::__construct();
        $this->load->model('calendarioModel');
		$this->load->library('session');
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
		date_default_timezone_set('America/Mexico_City');

		$fecha = $this->input->post("fecha", true);
		$hora_inicio = $this->input->post("hora_inicio", true);
		$hora_final = $this->input->post("hora_final", true);
		$id_especialista = $this->input->post("id_usuario", true);
		$creado_por = $this->input->post("id_usuario", true);
		$fecha_modificacion = date("Y-m-d H:i:s");
		$fecha_creacion = date("Y-m-d H:i:s");
		$titulo = $this->input->post("titulo", true);
		$id_unico = $this->input->post('id_unico', true);

		$save = $this->calendarioModel->saveOccupied(
			$fecha, 
			$hora_inicio, 
			$hora_final, 
			$id_especialista, 
			$creado_por, 
			$fecha_modificacion, 
			$fecha_creacion, 
			$titulo, 
			$id_unico
		);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($save));
	}

	public function update_occupied(){
		date_default_timezone_set('America/Mexico_City');

		$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
		$request = json_decode($stream_clean);

		$hora_inicio = $request->hora_inicio;
		$hora_final = $request->hora_final;
		$fecha_modificacion = date("Y-m-d H:i:s");
		$titulo = $request->titulo;
		$id_unico = $request->id_unico;
		$fecha_ocupado = $request->fechaOcupado;

		$save = $this->calendarioModel->updateOccupied(
			$hora_inicio, 
			$hora_final,
			$fecha_modificacion, 
			$titulo, 
			$id_unico,
			$fecha_ocupado
		);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($save));
	}

	public function delete_occupied(){
		$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
		$request = json_decode($stream_clean);

		$id_unico = $request->id_unico;

		$delete = $this->calendarioModel->deleteOccupied($id_unico);

		if($delete["status"]){
			$data["status"] = true;
			$data["message"] = "Se ha eliminado el horario";
		}
		else{
			$data["status"] = false;
			$data["message"] = "No se puede eliminar el horario";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function delete_date(){
		$id = $this->input->post("id");
		$delete = $this->calendarioModel->deleteDate($id);

		if($delete["status"]){
			$data["status"] = true;
			$data["message"] = "Se ha cancelado la cita";
		}
		else{
			$data["status"] = false;
			$data["message"] = "No se ha cancelado la cita";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($data));
	}

	function create_appointment(){
		$idEspecialista = $this->input->post("idEspecialista", true);
		$idPaciente = $this->input->post("idPaciente", true);
		$fechaInicio = $this->input->post("fechaInicio", true);
		$fechaFinal = $this->input->post("fechaFinal", true);
		$creadoPor = $this->input->post("creadoPor", true);
		$fechaModificacion = date("Y-m-d H:i:s");
		$observaciones = $this->input->post("observaciones", true);
		$modificadoPor = $this->input->post("idEspecialista", true);

		$create = $this->calendarioModel->createAppointment(
			$idEspecialista, 
			$idPaciente, 
			$fechaInicio, 
			$fechaFinal, 
			$creadoPor, 
			$fechaModificacion, 
			$observaciones,
			$modificadoPor	
		);

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($create));
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
