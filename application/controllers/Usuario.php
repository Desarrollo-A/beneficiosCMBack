<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		$this->load->model('generalModel');
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');

		date_default_timezone_set('America/Mexico_City');
	}
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}

	public function getUsers(){
		$rs = $this->usuariosModel->getUsers();
		$data['result'] = count($rs) > 0; 
		if ($data['result']) {
			$data['msg'] = '¡Listado de usuarios cargado exitosamente!';
			$data['data'] = $rs; 
		}else {
			$data['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));
	}

	public function getAreas(){
		$rs = $this->usuariosModel->getAreas();
		$data['result'] = count($rs) > 0; 
		if ($data['result']) {
			$data['msg'] = '¡Listado de areas cargado exitosamente!';
			$data['data'] = $rs; 
		}else {
			$data['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));
	}

	public function updateUser() {
		$fecha = date('Y-m-d H:i:s');
		$user = $this->input->post('idUsuario');
		
		$data = array();
	
		// Recorre $_POST y agrega los campos con valores (incluido 0) al array $data
		foreach ($this->input->post() as $key => $value) {
			if (($value !== null || $value !== '') && $key != 'idUsuario') {
				$data[$key] = $value;
			}
		}

		$response['result'] = isset($user, $data) && !empty($user);
		
		if ($response['result']) {  
			$data['fechaModificacion'] = $fecha;
			$response['result'] = $this->generalModel->updateRecord('usuarios', $data, 'idUsuario', $user);
	
			if ($response['result']) {
				$response['msg'] = "¡Usuario actualizado exitosamente!";
			} else {
				$response['msg'] = "¡Error al intentar actualizar datos de usuario!";
			}
		} else {
			$response['msg'] = "¡Parametros invalidos!";
		}
	
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}
}
