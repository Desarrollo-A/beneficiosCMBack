<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class EventosController extends BaseController {
	public function __construct()
	{
		parent::__construct();
		$this->ch = $this->load->database('ch', TRUE);
		$this->load->database('default');
		$this->load->model('perfil/EventosModel');
		$this->load->model('GeneralModel');
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

    // Funciones auxiliares para simplificar respuestas
    private function errorResponse($message) {
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode(["result" => false, "msg" => $message], JSON_NUMERIC_CHECK));
    }
    
    private function successResponse($message) {
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode(["result" => true, "msg" => $message], JSON_NUMERIC_CHECK));
    }

    public function getEventos(){
        $idContrato = $this->input->post('dataValue[idContrato]');
        $idSede = $this->input->post('dataValue[idSede]');
        $idDepto = $this->input->post('dataValue[idDepartamento]');

        $data = $this->EventosModel->getEventos($idContrato, $idSede, $idDepto)->result();

		$rs['result'] = count($data) > 0; 
		if ($rs['result']) {
			$rs['msg'] = '¡Listado de eventos cargado exitosamente!';
			$rs['data'] = $data; 
		}else {
			$rs['msg'] = '¡No existen registros!';
		}

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($rs, JSON_NUMERIC_CHECK));
	}

    public function nuevoEvento() {
        // Obtener y sanitizar inputs
        $titulo            = trim($this->form('titulo'));
        $descripcion       = trim($this->form('descripcion'));
        $fechaEvento       = $this->form('fechaEvento');
        $inicioPublicacion = $this->form('inicioPublicacion');
        $finPublicacion    = $this->form('finPublicacion');
        $limiteRecepcion   = $this->form('limiteRecepcion');
        $ubicacion         = trim($this->form('ubicacion'));
        $sedes             = json_decode($this->form('sedes'), true);
        $departamentos     = json_decode($this->form('departamentos'), true);
        $imagen            = $this->file('imagen');
        $idUsuario         = (int) $this->form('idUsuario');
        $fecha             = date("Y-m-d H:i:s");
    
        // Validar JSON de sedes y departamentos
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sedes) || !is_array($departamentos)) {
            return $this->errorResponse("Error al registrar las sedes y departamentos en el evento");
        }
    
        // Preparar array para el evento
        $evento = [
            "titulo"            => $titulo,
            "descripcion"       => $descripcion,
            "fechaEvento"       => $fechaEvento,
            "inicioPublicacion" => $inicioPublicacion,
            "finPublicacion"    => $finPublicacion,
            "limiteRecepcion"   => $limiteRecepcion,
            "ubicacion"         => $ubicacion,
            "estatusEvento"     => 1,
            "estatus"           => 1,
            "creadoPor"         => $idUsuario,
            "modificadoPor"     => $idUsuario,
            "fechaModificacion" => $fecha,
            "fechaCreacion"     => $fecha,
        ];
    
        // Procesar la imagen (si existe)
        if ($imagen) {
            $file_ext = strtolower(pathinfo($imagen->name, PATHINFO_EXTENSION));
            $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
            if (!in_array($file_ext, $valid_ext)) {
                return $this->errorResponse("Formato de imagen no permitido.");
            }
    
            $file_name = "Evento_" . uniqid() . ".$file_ext";
            if (!$this->upload($imagen->tmp_name, $file_name)) {
                return $this->errorResponse("Error al subir la imagen.");
            }
    
            $evento['imagen'] = $file_name;
        }
    
        // Insertar el evento en la base de datos
        $res = $this->GeneralModel->addRecordReturnId($this->schema_cm . ".eventos", $evento);

        if (!$res) {
            return $this->errorResponse("No se ha podido crear el evento.");
        }
    
        // Preparar las filas de alcanceEvento
        $rows = [];
        foreach ($sedes as $sede) {
            foreach ($departamentos as $departamento) {
                $rows[] = [
                    'idEvento'          => $res,
                    'idDepartamento'    => $departamento['iddepto'],
                    'idSede'            => $sede['idsede'],
                    'estatus'           => 1,
                    'creadoPor'         => $idUsuario,
                    'fechaCreacion'     => $fecha,
                    'modificadoPor'     => $idUsuario,
                    'fechaModificacion' => $fecha,
                ];
            }
        }
    
        // Insertar en alcanceEvento
        $this->GeneralModel->insertBatch($this->schema_cm . '.alcanceEvento', $rows);
    
        // Respuesta de éxito
        return $this->successResponse("Se ha creado el evento.");
    }

    public function actualizarAsistencia() {
        $idContrato    = $this->input->post('dataValue[idContrato]');
        $idEvento      = $this->input->post('dataValue[idEvento]');
        $estatus       = $this->input->post('dataValue[estatusAsistencia]');
        $idUsuario     = $this->input->post('dataValue[idUsuario]');
        $today         = strtotime($this->input->post('dataValue[today]'));
        $eventDate     = strtotime($this->input->post('dataValue[eventDate]')); 
        $flagSuccess = true;

        $this->db->trans_begin();

        $hoy = date('d/M/Y H:i:s', $today);
        $fechaEvento = date('d/M/Y H:i:s', $eventDate);

        if($hoy > $fechaEvento){
            $flagSuccess = false;
        }

        $fecha = date("Y-m-d H:i:s");

        $rs = $this->EventosModel->getAsistenciaEvento($idContrato, $idEvento)->result();

        if ($rs[0]->idAsistenciaEv == NULL){
            $values = [
                "idEvento" => $idEvento,
				"idContrato" => $idContrato,
                "estatusAsistencia" => $estatus,
                "estatus" => $estatus,
				"creadoPor" => $idUsuario,
				"fechaCreacion" => $fecha,
				"modificadoPor" => $idUsuario,
				"fechaModificacion" => $fecha
			];
			$response["result"] = $this->GeneralModel->addRecord( $this->schema_cm.".asistenciasEventos", $values);
        }else {
            $values = [
                "estatusAsistencia" => $estatus,
				"modificadoPor" => $idUsuario,
				"fechaModificacion" => $fecha
			];
            if($flagSuccess){
                $response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".asistenciasEventos", $values, "idAsistenciaEv", $rs[0]->idAsistenciaEv);
            }
            else{
                $response["result"] = false;
            }
        }

        if ($response["result"] && $flagSuccess) {
            $this->db->trans_commit();
            $response["msg"] = "Se ha actualizado tu asistencia de manera exitosa";
        }else {
            $this->db->trans_rollback();
            $response["msg"] = $flagSuccess ? "Surgió un error al actualizar la asistencia del evento" : "Error, el evento ya ha pasado";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
    
    public function actualizarEvento() {
        // Obtener y sanitizar inputs
        $idEvento          = (int) $this->form('idEvento');
        $titulo            = trim($this->form('titulo'));
        $descripcion       = trim($this->form('descripcion'));
        $fechaEvento       = $this->form('fechaEvento');
        $inicioPublicacion = $this->form('inicioPublicacion');
        $finPublicacion    = $this->form('finPublicacion');
        $limiteRecepcion   = $this->form('limiteRecepcion');
        $ubicacion         = trim($this->form('ubicacion'));
        $sedes             = json_decode($this->form('sedes'), true);
        $departamentos     = json_decode($this->form('departamentos'), true);
        $imagen            = $this->file('imagen');
        $idUsuario         = (int) $this->form('idUsuario');
        $fecha             = date("Y-m-d H:i:s");
    
        // Construir el array de valores dinámicamente
        $values = [];
        if ($titulo !== null) $values["titulo"] = $titulo;
        if ($descripcion !== null) $values["descripcion"] = $descripcion;
        if ($fechaEvento !== null) $values["fechaEvento"] = $fechaEvento;
        if ($inicioPublicacion !== null) $values["inicioPublicacion"] = $inicioPublicacion;
        if ($finPublicacion !== null) $values["finPublicacion"] = $finPublicacion;
        if ($limiteRecepcion !== null) $values["limiteRecepcion"] = $limiteRecepcion;
        if ($ubicacion !== null) $values["ubicacion"] = $ubicacion;
        $values["modificadoPor"] = $idUsuario;
        $values["fechaModificacion"] = $fecha;
    
        // Procesar la imagen (si existe)
        if ($imagen) {
            $file_ext = strtolower(pathinfo($imagen->name, PATHINFO_EXTENSION));
            $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
            if (!in_array($file_ext, $valid_ext)) {
                return $this->errorResponse("Formato de imagen no permitido.");
            }
    
            $file_name = "Evento_" . uniqid() . ".$file_ext";
            if (!$this->upload($imagen->tmp_name, $file_name)) {
                return $this->errorResponse("Error al subir la imagen.");
            }
    
            $values['imagen'] = $file_name;
        }
    
        // Actualizar el registro solo con los valores proporcionados
        $response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".eventos", $values, "idEvento", $idEvento);

        if ($response["result"]) {
             // Eliminar registros existentes en alcanceEvento para este evento
            $response["result"] = $this->EventosModel->inhabilitaAlanceEvento($idEvento);

            // Preparar las filas de alcanceEvento
            $rows = [];
            foreach ($sedes as $sede) {
                foreach ($departamentos as $departamento) {
                    $rows[] = [
                        'idEvento'          => $idEvento,
                        'idDepartamento'    => $departamento['iddepto'],
                        'idSede'            => $sede['idsede'],
                        'estatus'           => 1,
                        'creadoPor'         => $idUsuario,
                        'fechaCreacion'     => $fecha,
                        'modificadoPor'     => $idUsuario,
                        'fechaModificacion' => $fecha,
                    ];
                }
            }

            $this->GeneralModel->insertBatch($this->schema_cm . '.alcanceEvento', $rows);
            $response["msg"] = "Se ha actualizado el evento de manera exitosa";
        }else {
            $response["msg"] = "Surgió un error al actualizar el evento";
        }
    
        // Devolver la respuesta como JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
    
    public function ocultarMostrarEvento() {
        $idEvento      = $this->input->post('dataValue[idEvento]');
        $estatus       = $this->input->post('dataValue[estatusEvento]');
        $idUsuario     = $this->input->post('dataValue[idUsuario]');
        
        $resultMessage = $estatus == 1 ? "Se ha activado el evento" : "Se ha desactivado el evento";

        $fecha = date("Y-m-d H:i:s");
       
        $values = [
            "estatusEvento" => $estatus,
			"modificadoPor" => $idUsuario,
			"fechaModificacion" => $fecha
		];

        $response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".eventos", $values, "idEvento", $idEvento);

        if ($response["result"]) {
            $response["msg"] = $resultMessage;
        }else {
            $response["msg"] = "Surgió un error al actualizar la asistencia del evento";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
}