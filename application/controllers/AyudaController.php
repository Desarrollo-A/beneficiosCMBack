<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class AyudaController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->load->model('AyudaModel');
        $this->load->model('GeneralModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function getAllFaqs(){
        $data['data'] = $this->AyudaModel->getAllFaqs();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getFaqs(){
        $data['data'] = $this->AyudaModel->getFaqs();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getAllManuales(){
        $data['data'] = $this->AyudaModel->getAllManuales();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getManuales(){
        $data['data'] = $this->AyudaModel->getManuales();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function createManuales(){

        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $icono = isset($_POST['icono']) ? trim($_POST['icono']) : null;
        $video = isset($_POST['video']) ? trim($_POST['video']) : null;
        $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;
        $modificadoPor = isset($_POST['modificadoPor']) ? trim($_POST['modificadoPor']) : null;

        // Verificación de campos obligatorios
        if (empty($titulo) || empty($descripcion) || empty($modificadoPor)) {
            echo json_encode(array("estatus" => false, "msj" => "Hay datos vacios"), JSON_NUMERIC_CHECK);
        }

        $data = [
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "icono" => $icono,
            "video" => $video,
            "idRol" => $rol,
            "modificadoPor" => $modificadoPor
        ];

        $insert = $this->GeneralModel->insertBatch($this->schema_cm . ".manuales", [$data]);

        if ($insert) {
            echo json_encode(array("estatus" => true, "msj" => "Registro exitoso"), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Error al registrar"), JSON_NUMERIC_CHECK);
        }			
	}

    public function updateManuales(){

        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $icono = isset($_POST['icono']) ? trim($_POST['icono']) : null;
        $video = isset($_POST['video']) ? trim($_POST['video']) : null;
        $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;
        $modificadoPor = isset($_POST['modificadoPor']) ? trim($_POST['modificadoPor']) : null;

        // Verificación de campos obligatorios
        if (empty($titulo) || empty($descripcion) || empty($modificadoPor)) {
            echo json_encode(array("estatus" => false, "msj" => "Hay datos vacios"), JSON_NUMERIC_CHECK);
        }

        $data = [
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "icono" => $icono,
            "video" => $video,
            "idRol" => $rol,
            "modificadoPor" => $modificadoPor
        ];

        $update["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".manuales", $data, 'idManual', $id);

        if ($update) {
            echo json_encode(array("estatus" => true, "msj" => "Datos actualizados"), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Error al actualizar datos"), JSON_NUMERIC_CHECK);
        }			
	}

    public function updateEstatusManual(){
		$dataValue = $this->input->post("dataValue", true);
		$id = $dataValue["id"];

		$data = [
			"estatus" => intval($dataValue["estatus"]),
            "modificadoPor" => intval($dataValue["modificadoPor"])
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.manuales', $data, "idManual", $id);

		if($updateRecord){
			$response["result"] = true;
			$response["msg"] = "Se ha actualizado el estatus";
		}
		else{
			$response["result"] = false;
			$response["msg"] = "Ha ocurrido un error al actualizar";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

    public function createFaqs(){

        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;
        $modificadoPor = isset($_POST['modificadoPor']) ? trim($_POST['modificadoPor']) : null;

        // Verificación de campos obligatorios
        if (empty($titulo) || empty($descripcion) || empty($modificadoPor)) {
            echo json_encode(array("estatus" => false, "msj" => "Hay datos vacios"), JSON_NUMERIC_CHECK);
        }

        $data = [
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "idRol" => $rol,
            "modificadoPor" => $modificadoPor
        ];

        $insert = $this->GeneralModel->insertBatch($this->schema_cm . ".faqs", [$data]);

        if ($insert) {
            echo json_encode(array("estatus" => true, "msj" => "Registro exitoso"), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Error al registrar"), JSON_NUMERIC_CHECK);
        }			
	}

    public function updateFaqs(){

        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;
        $modificadoPor = isset($_POST['modificadoPor']) ? trim($_POST['modificadoPor']) : null;

        // Verificación de campos obligatorios
        if (empty($titulo) || empty($descripcion) || empty($modificadoPor)) {
            echo json_encode(array("estatus" => false, "msj" => "Hay datos vacios"), JSON_NUMERIC_CHECK);
        }

        $data = [
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "idRol" => $rol,
            "modificadoPor" => $modificadoPor
        ];

        $update["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".faqs", $data, 'idFaqs', $id);

        if ($update) {
            echo json_encode(array("estatus" => true, "msj" => "Datos actualizados"), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array("estatus" => false, "msj" => "Error al actualizar datos"), JSON_NUMERIC_CHECK);
        }			
	}

    public function updateEstatusFaq(){
		$dataValue = $this->input->post("dataValue", true);
		$id = $dataValue["id"];

		$data = [
			"estatus" => intval($dataValue["estatus"]),
            "modificadoPor" => intval($dataValue["modificadoPor"])
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.faqs', $data, "idFaqs", $id);

		if($updateRecord){
			$response["result"] = true;
			$response["msg"] = "Se ha actualizado el estatus";
		}
		else{
			$response["result"] = false;
			$response["msg"] = "Ha ocurrido un error al actualizar";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
}

?>