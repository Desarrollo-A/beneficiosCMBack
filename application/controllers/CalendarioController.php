<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarioController extends CI_Controller{
    public function __construct(){
		parent::__construct();

        $this->load->model('calendarioModel');
		$this->load->model('generalModel');
		$this->load->library('session');
		date_default_timezone_set('America/Mexico_City');
	}
	
	public function getAllEvents(){

		$dataValue = $this->input->post("dataValue", true);
		$year = $dataValue["year"];
		$month = $dataValue["month"];
		$id_usuario = $dataValue["idUsuario"];

		$dates = [
			"month_1" => $month_1 = ($month - 1) === 0 ? 12 : ($month - 1),
        	"month_2" => $month_2 = ($month + 1) > 12 ? 1 : ($month + 1),
        	"year_1" => $year_1 =  intval($month) === 1 ? $year - 1 : $year,
        	"year_2" => $year_2 =  intval($month) === 12 ? $year + 1 : $year
		];
		
		$occupied = $this->calendarioModel->getOccupied($year, $month, $id_usuario, $dates);
		$appointment = $this->calendarioModel->getAppointment($year, $month, $id_usuario, $dates);

		if ($occupied->num_rows() > 0 || $appointment->num_rows() > 0) 
            $data["events"] = array_merge($occupied->result(), $appointment->result());
        else 
            $data["events"] = array('');

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}


	public function saveOccupied(){
		$dataValue = $this->input->post("dataValue");
		$now = date('Y/m/d H:i:s', time());

		$hora_final_resta = date('H:i:s', strtotime($dataValue["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($dataValue["hora_inicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

		$values = [
			"fechaOcupado" => $dataValue["fecha"], 
			"horaInicio" => $dataValue["hora_inicio"], 
			"horaFinal" => $dataValue["hora_final"], 
			"idEspecialista" => $dataValue["id_usuario"], 
			"creadoPor" => $dataValue["id_usuario"], 
			"fechaModificacion" => date("Y-m-d H:i:s"), 
			"fechaCreacion" => date("Y-m-d H:i:s"), 
			"titulo" => $dataValue["titulo"], 
			"idUnico" => $dataValue["id_unico"]
		];

		if($dataValue["fecha_inicio"] > $now)
			$pass = true;

		try{
			$check_occupied = $this->calendarioModel->checkOccupied($dataValue, $hora_inicio_suma ,$hora_final_resta);
			$check_appointment = $this->calendarioModel->checkAppointment($dataValue, $fecha_inicio_suma, $fecha_final_resta);
			
			if ($check_occupied->num_rows() < 1 && $check_appointment->num_rows() < 1 && isset($pass) ) {
				$addRecord = $this->generalModel->addRecord("horariosOcupados", $values);

				if ($addRecord) {
                    $response["result"] = true;
                    $response["msg"] = "Se ha guardado el horario";
                } 
                else {
                    $response["result"] = false;
                    $response["msg"] = "Error al guardar el horario";
                }
			}
			else{
				$response["result"] = false;
				$response["msg"] = "El horario ya ha sido ocupado";
			}

		}
		catch(Exception $e){
			$response["result"] = false;
            $response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateOccupied(){
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["start"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["oldStart"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');

		$hora_final_resta = date('H:i:s', strtotime($dataValue["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($dataValue["hora_inicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

		if($start < $now){
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";

			if($oldStart < $now){
				$response["result"] = false;
				$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			}

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($response));
		}
		
		try{
			$values = [
				"horaInicio" => $dataValue["hora_inicio"], 
				"horaFinal" => $dataValue["hora_final"], 
				"fechaModificacion" => date("Y-m-d H:i:s"), 
				"titulo" => $dataValue["titulo"], 
				"fechaOcupado" => $dataValue["fecha_ocupado"], 
			];
			
			$check_occupiedId = $this->calendarioModel->checkOccupiedId($dataValue, $hora_inicio_suma ,$hora_final_resta);
			$check_appointment = $this->calendarioModel->checkAppointment($dataValue, $fecha_inicio_suma, $fecha_final_resta);

			if ($check_occupiedId->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $response["result"] = false;
                $response["msg"] = "El horario ya ha sido ocupado";
            } 
			else {
				$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $dataValue["id"]);

                if ($updateRecord) {
                    $response["result"] = true;
                    $response["msg"] = "Se ha guardado el horario";
                } else {
                    $response["result"] = false;
                    $response["msg"] = "Error al guardar el horario";
                }
            }
		}
		catch(EXCEPTION $e){
			$response["result"] = false;
            $response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function deleteOccupied(){
		$id_unico = $this->input->post("dataValue", true);

		$values = [
			"estatus" => 0
		];
		
		$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $id_unico);

		if ($updateRecord) {
            $response["result"] = true;
            $response["msg"] = "Se ha eliminado el horario";
        } else {
            $response["result"] = false;
            $response["msg"] = "No se puede eliminar el horario";
        }

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	function createAppointment(){
		$dataValue = $this->input->post("dataValue", true);
		$now = date('Y/m/d H:i:s', time());

		$hora_final_resta = date('H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

		if($dataValue["fecha_inicio"] > $now)
			$pass = true;

		try{
			$values = [
				"idEspecialista" => $dataValue["id_usuario"],
            	"idPaciente" => $dataValue["id_paciente"],
            	"estatus" => 1,
            	"fechaInicio" => $dataValue["fecha_inicio"],
            	"fechaFinal" => $dataValue["fecha_final"],
            	"creadoPor" => $dataValue["creado_por"],
            	"fechaModificacion" => date("Y-m-d H:i:s"),
            	"observaciones" => $dataValue["observaciones"],
            	"modificadoPor" => $dataValue["modificado_por"]
			];
						
			$check_appointment = $this->calendarioModel->checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta);
			$check_occupied = $this->calendarioModel->checkOccupied($dataValue, $hora_inicio_suma, $hora_final_resta);

			if ($check_appointment->num_rows() > 0 || $check_occupied->num_rows() > 0 || !isset($pass)) {
                $response["result"] = false;
                $response["msg"] = "El horario ya ha sido ocupado";
            } 
			else {
				$addRecord = $this->generalModel->addRecord("citas", $values);

                if ($addRecord) {
                    $response["result"] = true;
                    $response["msg"] = "Se ha agendado a cita";
                } 
				else {
                    $response["result"] = false;
                    $response["msg"] = "No se ha guardado la cita";
                }
            }
		}
		catch(EXCEPTION $e){
			$response["result"] = false;
            $response["msg"] = "Error";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function cancelAppointment(){
		$id = $this->input->post("dataValue", true);

		$values = [
			"estatus" => 0
		];

		$updateRecord = $this->generalModel->updateRecord("citas", $values, "idCita", $id);

		if ($updateRecord) {
            $response["result"] = true;
            $response["msg"] = "Se ha cancelado la cita";
        } 
		else {
            $response["result"] = false;
            $response["msg"] = "No se puede cancelar la cita";
        }

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	function appointmentDrop(){
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["start"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["old_start"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

        $hora_final_resta = date('H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

		if($start < $now){
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";
			return;
		}
		
		if($oldStart < $now){
			$response["result"] = false;
			$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			return;
		}

		try{
			$values = [
				"fechaInicio" => $dataValue["fecha_inicio"],
				"fechaFinal" => $dataValue["fecha_final"],
				"fechaModificacion" => $now,
				"modificadoPor" => $dataValue["id_usuario"]
			];
			
			$check_occupied = $this->calendarioModel->checkOccupied($dataValue, $hora_inicio_suma ,$hora_final_resta);
			$check_appointment = $this->calendarioModel->checkAppointment($dataValue, $fecha_inicio_suma, $fecha_final_resta);

			if ($check_occupied->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $response["result"] = false;
                $response["msg"] = "El horario ya ha sido ocupado";
            } 
			else {
				$updateRecord = $this->generalModel->updateRecord("citas", $values, "idCita", $dataValue["id"]);

                if ($updateRecord) {
                    $response["result"] = true;
                    $response["msg"] = "Se ha guardado la cita";
                } else {
                    $response["result"] = false;
                    $response["msg"] = "Error al guardar la cita";
                }
            }
		}
		catch(EXCEPTION $e){
			$response["result"] = false;
            $response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	function occupiedDrop(){
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["start"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["old_start"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

        $hora_final_resta = date('H:i:s', strtotime($dataValue["fecha_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($dataValue["fecha_inicio"] . '+1 minute'));

		$hora_final = date('H:i:s', strtotime($dataValue["fecha_final"]));
        $hora_inicio = date('H:i:s', strtotime($dataValue["fecha_inicio"]));

		if($start < $now){
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";
			return;
		}
		
		if($oldStart < $now){
			$response["result"] = false;
			$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			return;
		}

		try{
			$values = [
				"fechaOcupado" => $dataValue["fecha"],
				"horaInicio" => $hora_inicio,
				"horaFinal" => $hora_final,
				"fechaModificacion" => $now
			];
			
			$check_occupied = $this->calendarioModel->checkOccupied($dataValue, $hora_inicio_suma ,$hora_final_resta);
			$check_appointment = $this->calendarioModel->checkAppointment($dataValue, $fecha_inicio_suma, $fecha_final_resta);

			if ($check_occupied->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $response["result"] = false;
                $response["msg"] = "El horario ya ha sido ocupado";
            } 
			else {
				$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $dataValue["id"]);

                if ($updateRecord) {
                    $response["result"] = true;
                    $response["msg"] = "Se ha guardado la cita";
                } else {
                    $response["result"] = false;
                    $response["msg"] = "Error al guardar la cita";
                }
            }
		}
		catch(EXCEPTION $e){
			$response["result"] = false;
            $response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
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
		$sede = $this->input->post('dataValue[sede]');
		
		$response['result'] = isset($sede);
		if ($response['result']) {
			$rs = $this->calendarioModel->getBeneficiosPorSede($sede)->result();
			$response['result'] = count($rs) > 0; 
			if ($response['result']) {
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
		$sede = $this->input->post('dataValue[sede]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($sede, $beneficio);
		if ($response['result']) {
			$rs = $this->calendarioModel->getEspecialistaPorBeneficioYSede($sede, $beneficio)->result();
			$response['result'] = count($rs) > 0; 
			if ($response['result']) {
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
		$sede = $this->input->post('dataValue[sede]');
		$especialista = $this->input->post('dataValue[especialista]');

		$response['result'] = isset($sede, $especialista);
		if ($response['result']) {
			$rs = $this->calendarioModel->getModalidadesEspecialista($sede, $especialista)->result();
			$response['result'] = count($rs) > 0; 
			if ($response['result']) {
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

	public function getAppointmentsByUser() {
		$year = $this->input->post('dataValue[year]');
		$month = $this->input->post('dataValue[month]');
		$idUsuario = $this->input->post('dataValue[idUsuario]');

		$response['result'] = isset($year, $month, $idUsuario);
		if ($response['result']) {
			$rs = $this->calendarioModel->getAppointmentsByUser($year, $month, $idUsuario)->result();
			$response['result'] = count($rs) > 0; 
			if ($response['result']) {
				$response['msg'] = '¡Listado de citas cargadas exitosamente!';
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
