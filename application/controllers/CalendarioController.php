<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class CalendarioController extends BaseController{
    public function __construct(){
		parent::__construct();
		$this->load->model('CalendarioModel');
		$this->load->model('GeneralModel');
		$this->load->model('UsuariosModel');
		$this->load->model('EspecialistasModel');
		$this->load->library("email");
		$this->load->library('GoogleApi');
		$this->ch = $this->load->database('ch', TRUE);
		date_default_timezone_set('America/Mexico_City');
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
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

        $occupied = $this->CalendarioModel->getOccupied($month, $idUsuario, $dates);
        $appointment = $this->CalendarioModel->getAppointment($month, $idUsuario, $dates);
		$external = $this->CalendarioModel->getExternalAppointments($month, $idUsuario, $dates);

        if ($occupied->num_rows() > 0 || $appointment->num_rows() > 0 || $external->num_rows() > 0)
            $response["events"] = array_merge($occupied->result(), $appointment->result(), $external->result());
        else
            $response["events"] = array();

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function getAllEventsWithRange()
	{
		$especialista = $this->input->post('dataValue[especialista]');
		$usuario      = $this->input->post('dataValue[usuario]');
		$fechaInicio  = $this->input->post('dataValue[fechaInicio]');
		$fechaFin     = $this->input->post('dataValue[fechaFin]');

		$response['result'] = isset($especialista, $usuario, $fechaInicio, $fechaFin);
		if ($response['result']) {
			$occupied = $this->CalendarioModel->getOccupiedRange($fechaInicio, $fechaFin, $especialista);
			$appointment = $this->CalendarioModel->getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario);

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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getHorarioBeneficio()
	{
		$beneficio = $this->input->post('dataValue[beneficio]');
		$especialista = $this->input->post('dataValue[especialistah]');

		$rs = $this->CalendarioModel->getHorarioBeneficio($beneficio, $especialista)->result();
		$response['result'] = count($rs) > 0;
		if ($response['result']) {
			$response['msg'] = '¡Horario cargado exitosamente!';
			$response['data'] = $rs;
		} else {
			$response['msg'] = '¡No existen horario!';
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
			$checkOccupied = $this->CalendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->CalendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() < 1 && $checkAppointment->num_rows() < 1 && isset($pass)) {
				$addRecord = $this->GeneralModel->addRecord( $this->schema_cm.".horariosocupados", $values);

				if ($addRecord) {
					$response["result"] = true;
					$response["msg"] = "Se ha guardado el horario";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al guardar el horario";
				}
			} else {
				$response["result"] = false;

				if ($checkAppointment->num_rows() > 0) {
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
			$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"titulo" => $dataValue["titulo"],
				"modificadoPor" => $dataValue["modificadoPor"]
			];

			$checkOccupiedId = $this->CalendarioModel->checkOccupiedId($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->CalendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupiedId->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;

				if ($checkAppointment->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".horariosocupados", $values, "idUnico", $dataValue["id"]);

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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function deleteOccupied()
	{
		$dataValue = $this->input->post("dataValue", true);

		$values = [
			"estatus" => 0,
			"modificadoPor" => $dataValue["modificadoPor"],
			"fechaModificacion" => date('Y/m/d H:i:s')
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".horariosocupados", $values, "idUnico", $dataValue["eventId"]);

		if ($updateRecord) {
			$response["result"] = true;
			$response["msg"] = "Se ha eliminado el horario";
		} else {
			$response["result"] = false;
			$response["msg"] = "No se puede eliminar el horario";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
        $modalidad = $this->input->post('dataValue[modalidad]');
        $idAtencionXSede = $this->input->post('dataValue[idAtencionXSede]');
        $idSede = $this->input->post('dataValue[idSede]');
        $estatusCita = $this->input->post('dataValue[estatusCita]');
        $detalle = $this->input->post('dataValue[detallePago]');
        $idGoogleEvent = $this->input->post('dataValue[idGoogleEvent]');
        $fecha = date('Y-m-d H:i:s');

        $response['result'] = isset(
            $titulo,
            $idEspecialista,
            $idPaciente,
            $observaciones,
            $fechaInicio,
            $fechaFinal,
            $modalidad,
            $tipoCita,
            $idAtencionXSede,
            $estatusCita
        );
		
        if ($response['result']) { // Validamos que vengan todos los valores de post
            // Validación para ver que tenga dias disponibles en su sede de manera presencial, que el especialista
            // brinde la atención en su sede, y si la brinde que sea la unica y en caso que no que tenga dias asignados a esa sede. 
            if ($modalidad === "1") { 
                $sedesatencion = $this->CalendarioModel->getSedesDeAtencionEspecialista($idEspecialista);
                $response['result'] = $sedesatencion->num_rows() > 0;
                if ($response['result']) {
                    $response['result'] = $sedesatencion->num_rows() > 1;
                    if ($response['result']){
                        $response['result'] = false;
                        foreach ($sedesatencion->result() as $row) {
                            if ($row->value == $idSede) {
                                $response['result'] = true;
                                break;
                            }
                        }
                        if ($response['result']) {
                            $checkPresencial = $this->CalendarioModel->checkPresencial($idSede, $idEspecialista, date('Y-m-d', strtotime($fechaInicio)));
                            $response['result'] = $checkPresencial->num_rows() > 0;
                            if (!$response['result']) {
                                $response['msg'] = "¡El especialista cambió los dias de atención!"; 
                            }
                        }else {
                            $response['msg'] = "¡El especialista no brinda la atención en su sede!";
                        }
                    }else {
                        $response['result'] = $sedesatencion->num_rows() === 1 && $sedesatencion->result()[0]->value == $idSede; 
                        if (!$response['result']) {
                            $response['msg'] = "¡El especialista no brinda atención en su sede!";
                        }
                    }
                }else {
                    $response['msg'] = "¡No se encontró el listado de atención del especialista!";
                }
            }

            if ($response['result']) {
                $dataValue = ["idPaciente" => $idPaciente, "idUsuario" => $idEspecialista];
                $fechaFinalResta = date('Y/m/d H:i:s', strtotime($fechaFinal . '-1 minute'));
                $fechaInicioSuma = date('Y/m/d H:i:s', strtotime($fechaInicio . '+1 minute'));
                $checkAppointment = $this->CalendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);
                $checkOccupied = $this->CalendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
                $response['result'] = $checkAppointment->num_rows() === 0 && $checkOccupied->num_rows() === 0;
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
                            "fechaModificacion" => $fecha, 
							"fechaCreacion" => $fecha,
							"modificadoPor" => $idPaciente, "idDetalle" => $detalle,
                            "idEventoGoogle" => $idGoogleEvent,
                        ];
                        $rs = $this->GeneralModel->addRecordReturnId( $this->schema_cm.".citas", $values);
                        $response["result"] = $rs > 0;
                        $response["data"] = $rs;
                        if ($response["result"]) {

                            $response["msg"] = "¡Se ha agendado la cita con éxito!";
                        } else {
                            $response["msg"] = "¡Surgió un error al intentar guardar la cita!";
                        }
                    } else {
                        $response['msg'] = "¡Horario de cita dentro del limite de horarios no permitidos!";
                    }
                } else {
                    $response['msg'] = '¡El horario ya ha sido ocupado!';
                }   
            }
        }else {
            $response['msg'] = "¡Parámetros inválidos!";
        }       

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function createAppointment()
	{
		$dataValue = $this->input->post("dataValue", true);
		$fundacion = $dataValue["fundacion"];
		$tipoPuesto = $dataValue["tipoPuesto"];
		$now = date('Y/m/d H:i:s', time());

		$fechaFinalResta = date('Y/m/d H:i:s', strtotime($dataValue["fechaFinal"] . '-1 minute'));
		$fechaInicioSuma = date('Y/m/d H:i:s', strtotime($dataValue["fechaInicio"] . '+1 minute'));

		$year = date('Y', strtotime($dataValue["fechaInicio"]));
		$month = date('m', strtotime($dataValue["fechaInicio"]));

		$fechaActual = new DateTime(); // Obtén la fecha actual
		$fechaActual->modify('+3 hours');
		$fechaActual = $fechaActual->format('Y/m/d H:i:s');
		$valid = $dataValue["fechaInicio"] > $fechaActual; //Si la fecha de la cita es despues de la actual

		$reagenda = $dataValue["reagenda"]; // valor 1 es cuando se reagenda

		$time = strtotime($dataValue["fechaInicio"]);
		$fechaCheck = date('Y-m-d', $time);

		if (!$valid) {
			$response["result"] = false;
			$response["msg"] = "¡Horario de cita dentro del limite de horarios no permitidos!";
			
			$this->output->set_content_type("application/json");
			$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
		} else {

			if ($dataValue["fechaInicio"] > $now)
				$pass = true;

			$values = [
				"idEspecialista" => $dataValue["idUsuario"],
				"idPaciente" => $dataValue["idPaciente"],
				"estatusCita" => ($fundacion == 1 || $reagenda == 1 || $tipoPuesto == 'Operativa') ? 1 : 6,
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"creadoPor" => $dataValue["creadoPor"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"fechaCreacion" => date("Y-m-d H:i:s"),
				"titulo" => $dataValue["titulo"],
				"modificadoPor" => $dataValue["modificadoPor"],
				"idAtencionXSede" => intval($dataValue["idCatalogo"]),
				"tipoCita" => $reagenda == 1 ? $dataValue['oldEventTipo'] : 3,
				"idDetalle" => $dataValue["idDetalle"] == 0 ? NULL : $dataValue["idDetalle"],
				"idEventoGoogle" => $reagenda == 1 ? $dataValue["idEventoGoogle"] : ''
			];

			$checkModalitie = $this->EspecialistasModel->checkModalitie($dataValue["idUsuario"], $fechaCheck);
			$checkUser = $this->UsuariosModel->checkUser($dataValue["idPaciente"], $year, $month);
			$checkAppointment = $this->CalendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkOccupied = $this->CalendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			
			if ($checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "El horario ya esta ocupado";
			} else if ($checkOccupied->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "Horario no disponible"; 
			}  /* else if ($checkUser->num_rows() == 0 && $reagenda == 0 && $fundacion == 0) {
				$response["result"] = false;
				$response["msg"] = " El paciente debe finalizar sus beneficios mensuales";
			} */ else if($checkUser->num_rows() === 0 && $reagenda == 1 && $month != date('m') || $checkUser->num_rows() === 0 && $reagenda == 1 && $year != date('Y')){
				$response["result"] = false;
				$response["msg"] = "Solo se puede reagendar en el mismo mes";
			} else if (!isset($pass)) {
				$response["result"] = false;
				$response["msg"] = "Error en las fechas seleccionadas";
			} else if ($checkModalitie->num_rows() > 0 && $checkModalitie->result()[0]->idSede != $dataValue["idSede"] && $dataValue["modalidad"] == 1){
				$response["result"] = false;
				$response["msg"] = "La sede presencial es distinta al del paciente seleccionado";
			} else {
				$rs = $this->GeneralModel->addRecordReturnId( $this->schema_cm.".citas", $values);
				$addRecord = $rs > 0; 
				
				if ($addRecord) {
					$response["result"] = true;
					$response["data"] = $rs;
					$response["msg"] = "Se ha agendado la cita";
				} else {
					$response["result"] = false;
					$response["msg"] = "Error al agendar";
				}
			}
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
			$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
		}

		try {
			$values = [
				"fechaInicio" => $dataValue["fechaInicio"],
				"fechaFinal" => $dataValue["fechaFinal"],
				"fechaModificacion" => date("Y-m-d H:i:s"),
				"modificadoPor" => $dataValue["idUsuario"],
				"titulo" => $dataValue["titulo"]
			];

			$checkOccupied = $this->CalendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointmentId = $this->CalendarioModel->checkAppointmentId($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointmentId->num_rows() > 0) {
				$response["result"] = false;

				if ($checkAppointmentId->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, "idCita", $dataValue["id"]);

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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function cancelAppointment()
	{
		$dataValue = $this->input->post("dataValue", true);
		$tipo = intval($dataValue["tipo"]);
		$startDate = $dataValue["start"];

		$estatus = 2;

		$fechaActual = new DateTime();
		$fechaActual->modify('+3 hours');
		$fechaActual = $fechaActual->format('Y-m-d H:i:s');

		if (in_array($tipo, array(3, 7, 8))) {
			$estatus = $tipo;
		} else if ($tipo == 0 && ($fechaActual > $startDate)) { // condición para poder saber si se penaliza la cita
			$estatus = 3;
		}

		$values = [
			"estatusCita" => $estatus,
			"fechaModificacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => $dataValue["modificadoPor"],
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, "idCita", $dataValue["idCita"]);

		if ($updateRecord) {
			$response["result"] = true;
			$response["msg"] = "Se ha cancelado la cita";	
		} else {
			$response["result"] = false;
			$response["msg"] = "No se puede cancelar la cita";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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

			$checkOccupied = $this->CalendarioModel->checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->CalendarioModel->checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				if ($checkAppointment->num_rows() > 0) {
					$response["msg"] = "El paciente ocupo el horario";
				} else {
					$response["msg"] = "Horario no disponible";
				}
			} else {
				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, "idCita", $dataValue["id"]);

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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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

			$checkOccupied = $this->CalendarioModel->checkOccupiedId($dataValue, $fechaInicioSuma, $fechaFinalResta);
			$checkAppointment = $this->CalendarioModel->checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta);

			if ($checkOccupied->num_rows() > 0 || $checkAppointment->num_rows() > 0) {
				$response["result"] = false;
				$response["msg"] = "El horario ya ha sido ocupado";
			} else {
				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".horariosocupados", $values, "idUnico", $dataValue["id"]);

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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
			$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $valuesUpdate, "idCita", $idCita);
			if ($updateRecord) {
				$insertBatch = $this->GeneralModel->insertBatch($this->schema_cm .".motivosporcita", $valuesAdd);

				if ($insertBatch) {
					$this->evaluacion($idCita);
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function evaluacion($idCita){
        $response['result'] = isset($idCita);

        if ($response['result']) {

            $query = $this->ch->query("SELECT tipoCita FROM ". $this->schema_cm .".citas
			WHERE idCita = $idCita");

			$tipoCita = 0;
			foreach ($query->result() as $row) {
				$tipoCita = $row->tipoCita;
			}

			if($tipoCita == 1){
				$data = array(
					"primeraSesion" => 0,
				);
		
				$this->GeneralModel->updateRecord($this->schema_cm .'.evaluacionencuestas', $data, 'idCita', $idCita);
			}else{
				$data = array(
					"satisfaccion" => 0,
				);
		
				$this->GeneralModel->updateRecord($this->schema_cm .'.evaluacionencuestas', $data, 'idCita', $idCita);
			}

        }else {
            $response['msg'] = "¡Parámetros inválidos!";
        }
    }

	public function getBeneficiosPorSede()
	{
		$sede = $this->input->post('dataValue[sede]');
		$area = $this->input->post('dataValue[area]');

		$response['result'] = isset($sede);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getBeneficiosPorSede($sede, $area)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getEspecialistaPorBeneficioYSede()
	{
		$area = $this->input->post('dataValue[area]');
		$sede = $this->input->post('dataValue[sede]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$token = $this->headers('Token');
        $user = json_decode(base64_decode(explode(".", $token)[1]));

		$response['result'] = isset($area, $sede, $beneficio);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getEspecialistaPorBeneficioYSede($sede, $area, $beneficio, $user->numEmpleado)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getModalidadesEspecialista()
	{
		$sede = $this->input->post('dataValue[sede]');
		$especialista = $this->input->post('dataValue[especialista]');
		$area = $this->input->post('dataValue[area]');
		
		$response['result'] = isset($sede, $especialista);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getModalidadesEspecialista($sede, $especialista, $area)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getModalidadesEspecialistaBene()
	{
		$sede = $this->input->post('dataValue[sede]');
		$especialista = $this->input->post('dataValue[especialista]');
		$area = $this->input->post('dataValue[area]');
		
		$response['result'] = isset($sede, $especialista);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getModalidadesEspecialistaBene($sede, $especialista, $area)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getAppointmentsByUser()
	{
		$year = $this->input->post('dataValue[year]');
		$month = $this->input->post('dataValue[month]');
		$idUsuario = $this->input->post('dataValue[idUsuario]');

		$response['result'] = isset($year, $month, $idUsuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getAppointmentsByUser($year, $month, $idUsuario)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
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
			case 686:
				$tipo = 9;
				break;
		}

		$get = $this->CalendarioModel->getReasons($tipo);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($get, JSON_NUMERIC_CHECK));
	}

	public function getOficinaByAtencion()
	{
        $sede = $this->input->post('dataValue[sede]');
        $beneficio = $this->input->post('dataValue[beneficio]');
        $especialista = $this->input->post('dataValue[especialista]');
        $modalidad = $this->input->post('dataValue[modalidad]');

        $rs = $this->CalendarioModel->getOficinaByAtencion($sede, $especialista, $modalidad)->result();
        $response['result'] = count($rs) > 0;
        if ($response['result']) {
            $response['msg'] = '¡Datos de oficina cargados exitosamente!';
            $response['data'] = $rs;
        } else {
            $response['msg'] = '¡No existen registros!';
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function isPrimeraCita() {
		$usuario = $this->input->post('dataValue[usuario]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($usuario, $beneficio);
		if ($response['result']) {
			$rs = $this->CalendarioModel->isPrimeraCita($usuario, $beneficio)->result();
			$response['result'] = count($rs) == 0;
			if ($response['result']) {
				$response['msg'] = '¡Primera cita del usuario!';
			} else {
				$response['msg'] = '¡Usuario con más citas!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getCitasSinFinalizarUsuario()
	{
		$usuario   = $this->input->post('dataValue[usuario]');
		$beneficio = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($usuario, $beneficio);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getCitasSinFinalizarUsuario($usuario, $beneficio)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getCitasSinEvaluarUsuario()
	{
		$usuario = $this->input->post('dataValue[usuario]');

		$response['result'] = isset($usuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getCitasSinEvaluarUsuario($usuario)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Usuario con citas sin evaluar!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getCitasSinPagarUsuario()
	{
		$usuario = $this->input->post('dataValue[usuario]');

		$response['result'] = isset($usuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getCitasSinPagarUsuario($usuario)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['msg'] = '¡Usuario con citas sin pagar!';
				$response['data'] = $rs;
			} else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getCitasFinalizadasUsuario()
	{
		$usuario = $this->input->post('dataValue[usuario]');
		$mes = $this->input->post('dataValue[mes]');
		$año = $this->input->post('dataValue[anio]');

		$response['result'] = isset($usuario, $mes, $año);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getCitasFinalizadasUsuario($usuario, $mes, $año)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getAtencionPorSede()
	{
		$especialista = $this->input->post('dataValue[especialista]');
		$sede         = $this->input->post('dataValue[sede]');
		$area         = $this->input->post('dataValue[area]');
		$modalidad    = $this->input->post('dataValue[modalidad]');

		$response['result'] = isset($especialista, $sede, $modalidad);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getAtencionPorSede($especialista, $sede, $area, $modalidad)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getPendingEnd()
	{
		$idUsuario = $this->input->post('dataValue', true);

		$get = $this->CalendarioModel->getPending($idUsuario)->result();

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($get, JSON_NUMERIC_CHECK));
	}

	public function getPendientes(){
		$usuario = $this->input->post('dataValue[idUsuario]');
		
		$response['result'] = isset($usuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getPendientesPago($usuario)->result();
			// $rs2 = $this->CalendarioModel->getPendientesEvaluacion($usuario)->result();

			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['data']['pago'] = $rs;
				// $response['data']['evaluacion'] = $rs2;
				$response['msg'] = '¡Consulta de citas con estatus pendiente!';
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getUnrated(){
		$usuario = $this->input->post('dataValue[idUsuario]');
		
		$response['result'] = isset($usuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getUnrated($usuario)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getEventReasons(){
		$idCita = $this->input->post('dataValue', true);

		$response = $this->CalendarioModel->getEventReasons($idCita)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function registrarTransaccionPago(){
		$fecha = date('Y-m-d H:i:s');
		$usuario = $this->input->post('dataValue[usuario]');
		$folio = $this->input->post('dataValue[folio]');
		$referencia = $this->input->post('dataValue[referencia]');
		$concepto = $this->input->post('dataValue[concepto]');
		$cantidad = $this->input->post('dataValue[cantidad]');
		$metodoPago = $this->input->post('dataValue[metodoPago]');
		$estatusPago = $this->input->post('dataValue[estatusPago]');
		$fechaPago = $metodoPago == 7 ? $fecha : $this->input->post('dataValue[fechaPago]');
		
		$response['result'] = isset($usuario, $folio, $referencia, $concepto, $cantidad, $metodoPago, $estatusPago, $fechaPago);
		if ($response['result']) {
			$values = [
				"folio" => $folio,
				"idConcepto" => $concepto,
				"referencia" => $referencia,
				"cantidad" => $cantidad,
				"metodoPago" => $metodoPago,
				"estatusPago" => $estatusPago,
				"fechaPago" => $fechaPago,
				"estatus" => 1,
				"creadoPor" => $usuario,
				"fechaCreacion" => $fecha,
				"modificadoPor" => $usuario,
				"fechaModificacion" => $fecha
			];
			$response["result"] = $this->GeneralModel->addRecord( $this->schema_cm.".detallepagos", $values);
			if ($response["result"]) {
				$rs = $this->CalendarioModel->getDetallePago($folio)->result();
				if (!empty($rs) && isset($rs[0]->idDetalle)) {
					$response["data"] = $rs[0]->idDetalle;
					$partes = explode('-', $referencia);
					$ultimoDatoReferencia = end($partes);

					$idCita = substr($ultimoDatoReferencia, 1); //Cortamos la inicial del dato que es una letra para extraer solo el numero del id

					if ($concepto == 1) { // Actualizamos el id de cita
						$upd = [
							"idDetalle" => $rs[0]->idDetalle,
							"estatusCita" => 1,
							"modificadoPor" => $usuario,
							"fechaModificacion" => $fecha 
						];
						$response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $upd, 'idCita', $idCita);
						if ($response["result"]) {
							$response["msg"] = "¡Se ha generado el detalle de pago de cita con éxito!";
						}else {
							$response["msg"] = "¡Surgió un error al enlazar la cita con el pago!";
						}
					}else {
						$response["msg"] = "¡Se ha generado el detalle de pago con éxito!";
					}
				} else {
					$response["msg"] = 'No se pudo consultar los datos de detalle de pago';
				}
			} 
			else {
				$response["msg"] = "¡Surgió un error al intentar generar el detalle de pago!";
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getLastAppointment() {
		$usuario  = $this->input->post('dataValue[usuario]');
		$beneficio  = $this->input->post('dataValue[beneficio]');

		$response['result'] = isset($usuario, $beneficio);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getLastAppointment($usuario, $beneficio)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function checkInvoice()
    {
        $id = $this->input->post('dataValue');
    
        $response['result'] = isset($id);
        
        if ($response['result']) {
            $response['result'] = $this->CalendarioModel->checkInvoice($id)->num_rows() === 0;
            if ($response['result']) {
                $response['msg'] = 'Se puede utilizar el folio';
            } else {
                $response['msg'] = 'Ya se ha cancelado y reagendado 2 veces';
            }
        }else{
            $response['msg'] = "¡Parámetros inválidos!";
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function sendMail()
	{
		$data = $this->input->post('dataValue', true);
		$fecha = date('Y-m-d H:i:s');

		$data["data"] = $data;
		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'smtp.gmail.com';
		$config['smtp_user'] = 'no-reply@ciudadmaderas.com';
		$config['smtp_pass'] = 'JDe64%8q5D';
		$config['smtp_port'] = 465;
		$config['charset']   = 'utf-8';
		$config['mailtype']  = 'html';
		$config['newline']   = "\r\n";
		$config['smtp_crypto']   = 'ssl';

		$html_message = $this->load->view($data["view"], $data, true); // la variable de data["view"] para cargar una vista dinamica

		$this->email->initialize($config);
		$this->email->from("no-reply@ciudadmaderas.com");
		$this->email->to($data["correo"]); // 'correo' or 'correo, correo1' or [correo, correo1, correo2].
		$this->email->message($html_message);
		$subject = "Citas Beneficios CM - " . $fecha;
		$this->email->subject($subject);

		if ($this->email->send()) {
			$response["result"] = true;
			$response["msg"] = "Se ha enviado el correo";
		}
		else {
			$response["result"] = false;
			$response["msg"] = "Error al enviar el correo";
		}

		$logData['fromEmail'] = $config['smtp_user'];
		$logData['toEmail'] = implode(', ', $data["correo"]); // ['pedro', 'luis', 'equis']
		$logData['subject'] = $subject;
		$logData['content'] = $data["view"];
		$logData['result'] = $response["result"];
		$logData['estatus'] = 1;
		$logData['creadoPor'] = $data["idUsuario"];
		$logData['fechaCreacion'] = $fecha;
		$logData['modificadoPor'] = $data["idUsuario"];
		$logData['fechaModificacion'] = $fecha;

		$this->GeneralModel->addRecord( $this->schema_cm.".emaillogs", $logData);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function updateDetallePaciente() {
		$user  	 = $this->input->post('dataValue[usuario]');
		$benefit = $this->input->post('dataValue[beneficio]');
		$fecha   = date("Y-m-d H:i:s");

		$response['result'] = isset($user, $benefit);
		if ($response['result']) {
			switch ($benefit) {
				case 158: $column = 'estatusQB' ; break;
				case 585: $column = 'estatusPsi'; break;
				case 537: $column = 'estatusNut'; break;
				case 686:  $column = 'estatusGE' ; break;
				default: $column = 'estatus';
			}
			$rs = $this->CalendarioModel->checkDetailPacient($user, $column)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				if ($rs !== 1) {
					$values = [
						$column => 1,
						"modificadoPor" => $user,
						"fechaModificacion" => $fecha,
					];
					$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".detallepaciente", $values, "idUsuario", $user);
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function updateAppointmentData() {
		$idUsuario     = $this->input->post('dataValue[idUsuario]');
		$idCita        = $this->input->post('dataValue[idCita]');
		$estatus       = $this->input->post('dataValue[estatus]');
		$detalle       = $this->input->post('dataValue[detalle]');
		$evaluacion    = $this->input->post('dataValue[evaluacion]');
		$googleEventId = $this->input->post('dataValue[googleEventId]');

		$response['result'] = isset($idUsuario, $idCita);
		if ($response['result']) {
			
			$values = [
				"estatusCita" => $estatus,
				"idDetalle" => $detalle,
				"evaluacion" => $evaluacion,
				"idEventoGoogle" => $googleEventId,
				"modificadoPor" => $idUsuario,
				"fechaModificacion" => date("Y-m-d H:i:s"),
			];
			$response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, 'idCita', $idCita);
			if ($response["result"]) {
				$response["msg"] = "¡Se ha actualizado la información de la cita!";
			}else {
				$response["msg"] = "¡Surgió un error al intentar actualizar los datos de cita!";
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function updateStatusAppointmentData() {
		$idUsuario     = $this->input->post('dataValue[idUsuario]');
		$idCita        = $this->input->post('dataValue[idCita]');
		$estatus       = $this->input->post('dataValue[estatus]');

		$response['result'] = isset($idUsuario, $idCita, $estatus);
		if ($response['result']) {
			
			$values = [
				"estatusCita" => $estatus,
				"modificadoPor" => $idUsuario,
				"fechaModificacion" => date("Y-m-d H:i:s"),
			];
			$response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, 'idCita', $idCita);
			if ($response["result"]) {
				$response["msg"] = "¡Se ha actualizado la información de la cita!";
			}else {
				$response["msg"] = "¡Surgió un error al intentar actualizar los datos de cita!";
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getCitaById () {
		$idCita  = $this->input->post('dataValue[idCita]');

		$response['result'] = isset($idCita);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getCitaById($idCita)->result();
			$response['result'] = count($rs) > 0;
			if ($response["result"]) {
				$response["data"] = $rs;
				$response["msg"] = "¡Se ha generado el detalle de pago con éxito!";
			}else {
				$response["msg"] = "¡Surgió un error al intentar actualizar los datos de cita!";
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function insertGoogleEvent () {
        $dataValue = $this->input->post("dataValue", true);

        $title       = $this->input->post('dataValue[title]'); // Titulo del evento: 'Cualquier titulo'
        $start       = $this->input->post('dataValue[start]'); // Fecha inicial del evento: '2024-01-31T13:00:00'
        $end         = $this->input->post('dataValue[end]');   // Fecha final del evento: '2024-01-31T14:00:00'
        $location    = $this->input->post('dataValue[location]'); // Ubicación del evento: 'Queretaro, querétaro, etc. etc...'
        $description = $this->input->post('dataValue[description]'); // Descripción del evento: 'Cita para el beneficio de ciudad maderas'
        $attendees   = $this->input->post('dataValue[attendees]'); // Personas involucradas para asistir al evento
        $email       = $this->input->post('dataValue[email]'); // Correo del que utilizará el correo para agendarlo.

        $response['result'] = isset($title, $end, $start, $attendees, $email);
        if ($response['result']) {
            $this->googleapi->getAccessToken($email);

            $data = json_encode(array(
                'summary' => $title,
                'location' => $location,
                'description' => $description,
                'start' => array(
                    'dateTime' => $start,
                    'timeZone' => 'America/Mexico_City',
                ),
                'end' => array(
                    'dateTime' => $end,
                    'timeZone' => 'America/Mexico_City',
                ),
                'attendees' => $attendees,
                'source' => [
                    'title' => 'Beneficios Maderas',
                    'url' => 'https://prueba.gphsis.com/beneficiosmaderas/'
                ],
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                        array('method' => 'email', 'minutes' => 24 * 60), // 1 dia antes
                        array('method' => 'email', 'minutes' => 4 * 60), // 4 horas antes
                        array('method' => 'popup', 'minutes' => 24* 60), // 1 dia antes
                        array('method' => 'popup', 'minutes' => 4 * 60), // 4 horas antes
                    ),
                ),
                'visibility' => 'public',
                'colorId' => '07'
            ), JSON_NUMERIC_CHECK);

            $event = $this->googleapi->createCalendarEvent('primary', $data);
            $response['result'] = !isset($event->error); 

            if ($response['result']) {
                $response['data'] = $event;
                $response['msg'] = "¡Evento registrado en el calendario de google!"; 
            }else {
                $response['msg'] = "¡No se pudo insertar el evento en el calendario de google!"; 
            }
        }else {
            $response['msg'] = "¡Parámetros inválidos!";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function updateGoogleEvent(){
        $start = $this->input->post("dataValue[start]");
        $end = $this->input->post("dataValue[end]");
        $id = $this->input->post("dataValue[id]");
        $email    = $this->input->post("dataValue[email]");
		$attendees = $this->input->post("dataValue[attendees]");

        $response["result"] = isset($id, $start, $end, $email, $attendees);
	
        if($response["result"]){
            $this->googleapi->getAccessToken($email);

            $data = json_encode(array(
                'start' => array(
                    'dateTime' => $start,
                    'timeZone' => 'America/Mexico_City',
                ),
                'end' => array(
                    'dateTime' => $end,
                    'timeZone' => 'America/Mexico_City',
				),
				'attendees' => $attendees
            ), JSON_NUMERIC_CHECK);
    
            $updatedEvent = $this->googleapi->updateCalendarEvent('primary', $id, $data);
            $response["result"] = !isset($updatedEvent->error);

            if($response["result"]){
                $response["data"] = $updatedEvent;
                $response["msg"] = "Se ha actualizado los datos del evento en el calendario de google";
            }
            else{
                $response["msg"] = "No se pudo realizar la modificación de datos del evento en el calendario de google";
            }
        } 
        else{
            $response['msg'] = "¡Parámetros inválidos!";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function deleteGoogleEvent(){

        $id    = $this->input->post('dataValue[id]');
        $email = $this->input->post('dataValue[email]');

        $response["result"] = isset($id, $email);

        if($response['result']){
            $this->googleapi->getAccessToken($email);

            $delete = $this->googleapi->deleteCalendarEvent('primary', $id);
            $response['result'] = !isset($delete->error);
            if ($response['result']) {
                $response['msg'] = "¡Evento eliminado en el calendario de google!"; 
                $response['data'] = $delete;
            }else {
                $response['msg'] = "¡No se pudo eliminar el evento en el calendario de google!";
            }
        }
        else{
            $response['msg'] = "¡Parámetros inválidos!";
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function insertGoogleId(){
		$dataValue = $this->input->post('dataValue', true);

		$data = [
			'idEventoGoogle' => $dataValue['idEventoGoogle']
		];

		$update = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $data, 'idCita', $dataValue["idCita"]);

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($update, JSON_NUMERIC_CHECK));
	}

	public function getSedesDeAtencionEspecialista(){
		$idUsuario = $this->input->post('dataValue[idUsuario]');

		$response['result'] = isset($idUsuario);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getSedesDeAtencionEspecialista($idUsuario)->result();
			$response['result'] = count($rs) > 0;
			if ($response['result']) {
				$response['data'] = $rs;
				$response['msg'] = '¡Sedes de atención de especialista cargadas exitosamente!';
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getDiasDisponiblesAtencionEspecialista(){
		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$idSede = $this->input->post('dataValue[idSede]');

		$response['result'] = isset($idUsuario, $idSede);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getDiasDisponiblesAtencionEspecialista($idUsuario, $idSede);
			$response['result'] = count($rs->result()) > 0;
			if ($response['result']) {
				$dias = [];
				foreach ($rs->result_array() as $dia) {
					array_push($dias, $dia['presencialDate']);
				}
				$response['data'] = $dias;
				$response['msg'] = '¡Sedes de atención de especialista cargadas exitosamente!';
			}else {
				$response['msg'] = '¡No existen registros!';
			}
		}else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function actualizaFechaIntentoPago(){
		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$idCita = $this->input->post('dataValue[idCita]');
		$fecha = date('Y-m-d H:i:s');

		$values = [
			"estatusCita" => 10, // Procesando pago 
			"fechaIntentoPago" => $fecha,
			"modificadoPor" => $idUsuario, 
			"fechaModificacion" => $fecha,
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $values, "idCita", $idCita);
		$response["result"] = $updateRecord;
		if ($response["result"]) {
			$response["msg"] = "Fecha de intento de pago actualizada";
		} else {
			$response["msg"] = "Error al actualizar el dato";
		}

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getBeneficioActivo(){
		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$get = $this->CalendarioModel->getBeneficioActivo($idUsuario)->result();

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($get, JSON_NUMERIC_CHECK));
	}

	public function getDocumento(){
		$beneficio = $this->input->post("dataValue[beneficio]");

		switch($beneficio){
			case 585:
				$beneficio = 1;
				break;

			case 537:
				$beneficio = 2;
				break;

			case 686:
				$beneficio = 3;
				break;

			case 158:
				$beneficio = 4;
				break;
		}
		$get = $this->CalendarioModel->getDocumento($beneficio)->result();

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($get, JSON_NUMERIC_CHECK));
	}

	public function getSedeEsp()
	{
		$idEsp = $this->input->post('dataValue[especialista]');

		$response['result'] = isset($idEsp);
		if ($response['result']) {
			$rs = $this->CalendarioModel->getSedeEsp($idEsp)->result();
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
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function eventCancelaCitasSinPago(){
		$rs = $this->CalendarioModel->eventCancelaCitasSinPago();

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($rs, JSON_NUMERIC_CHECK));
	}

}