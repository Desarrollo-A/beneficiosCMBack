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

    public function nuevoEvento(){
        $titulo            = $this->form('titulo');
        $descripcion       = $this->form('descripcion');
        $fechaEvento       = $this->form('fechaEvento');
        $inicioPublicacion = $this->form('inicioPublicacion');
        $finPublicacion    = $this->form('finPublicacion');
        $limiteRecepcion   = $this->form('limiteRecepcion');
        $ubicacion         = $this->form('ubicacion');
        $imagen            = $this->file('imagen');
        $idUsuario         = $this->form('idUsuario');

        // var_dump($imagen);
        $fecha = date("Y-m-d H:i:s");

        $upload = false;
        if($imagen){
            $file_ext = pathinfo($imagen->name, PATHINFO_EXTENSION);
            // $file_name =  "Evento_".$titulo."_".$fechaEvento.".".$file_ext."";
            $file_name =  "Evento_$titulo.$file_ext";
            // $file_name =  "justificacion-$idCita.$file_ext";

            $upload = $this->upload($imagen->tmp_name, $file_name);

            if($upload){
                $evento['imagen'] = $file_name;
            }
        }

        $evento = [
			"titulo" => $titulo,
			"descripcion" => $descripcion,
			"fechaEvento" => $fechaEvento,
            "inicioPublicacion" => $inicioPublicacion,
            "finPublicacion" => $finPublicacion,
            "limiteRecepcion" => $limiteRecepcion,
            "ubicacion" => $ubicacion,
			"creadoPor" => $idUsuario,
			"modificadoPor" => $idUsuario,
			"fechaModificacion" => $fecha,
			"fechaCreacion" => $fecha,
		];

        if($upload){
            $rs["result"] = $this->GeneralModel->addRecord( $this->schema_cm.".eventos", $evento);
        }else {
            $rs["result"] = false;
        }

		if ($rs["result"]) {
			$response["msg"] = "Se ha creado el evento";
		} else {
			$rs["msg"] = "No se ha podído crear el evento";
		}

		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($upload, JSON_NUMERIC_CHECK));
	}
}