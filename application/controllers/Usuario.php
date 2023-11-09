<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');
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
		$user = $_POST['idUsuario'];
		$data = array(
			"nombre" => $_POST['nombre'],
			"telPersonal" => $_POST['telPersonal'],
			"area" => $_POST['area'],
			"oficina" => $_POST['oficina'],
			"sede" => $_POST['sede'],
			"correo" => $_POST['correo'],
			"estatus" => $_POST['estatus'],
			"fecha_modificacion" => $_POST['fecha_modificacion'],
		);
		$response['result'] = isset($user, $data) && !empty($user);
        if ($response['result']) {  
            $response['result'] = $this->generalModel->updateRecord($table, $data, 'usuarios', $user);
            
            if ($response['result']) {
                $response['msg'] = "¡Listado insertado exitosamente!";
            } else {
                $response['msg'] = "¡No se ha podido insertar los datos!";
            }
        } else {
            $response['msg'] = "¡Parametros invalidos!";
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}
}
