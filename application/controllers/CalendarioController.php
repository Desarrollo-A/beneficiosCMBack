<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class CalendarioController extends BaseController{
    public function __construct(){
		parent::__construct();

		$this->load->model('calendarioModel');
		$this->load->model('generalModel');
		$this->load->model('usuariosModel');
		$this->load->library('session');
	}

	public function getAllEvents()
	{

		$dataValue = $this->input->post("dataValue", true);
		$year = $dataValue["year"];
		$month = $dataValue["month"];
		$idUsuario = $dataValue["idUsuario"];

		$dates = [
			"month1" => $month1 = ($month - 1) === 0 ? 12 : ($month - 1),
			"month2" => $month2 = ($month + 1) > 12 ? 1 : ($month + 1),
			"year1" => $year1 =  intval($month) === 1 ? $year - 1 : $year,
			"year2" => $year2 =  intval($month) === 12 ? $year + 1 : $year
		];

		$occupied = $this->calendarioModel->getOccupied($year, $month, $idUsuario, $dates);
		$appointment = $this->calendarioModel->getAppointment($year, $month, $idUsuario, $dates);

		if ($occupied->num_rows() > 0 || $appointment->num_rows() > 0)
			$data["events"] = array_merge($occupied->result(), $appointment->result());
		else
			$data["events"] = array('');

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function getAllEventsWithRange()
	{
		$especialista = $this->input->post('dataValue[especialista]');
		$usuario      = $this->input->post('dataValue[usuario]');
		$fechaInicio  = $this->input->post('dataValue[fechaInicio]');
		$fechaFin     = $this->input->post('dataValue[fechaFin]');

		$response['result'] = isset($especialista, $usuario, $fechaInicio, $fechaFin);
		if ($response['result']) {
			$occupied = $this->calendarioModel->getOccupiedRange($fechaInicio, $fechaFin, $especialista);
			$appointment = $this->calendarioModel->getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario);

			$response['result'] = $occupied->num_rows() > 0 || $appointment->num_rows() > 0;
			if ($response['result']) {
				$response['msg'] = '¡Eventos cargados exitosamente!';
				$response['data'] = array_merge($occupied->result(), $appointment->result());
			} else {
				$response['msg'] = '¡No existen eventos!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function getHorarioBeneficio()
	{
		$beneficio = $this->input->post('dataValue[beneficio]');

		$rs = $this->calendarioModel->getHorarioBeneficio($beneficio)->result();
		$response['result'] = count($rs) > 0;
		if ($response['result']) {
			$response['msg'] = '¡Horario cargado exitosamente!';
			$response['data'] = $rs;
		} else {
			$response['msg'] = '¡No existen horario!';
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function saveOccupied()
	{
		$dataValue = $this->input->post("dataValue");
		$now = date('Y/m/d H:i:s', time());

		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));
		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));

		$values = [
			"idEspecialista" => $dataValue["idUsuario"],
			"fechaInicio" => $dataValue["fechaInicio"],
			"fechaFinal" => $dataValue["fechaFinal"],
			"creadoPor" => $dataValue["idUsuario"],
			"modificadoPor" => $dataValue["modificadoPor"],
			"fechaModificacion" => date("Y-m-d H:i:s"),
			"fechaCreacion" => date("Y-m-d H:i:s"),
			"titulo" => $dataValue["titulo"],
			"idUnico" => $dataValue["idUnico"]
		];

		if ($dataValue["fechaInicio"] > $now)
			$pass = true;

		try {
			$checkOccupied = $this->calendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->calendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() < 1 && $checkAppointment->num_rows() < 1 && isset($pass)) {
				$addRecord = $this->generalModel->addRecord("horariosOcupados", $values);

				if ($addRecord) {
					$response["result"] = true;
					$response["msg"] = "Se ha guardado el horario";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al guardar el horario";
				}
			} else {
				$response["result"] = false;

				if ($check_appointment->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			}
		} catch (Exception $e) {
			$response["result"] = false;
			$response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateOccupied()
	{
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["fechaInicio"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["oldStart"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');

		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));
		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));

		if ($start < $now) {
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";

			if ($oldStart < $now) {
				$response["result"] = false;
				$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			}

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($response));
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"titulo" => $dataValue["titulo"],
				"modificadoPor" => $dataValue["modificadoPor"]
			];

			$checkOccupiedId = $this->calendarioModel->checkOccupiedId($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->calendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupiedId->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;

				if ($checkAppointment->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $dataValue["id"]);

				if ($updateRecord) {
					$response["result"] = true;
					$response["msg"] = "Horario actualizado";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al guardar";
				}
			}
		} catch (EXCEPTION $e) {
			$response["result"] = false;
			$response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function deleteOccupied()
	{
		$dataValue = $this->input->post("dataValue", true);

		$values = [
			"estatus" => 0,
			"modificadoPor" => $dataValue["modificadoPor"],
			"fechaModificacion" => date('Y/m/d H:i:s')
		];

		$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $dataValue["eventId"]);

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

	public function createAppointmentByColaborator()
	{
		$titulo = $this->input->post('dataValue[titulo]');
		$idEspecialista = $this->input->post('dataValue[idEspecialista]');
		$idPaciente = $this->input->post('dataValue[idPaciente]');
		$observaciones = $this->input->post('dataValue[observaciones]');
		$fechaInicio = $this->input->post('dataValue[fechaInicio]');
		$fechaFinal = date('Y-m-d H:i:s', strtotime($fechaInicio . '+1 hour'));
		$tipoCita = $this->input->post('dataValue[tipoCita]');
		$idAtencionXSede = $this->input->post('dataValue[idAtencionXSede]');
		$estatusCita = $this->input->post('dataValue[estatusCita]');
		$detalle = $this->input->post('dataValue[detallePago]');

		$response['result'] = isset(
			$titulo,
			$idEspecialista,
			$idPaciente,
			$observaciones,
			$fechaInicio,
			$fechaFinal,
			$tipoCita,
			$idAtencionXSede,
			$estatusCita,
			$detalle
		);
		if ($response['result']) { // Validamos que vengan todos los valores de post
			$dataValue = ["idPaciente" => $idPaciente, "idUsuario" => $idEspecialista];
			$fechaFinalResta = date('Y/m/d H:i:s', strtotime($fechaFinal . '-1 minute'));
			$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($fechaInicio . '+1 minute'));
			$checkAppointment = $this->calendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$response['result'] = $checkAppointment->num_rows() === 0;

			if ($response['result']) { // Validamos que no tenga registros con horarios repetidos
				// Obtén la fecha actual
				$fechaActual = new DateTime();
				$fechaActual->modify('+3 hours');
				$fechaActual = $fechaActual->format('Y-m-d H:i:s');
				$response['result'] = $fechaInicio > $fechaActual; //Si la fecha de la cita es despues de la actual

				if ($response['result']) {
					$values = [
						"titulo" => $titulo, "idEspecialista" => $idEspecialista,
						"idPaciente" => $idPaciente, "observaciones" => $observaciones,
						"fechaInicio" => $fechaInicio, "fechaFinal" => $fechaFinal,
						"tipoCita" => $tipoCita, "idAtencionXSede" => $idAtencionXSede,
						"estatusCita" => $estatusCita, "creadoPor" => $idPaciente,
						"fechaModificacion" => date('Y-m-d H:i:s'), "modificadoPor" => $idPaciente,
						"idDetalle" => $detalle
					];
					$response["result"] = $this->generalModel->addRecord("citas", $values);
					if ($response["result"]) {
						$response["msg"] = "¡Se ha agendado la cita con exito!";
					} else {
						$response["msg"] = "¡Surgió un error al intentar guardar la cita!";
					}
				} else {
					$response['msg'] = "¡Horario de cita dentro del limite de horarios no permitidos!";
				}
			} else {
				$response['msg'] = '¡El horario ya ha sido ocupado!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}       

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}

	public function createAppointment()
	{
		$dataValue = $this->input->post("dataValue", true);
		$fundacion = $dataValue["fundacion"];
		$now = date('Y/m/d H:i:s', time());

		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));
		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));
		$year = date('Y', strtotime($dataValue["fechaInicio"]));
		$month = date('m', strtotime($dataValue["fechaInicio"]));

		// Obtén la fecha actual
		$fechaActual = new DateTime();
		$fechaActual->modify('+3 hours');
		$fechaActual = $fechaActual->format('Y/m/d H:i:s');
		$valid = $dataValue["fechaInicio"] > $fechaActual; //Si la fecha de la cita es despues de la actual

		$reagenda = $dataValue["reagenda"]; // valor 1 es cuando se reagenda

		if (!$valid) {
			$response["result"] = false;
			$response["msg"] = "¡Horario de cita dentro del limite de horarios no permitidos!";
		} else {

			if ($dataValue["fechaInicio"] > $now)
				$pass = true;

			$values = [
				"idEspecialista" => $dataValue["idUsuario"],
				"idPaciente" => $dataValue["idPaciente"],
				"estatusCita" => ($fundacion == 1 || $reagenda == 1) ? 1 : 6,
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"creadoPor" => $dataValue["creadoPor"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"fechaCreacion" => date("Y-m-d H:i:s"),
				"titulo" => $dataValue["titulo"],
				"modificadoPor" => $dataValue["modificadoPor"],
				"idAtencionXSede" => intval($dataValue["idCatalogo"]),
				"tipoCita" => 1,
				"idDetalle" => $dataValue["idDetalle"]
			];

			$checkUser = $this->usuariosModel->checkUser($dataValue["idPaciente"], $year, $month);
			$checkAppointment = $this->calendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkOccupied = $this->calendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "El paciente ocupo el horario";
			} else if ($checkOccupied->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "Horario no disponible";
			} else if ($checkUser->num_rows() === 0) {
				$response["result"] = false;
				$response["msg"] = "El paciente no ha ocupado sus beneficios disponibles";
			} else if (!isset($pass)) {
				$response["result"] = false;
				$response["msg"] = "Error en las fechas seleccionadas";
			} else {
				$addRecord = $this->generalModel->addRecord("citas", $values);

				if ($addRecord) {
					$response["result"] = true;
					$response["msg"] = "Se ha agendado a cita";
				} else {
					$response["result"] = false;
					$response["msg"] = "No se ha guardado la cita";
				}
			}
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function updateAppointment()
	{
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["fechaInicio"]; // datos para la validación de no mover una eveneto pasado de su dia
		$current = new DateTime();
		$now = $current->format('Y/m/d');

		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));
		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));

		if ($start < $now) {
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($response));
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"modificadoPor" => $dataValue["idUsuario"],
				"titulo" => $dataValue["titulo"]
			];

			$checkOccupied = $this->calendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointmentId = $this->calendarioModel->checkAppointmentId($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointmentId->num_rows() > 0) {
				$response["result"] = false;

				if ($checkAppointmentId->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->generalModel->updateRecord("citas", $values, "idCita", $dataValue["id"]);

				if ($updateRecord) {
					$response["result"] = true;
					$response["msg"] = "Horario actualizado";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al guardar";
				}
			}
		} catch (EXCEPTION $e) {
			$response["result"] = false;
			$response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function cancelAppointment()
	{
		$dataValue = $this->input->post("dataValue", true);
		$tipo = intval($dataValue["tipo"]);
		$current = new DateTime();
		$startStamp = $dataValue["startStamp"];
		$todayStamp = $current->format('Y/m/d H:i:s');

		$start = new DateTime($startStamp);
		$diferencia = $start->diff(new DateTime($todayStamp));
		$estatus = 2;

		if (in_array($tipo, array(3, 7, 8))) {
			$estatus = $tipo;
		} else if ($tipo === 0 && $diferencia->d === 0 && $diferencia->h < 3) { // condición para poder saber si se penaliza la cita
			$estatus = 3;
		}

		$values = [
			"estatusCita" => $estatus,
			"fechaModificacion" => $todayStamp,
			"modificadoPor" => $dataValue["modificadoPor"],
		];

		$updateRecord = $this->generalModel->updateRecord("citas", $values, "idCita", $dataValue["idCita"]);

		if ($updateRecord) {
			$response["result"] = true;
			$response["msg"] = "Se ha cancelado la cita";	
		} else {
			$response["result"] = false;
			$response["msg"] = "No se puede cancelar la cita";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function appointmentDrop()
	{
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["fechaInicio"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["oldStart"];
		$current = new DateTime();
		$now = $current->format('Y/m/d H:i:s');

		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));
		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));

		if ($start < $now) {
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior o actual";
			return;
		}

		if ($oldStart < $now) {
			$response["result"] = false;
			$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			return;
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => $now,
				"modificadoPor" => $dataValue["idUsuario"]
			];

			$checkOccupied = $this->calendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->calendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				if ($checkAppointment->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->generalModel->updateRecord("citas", $values, "idCita", $dataValue["id"]);

				if ($updateRecord) {
					$response["result"] = true;
					$response["msg"] = "Se actualizó la cita";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}
			}
		} catch (EXCEPTION $e) {
			$response["result"] = false;
			$response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function occupiedDrop()
	{
		$dataValue = $this->input->post("dataValue", true);
		$start = $dataValue["fechaInicio"]; // datos para la validación de no mover una eveneto pasado de su dia
		$oldStart = $dataValue["oldStart"];
		$current = new DateTime();
		$now = $current->format('Y/m/d H:i:s');

		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));
		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));

		if ($start < $now) {
			$reponse["result"] = false;
			$response["msg"] = "No se pueden mover las fechas a un dia anterior";
			return $response;
		}

		if ($oldStart < $now) {
			$response["result"] = false;
			$response["msg"] = "Las citas u horarios pasados no se pueden mover";
			return $response;
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => $now
			];

			$checkOccupied = $this->calendarioModel->checkOccupiedId($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->calendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "El horario ya ha sido ocupado";
			} else {
				$updateRecord = $this->generalModel->updateRecord("horariosOcupados", $values, "idUnico", $dataValue["id"]);

				if ($updateRecord) {
					$response["result"] = true;
					$response["msg"] = "Se actualizó el horario";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al actualizar horario";
				}
			}
		} catch (EXCEPTION $e) {
			$response["result"] = false;
			$response["msg"] = "Error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function endAppointment()
	{
		$dataValue = $this->input->post('dataValue', true);
		$idCita = $dataValue["idCita"];
		$idUsuario = $dataValue["idUsuario"];
		$reasons = $dataValue["reason"];
		$valuesAdd = [];

		foreach ($reasons as $reason) {
			array_push($valuesAdd, [
				"idCita" => $idCita,
				"idMotivo" => $reason["value"],
				"creadoPor" => $idUsuario,
				"fechaCreacion" => $now = date('Y/m/d H:i:s', time()),
				"modificadoPor" => $idUsuario,
				"fechaModificacion" => $now = date('Y/m/d H:i:s', time())
			]);
		}

		$valuesUpdate = [
			"estatusCita" => 4,
		];

		try {
			$updateRecord = $this->generalModel->updateRecord("citas", $valuesUpdate, "idCita", $idCita);
			if ($updateRecord) {
				$insertBatch = $this->generalModel->insertBatch("motivosPorCita", $valuesAdd);

				if ($insertBatch) {
					$response["result"] = true;
					$response["msg"] = "Se ha finalizado la cita";
				}
			} else {
				$response["result"] = false;
				$response["msg"] = "Error al finalizar la cita";
			}
		} catch (EXCEPTION $e) {
			$response["result"] = false;
			$response["msg"] = "Ha ocurrido un error";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function getBeneficiosPorSede()
	{
		$sede = $this->input->post('dataValue[sede]');

		$response['result'] = isset($sede);
		if ($response['result']) {
			$rs = $this->calendarioModel->getBeneficiosPorSede($sede)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Listado de beneficios cargado exitosamente!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getEspecialistaPorBeneficioYSede()
	{
		$area = $this->input->post('dataValue[area]');
		$sede = $this->input->post('dataValue[sede]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($area, $sede, $beneficio);
		if ($response['result']) {
			$rs = $this->calendarioModel->getEspecialistaPorBeneficioYSede($sede, $area, $beneficio)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Listado de especialistas cargado exitosamente!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
            $response['msg'] = "¡Parámetros inválidos!";
        }

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getModalidadesEspecialista()
	{
		$sede = $this->input->post('dataValue[sede]');
		$especialista = $this->input->post('dataValue[especialista]');

		$response['result'] = isset($sede, $especialista);
		if ($response['result']) {
			$rs = $this->calendarioModel->getModalidadesEspecialista($sede, $especialista)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Listado de modalidades cargado exitosamente!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
            $response['msg'] = "¡Parámetros inválidos!";
        }

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getAppointmentsByUser()
	{
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
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getReasons()
	{
		$puesto = $this->input->post('dataValue', true);

		switch ($puesto) {
			case 158:
				$tipo = 6;
				break;
			case 585:
				$tipo = 7;
				break;
			case 537:
				$tipo = 8;
				break;
			case 68:
				$tipo = 9;
				break;
		}

		$get = $this->calendarioModel->getReasons($tipo);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($get));
	}

	public function getOficinaByAtencion()
	{
		$sede = $this->input->post('dataValue[sede]');
		$beneficio = $this->input->post('dataValue[beneficio]');
		$especialista = $this->input->post('dataValue[especialista]');
		$modalidad = $this->input->post('dataValue[modalidad]');

		$rs = $this->calendarioModel->getOficinaByAtencion($sede, $beneficio, $especialista, $modalidad)->result();
		$response['result'] = count($rs) > 0;
		if ($response['result']) {
			$response['msg'] = '¡Datos de oficina cargados exitosamente!';
			$response['data'] = $rs;
		} else {
			$response['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function isPrimeraCita()
	{
		$usuario = $this->input->post('dataValue[usuario]');
		$especialista = $this->input->post('dataValue[especialista]');

		$response['result'] = isset($usuario, $especialista);
		if ($response['result']) {
			$rs = $this->calendarioModel->isPrimeraCita($usuario, $especialista)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Usuario con registros de citas!';
				// $response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getCitasSinFinalizarUsuario()
	{
		$usuario   = $this->input->post('dataValue[usuario]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($usuario, $beneficio);
		if ($response['result']) {
			$rs = $this->calendarioModel->getCitasSinFinalizarUsuario($usuario, $beneficio)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Usuario con citas sin finalizar!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getCitasFinalizadasUsuario()
	{
		$usuario = $this->input->post('dataValue[usuario]');
		$mes = $this->input->post('dataValue[mes]');
		$año = $this->input->post('dataValue[anio]');

		$response['result'] = isset($usuario, $mes, $año);
		if ($response['result']) {
			$rs = $this->calendarioModel->getCitasFinalizadasUsuario($usuario, $mes, $año)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Usuario con citas finalizadas!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getAtencionPorSede()
	{
		$especialista = $this->input->post('dataValue[especialista]');
		$sede         = $this->input->post('dataValue[sede]');
		$modalidad    = $this->input->post('dataValue[modalidad]');

		$response['result'] = isset($especialista, $sede, $modalidad);
		if ($response['result']) {
			$rs = $this->calendarioModel->getAtencionPorSede($especialista, $sede, $modalidad)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Datos de atencion por sede consultados!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}

	public function getPendingEnd()
	{
		$idUsuario = $this->input->post('dataValue', true);

		$get = $this->calendarioModel->getPending($idUsuario)->result();

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($get));
	}

	public function getPendientes(){
		$usuario = $this->input->post('dataValue[idUsuario]');
		
		$response['result'] = isset($usuario);
		if ($response['result']) {
			$rs = $this->calendarioModel->getPendientes($usuario)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['data'] = $rs;
				$response['msg'] = '¡Consulta de citas con estatus pendiente!';
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function getEventReasons(){
		$idCita = $this->input->post('dataValue', true);

		$response = $this->calendarioModel->getEventReasons($idCita)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function registrarTransaccionPago(){
		$usuario = $this->input->post('dataValue[usuario]');
		$folio = $this->input->post('dataValue[folio]');
		$concepto = $this->input->post('dataValue[concepto]');
		$cantidad = $this->input->post('dataValue[cantidad]');
		$metodoPago = $this->input->post('dataValue[metodoPago]');
		$fecha = date('Y-m-d H:i:s');
		
		$response['result'] = isset($usuario, $folio, $concepto, $cantidad, $metodoPago, $fecha);
		if ($response['result']) {
			$values = [
				"folio" => $folio,
				"idConcepto" => $concepto,
				"cantidad" => $cantidad,
				"metodoPago" => $metodoPago,
				"estatus" => 1,
				"creadoPor" => $usuario,
				"fechaCreacion" => $fecha,
				"modificadoPor" => $usuario,
				"fechaModificacion" => $fecha
			];
			$response["result"] = $this->generalModel->addRecord("detallePagos", $values);
			if ($response["result"]) {
				$response["msg"] = "¡Se ha generado el detalle de pago con exito!";
				$rs = $this->calendarioModel->getDetallePago($folio)->result();
				if (!empty($rs) && isset($rs[0]->idDetalle)) {
					$response["data"] = $rs[0]->idDetalle;
				} else {
					$response["data"] = null;
				}
			} 
			else {
				$response["msg"] = "¡Surgió un error al intentar generar el detalle de pago!";
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function getLastAppointment() {
		$usuario  = $this->input->post('dataValue[usuario]');
		$beneficio  = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($usuario, $beneficio);
		if ($response['result']) {
			$rs = $this->calendarioModel->getLastAppointment($usuario, $beneficio)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['data'] = $rs;
				$response['msg'] = '¡Última cita consultada exitosamente!';
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function checkInvoice()
    {
        $id = $this->input->post('dataValue');
    
        $response['result'] = isset($id);
        
        if ($response['result']) {
            $response['result'] = $this->calendarioModel->checkInvoice($id)->num_rows() === 0;
            if ($response['result']) {
                $response['msg'] = 'Se puede utilizar el folio';
            } else {
                $response['msg'] = 'Ya se ha cancelado y reagendado 2 veces';
            }
        }else{
            $response['msg'] = "¡Parámetros inválidos!";
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

	public function sendMail()
	{
		$data = $this->input->post('dataValue', true);

		$data["data"] = $data;
		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'smtp.gmail.com';
		$config['smtp_user'] = 'no-reply@ciudadmaderas.com';
		$config['smtp_pass'] = 'JDe64%8q5D';
		$config['smtp_port'] = 465;
		$config['charset']   = 'utf-8';
		$config['mailtype']  = 'html';
		$config['newline']   = "\r\n";

		$html_message = $this->load->view($data["view"], $data, true); // la variable de data["view"] para cargar una vista dinamica

		$this->load->library("email");
		$this->email->initialize($config);
		$this->email->from("no-reply@ciudadmaderas.com");
		$this->email->to($data["correo"]);
		$this->email->message($html_message);
		$this->email->subject("Citas Beneficios CM - " . date('d/m/Y - H:i:s A '));

		if ($this->email->send()) {
			$response["result"] = true;
			$response["msg"] = "Se ha enviado el correo";
		}
		else {
			$response["result"] = false;
			$response["msg"] = "Error al enviar el correo";
		}
		
		return $response;
	}

	public function updateDetallePaciente() {
		$user  = $this->input->post('dataValue[usuario]');
		$benefit  = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($user, $benefit);
		if ($response['result']) {
			switch ($benefit) {
				case 158: $column = 'estatusQB' ; break;
				case 585: $column = 'estatusPsi'; break;
				case 537: $column = 'estatusNut'; break;
				case 68:  $column = 'estatusGE' ; break;
			}
			$rs = $this->calendarioModel->checkDetailPacient($user, $column)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				if ($rs !== 1) {
					$values = [
						$column => 1,
						"modificadoPor" => $user,
						"fechaModificacion" => date("Y-m-d H:i:s"),
					];
					$updateRecord = $this->generalModel->updateRecord("detallePaciente", $values, "idUsuario", $user);
					if ($updateRecord) {
						$response['msg'] = '¡Registro de estatus actualizado!';
					}else {
						$response['msg'] = '¡Registro ya activo!';
					}
				}else {

				}
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
	
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
}
