<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class GestorController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('GestorModel');
		$this->load->model('GeneralModel');
		$this->ch = $this->load->database('ch', TRUE);
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

    public function getOficinasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getOficinasVal($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getEspecialistasVal(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getEspecialistasVal($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getSedeNone(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getSedeNone($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getSedeNoneEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getSedeNoneEsp($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function insertAtxSede(){
		$dt = $this->input->post('dataValue', true);

		$dt2 = json_decode($dt, true);
		$idArea = $dt2["idArea"] == 0 ? null : $dt2["idArea"];

		if(is_null($idArea))
			$checkAxs = $this->GestorModel->checkAxsNull($dt2);
		else
			$checkAxs = $this->GestorModel->checkAxs($dt2, $idArea);
		
        $data = [
			'idEspecialista' => $dt2["especialista"],
			'idSede'         => $dt2["sede"],
			'idOficina'      => $dt2["oficina"],
			'idArea'         => $idArea,
		    'tipoCita'       => $dt2["modalidad"],
			'estatus'        => 1,
			'creadoPor'      => $dt2["usuario"],
			'fechaCreacion'  => date('Y-m-d h:i:s'),
 			'modificadoPor'  => $dt2["usuario"],
			'fechaModificacion'  => date('Y-m-d h:i:s')
		];

		if($checkAxs->num_rows() > 0){
			$response["result"] = false;
			$response["msg"] = 'La atención por sede ya ha sido asignada anteriormente';
		}
		else{
			$data = $this->GeneralModel->addRecord( $this->schema_cm.".atencionxsede", $data);

			if($data){
				$response["result"] = true;
				$response["msg"] = 'Se registrado la atención por sede';
			}
			else{
				$response["result"] = false;
				$response["msg"] = 'No se ha guardado el registro';
			}
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateModalidad(){

		$idAts = $this->input->post('dataValue[idDetallePnt]');
		$modalidad= $this->input->post('dataValue[modalidad]');

		$getAxs = $this->GestorModel->getAxs($idAts);

		$checkData = [
			"especialista" => $getAxs->result()[0]->idEspecialista,
			"sede"         => $getAxs->result()[0]->idSede,
			"oficina"      => $getAxs->result()[0]->idOficina,
			"idArea"       => $getAxs->result()[0]->idArea,
			"modalidad"    => intval($modalidad)
		];

		if(is_null($checkData["idArea"]))
			$checkAxs = $this->GestorModel->checkAxsNull($checkData);
		else
			$checkAxs = $this->GestorModel->checkAxs($checkData, $checkData["idArea"]);

		if($checkAxs->num_rows() > 0){
			$response["result"] = false;
			$response["msg"] = 'La atención por sede ya ha sido asignada anteriormente';
		}
		else{
			$data = array(
				"tipoCita" => $modalidad
			);

			$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm .'.atencionxsede', $data, 'idAtencionXSede', $idAts);

			if($updateRecord){
				$response["result"] = true;
				$response["msg"] = 'Se ha actualizado la atención por sede';
			}
			else{
				$response["result"] = false;
				$response["msg"] = 'Error al actualizar la atención por sede';
			}
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateEspecialista(){

		$id= $this->input->post('dataValue[idDetallePnt]');
		$idEspe= $this->input->post('dataValue[espe]');

		$data = array(
			"idEspecialista" => $idEspe,
		);

		$this->GeneralModel->updateRecord($this->schema_cm.'.atencionxsede', $data, 'idAtencionXSede', $id);
		echo json_encode(array("estatus" => true, "msj" => "Estatus actualizado" ));
				
	}

	public function updateOficina(){

		$idOficina= $this->input->post('dataValue[idOfi]');
		$oficina= $this->input->post('dataValue[ofi]');
		$idSede= $this->input->post('dataValue[idSede]');
		$ubicacion= $this->input->post('dataValue[ubi]');
		$estatus= $this->input->post('dataValue[estatus]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		$data = array(
			"oficina" => $oficina,
			"idSede" => $idSede,	
			"ubicación" => $ubicacion,
			"estatus" => $estatus,
			"modificadoPor" => $modificadoPor,
		);

		$this->GeneralModel->updateRecord($this->schema_cm .'.oficinas', $data, 'idOficina', $idOficina);
		echo json_encode(array("estatus" => true, "msj" => "Datos actualizados" ));
				
	}

	public function updateSede(){

		$idSede= $this->input->post('dataValue[idSed]');
		$sede= $this->input->post('dataValue[sede]');
		$abreviacion= $this->input->post('dataValue[abreviacion]');
		$estatus= $this->input->post('dataValue[estatus]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		$data = array(
			"sede" => $sede,
			"abreviacion" => $abreviacion,
			"estatus" => $estatus,
			"modificadoPor" => $modificadoPor,
		);

		$this->GeneralModel->updateRecord($this->schema_cm .'.sedes', $data, 'idSede', $idSede);
		echo json_encode(array("estatus" => true, "msj" => "Datos actualizados" ));
				
	}

	public function getEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getEsp($dt);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getAtencionXsedeEsp(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getAtencionXsedeEsp($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getOficinas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getOficinas($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
 
	public function insertOficinas(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertOficinas($dt);
	}

	public function insertSedes(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertSedes($dt);
	}

	public function checkModalidades(){
		$dataValue = $this->input->post('dataValue', true);

		if($dataValue["idArea"] == 0)
			$check = $this->GestorModel->checkModalidadesNull($dataValue);
		else
			$check = $this->GestorModel->checkModalidades($dataValue);

		if($check->num_rows() > 0){
			$response["result"] = false;
			$response["msg"] = 'La oficina ya tiene asignada sus modalidades para este especialista';
		}
		else{
			$response["result"] = true;
			$response["msg"] = 'Modalidades disponibles';
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function getAreas(){
		$get = $this->GestorModel->getAreas();

		if($get->num_rows() > 0){
			$response["data"] = $get->result();
			$response["result"] = true;
			$response["msg"] = "Se han encontrado registros de áreas";
		}
		else{
			$response["data"] = [];
			$response["result"] = false;
			$response["msg"] = "No hay registros de áreas";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function updateArea(){
		$idAts = $this->input->post('dataValue[idDetallePnt]');
		$idArea = $this->input->post('dataValue[idArea]');
		$idAreaInsert = $idArea == 0 ? null : $idArea;

		$getAxs = $this->GestorModel->getAxs($idAts);

		$checkData = [
			"especialista" => $getAxs->result()[0]->idEspecialista,
			"sede"         => $getAxs->result()[0]->idSede,
			"oficina"      => $getAxs->result()[0]->idOficina,
			"idArea"       => $idArea,
			"modalidad"    => $getAxs->result()[0]->tipoCita
		];

		$checkAxs = $this->GestorModel->checkAxsId($checkData, $checkData["idArea"], $idAts);

		$checkAxsArea = $this->GestorModel->checkAxsArea($checkData, $idAts);

		if($checkAxs->num_rows() > 0){
			$response["result"] = false;
			$response["msg"] = 'La atención por sede ya ha sido asignada anteriormente';
		}
		else if($idArea == '0' && $checkAxsArea->num_rows() > 0 ){
			$response["result"] = false;
			$response["msg"] = 'No puede haber más atenciónes con esa área';
		}
		else{
			$data = array(
				"idArea" => $idAreaInsert
			);

			$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.atencionxsede', $data, 'idAtencionXSede', $idAts);

			if($updateRecord){
				$response["result"] = true;
				$response["msg"] = 'Se ha actualizado la atención por sede';
			}
			else{
				$response["result"] = false;
				$response["msg"] = 'Error al actualizar la atención por sede';
			}
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateEstatus(){
		$dataValue = $this->input->post("dataValue", true);
		$id = $dataValue["idDetallePnt"];

		$data = [
			"estatus" => intval($dataValue["estatus"])
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.".atencionxsede", $data, "idAtencionXSede", $id);

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

	public function getHorariosEspecificos(){

		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getHorariosEspecificos($dt)->result();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function updateHorario(){

		$idHorario= $this->input->post('dataValue[id]');
		$horaInicio= $this->input->post('dataValue[horaInicio]');
		$horaFin= $this->input->post('dataValue[horaFin]');
		$sabado= $this->input->post('dataValue[sabado]');
		$horaInicioSabado= $this->input->post('dataValue[horaInicioSabado]');
		$horaFinSabado= $this->input->post('dataValue[horaFinSabado]');
		$modificadoPor= $this->input->post('dataValue[modificadoPor]');

		$data = array(
			"idHorario" => $idHorario,
			"horaInicio" => $horaInicio,
			"horaFin" => $horaFin,		
			"sabados" => $sabado,
			"horaInicioSabado" => $horaInicioSabado,
			"horaFinSabado" => $horaFinSabado,
			"modificadoPor" => $modificadoPor,
		);

		$this->GeneralModel->updateRecord($this->schema_cm.'.horariosespecificos', $data, 'idHorario', $idHorario);
		echo json_encode(array("estatus" => true, "msj" => "Horario actualizado" ));
				
	}

	public function updateEstatusHorario(){
		$dataValue = $this->input->post("dataValue", true);
		$id = $dataValue["idHorario"];

		$data = [
			"estatus" => intval($dataValue["estatus"])
		];

		$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.horariosespecificos', $data, "idHorario", $id);

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

	public function insertHorario(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->insertHorario($dt);
	}

	public function especialistas(){
		$data['data'] = $this->GestorModel->especialistas();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getDepartamentos(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getDepartamentos($dt);

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getAreasPs(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getAreasPs($dt);

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getPuestos(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->GestorModel->getPuestos($dt);

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function getAllAreas(){
		$data['data'] = $this->GestorModel->getAllAreas()->result();

		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function updateEstatusPuestos(){
		$dataValue = $this->input->post("dataValue", true);

		$id = $dataValue["id"];
		$idUser = $dataValue["idUser"];
		$estatus = $dataValue["estatus"];

		switch ($estatus) {
			case 1:
				$data = [
					"canRegister" => 0
				];

				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $id);

				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
			case 0:
				$data = [
					"canRegister" => 1
				];

				$query = $this->db->query("SELECT * FROM ". $this->schema_cm .".datopuesto WHERE idPuesto = $id");

				if ($query->num_rows() == 0) {

					$this->ch->query("INSERT INTO ". $this->schema_cm .".datopuesto (idPuesto, canRegister, estatus, creadoPor, modificadoPor ) 
					VALUES (?, ?, ?, ?, ?)", 
					array($id, 1, 1, $idUser, $idUser ));
				}

				$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $id);
				
				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateEstatusAreas(){
		$dataValue = $this->input->post("dataValue", true);

		$id = $dataValue["id"];
		$idUser = $dataValue["idUser"];
		$estatus = $dataValue["estatus"];

		switch ($estatus) {
			case 1:
				$data = [
					"canRegister" => 0
				];

				$query = $this->db->query("SELECT ps.idpuesto 
					FROM ". $this->schema_ch .".beneficioscm_vista_area ar
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
					WHERE ar.idsubarea = $id");

				$idPuesto = 0;
				if ($query->num_rows() > 0) {

					foreach ($query->result() as $row) {
						$idPuesto = $row->idpuesto;

						$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $idPuesto);
					}

				}

				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
			case 0:
				$data = [
					"canRegister" => 1
				];

				$query = $this->db->query("SELECT ps.idpuesto 
					FROM ". $this->schema_ch .".beneficioscm_vista_area ar
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
					WHERE ar.idsubarea = $id");

				$idPuesto = 0;
				if ($query->num_rows() > 0) {

					foreach ($query->result() as $row) {
						$idPuesto = $row->idpuesto;

						$query_puestos = $this->db->query("SELECT * FROM ". $this->schema_cm .".datopuesto WHERE idPuesto = $idPuesto");

						if ($query_puestos->num_rows() == 0) {

							$this->ch->query("INSERT INTO ". $this->schema_cm .".datopuesto (idPuesto, canRegister, estatus, creadoPor, modificadoPor ) 
							VALUES (?, ?, ?, ?, ?)", 
							array($idPuesto, 1, 1, $idUser, $idUser ));
						}

						$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $idPuesto);
					}

				}
				
				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function updateEstatusDepartamentos(){
		$dataValue = $this->input->post("dataValue", true);

		$id = $dataValue["id"];
		$idUser = $dataValue["idUser"];
		$estatus = $dataValue["estatus"];

		switch ($estatus) {
			case 1:
				$data = [
					"canRegister" => 0
				];

				$query = $this->db->query("SELECT ps.idpuesto FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
				WHERE dep.iddepto = $id");

				$idPuesto = 0;
				if ($query->num_rows() > 0) {

					foreach ($query->result() as $row) {
						$idPuesto = $row->idpuesto;

						$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $idPuesto);
					}

				}

				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
			case 0:
				$data = [
					"canRegister" => 1
				];

				$query = $this->db->query("SELECT ps.idpuesto FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
				WHERE dep.iddepto = $id");

				$idPuesto = 0;
				if ($query->num_rows() > 0) {

					foreach ($query->result() as $row) {
						$idPuesto = $row->idpuesto;

						$query_puestos = $this->db->query("SELECT * FROM ". $this->schema_cm .".datopuesto WHERE idPuesto = $idPuesto");

						if ($query_puestos->num_rows() == 0) {

							$this->ch->query("INSERT INTO ". $this->schema_cm .".datopuesto (idPuesto, canRegister, estatus, creadoPor, modificadoPor ) 
							VALUES (?, ?, ?, ?, ?)", 
							array($idPuesto, 1, 1, $idUser, $idUser ));
						}

						$updateRecord = $this->GeneralModel->updateRecord($this->schema_cm.'.datopuesto', $data, "idPuesto", $idPuesto);
					}

				}
				
				if($updateRecord){
					$response["result"] = true;
					$response["msg"] = "Estatus actualizado";
				}
				else{
					$response["result"] = false;
					$response["msg"] = "Error al actualizar";
				}

				break;
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}

	public function getUsuarios(){

		$data['data'] = $this->GestorModel->getUsuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);

	}

	public function getPermisosOptions(){
		$options = $this->GeneralModel->getPermisosOptions();

		$this->json(['data' => $options]);
	}

	public function savePermisos(){
		$data = $this->input->post('dataValue');

		if($data['permisos_id']){
			$permisos = $data['permisos_id'];
		}else{
			$permisos = 'null';
		}

		$is_ok = $this->GestorModel->updatePermisosUsuarios($data['id'], $permisos);

		if($is_ok){
			$response["status"] = 'success';
			$response["msg"] = "Usuario guardado";
		}else{
			$response["status"] = 'error';
			$response["msg"] = "Error al guardar los datos";
		}

		$this->json($response);
	}
}
