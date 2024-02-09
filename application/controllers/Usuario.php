<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Usuario extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('UsuariosModel');
		$this->load->model('GeneralModel');
		$this->load->model('menuModel');

		$this->load->helper(array('form','funciones'));
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function get_token(){
		$headers = (object) $this->input->request_headers();
		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		$code = $this->post('code');

		$access_token = $this->googleapi->getAccessToken($code);

		$this->usuariosModel->updateRefreshToken($user->idUsuario, $access_token->refresh_token);
	}

	public function authorized(){
		/*
		$token = $this->headers('Token');
		$session = $this->token->validateToken($token);

		$user = (object) $session['data'];
		*/

		$headers = (object) $this->input->request_headers();

		//print_r($headers);
		//exit();

		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		//print_r($user);
		//exit();

		$path = substr($this->input->get('path'), 1);

		//print_r($path);
		//exit();

		$id_user = intval($user->idUsuario);
		$id_rol = intval($user->idRol);

		$auth = $this->menuModel->checkAuth($path, $id_user, $id_rol);

		//print_r($auth);
		//exit();

		$result = [
			"idRol" => $id_rol,
			"idUsuario" => $id_user,
			"authorized" => $auth,
		];
    
		$this->json($result);
	}

	public function login(){
		$response = [
			'message' => 'Error',
			'result' => 0
		];

		$data = $this->post();
		
		if(!(isset($data->password) || isset($data->numempleado))){
			$response['message'] = 'Faltan datos';

			$this->json($response);
		}

		$data->password = encriptar($data->password);

		$user = $this->UsuariosModel->login($data->numempleado, $data->password);

		if(!$user){
			$response['message'] = 'El empleado no se encuentra registrado';

			$this->json($response);
		}

		$session = array(
			'idUsuario'		=>	$user->idUsuario,
			'idRol'			=>	$user->idRol,
			'numEmpleado'	=>	$user->numEmpleado,
			'numContrato'	=>	$user->numContrato,
			'nombre'		=>	$user->nombre,
			'telPersonal'	=>	$user->telPersonal,
			'puesto'		=>	$user->puesto,
			'oficina'		=>	$user->oficina,
			'sede'			=>	$user->idSede,
			'correo'		=>	$user->correo,
			'idArea'		=>	$user->idArea,
		);

		$token = $this->token->generateToken($session);

		if($token){
			$response['token'] = $token;
			$response['message'] = 'ok';
			$response['result'] = 1;
		}

		$this->json($response);
	}

	public function menu()
	{
		$headers = (object) $this->input->request_headers();
		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		$id_user = intval($user->idUsuario);
		$id_rol = intval($user->idRol);

		echo json_encode($this->menuModel->getMenu($id_user, $id_rol), JSON_NUMERIC_CHECK);
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getUsers(){
		$rs = $this->UsuariosModel->getUsers();
		$data['result'] = count($rs) > 0; 
		if ($data['result']) {
			$data['msg'] = '¡Listado de usuarios cargado exitosamente!';
			$data['data'] = $rs; 
		}else {
			$data['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
	
	public function getUsersExternos(){
		$rs = $this->UsuariosModel->getUsersExternos()->result();
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
		$rs = $this->UsuariosModel->getAreas();
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

	public function insertBatchUsers()
    {
        $table = $this->input->post('dataValue[tabla]');
        $data  = $this->input->post('dataValue[data]');
    
        $response['result'] = isset($table, $data); // && !empty($data);
        if ($response['result']) {
            $fecha = date('Y-m-d H:i:s');
            $complemento = date('Ymd');
            $rows = array();
            foreach ($data as $user) {
                $iniciales = getIniciales($user['nombre']); // 
                $row = array(
                    'numContrato' => $iniciales.$complemento,
                    'numEmpleado' => $iniciales.$complemento,
                    'nombre' => $user['nombre'],
                    'telPersonal' => isset($user['telPersonal']) ? $user['telPersonal'] : null,
                    'idArea' => null,
					'idPuesto' => null,
                    'idSede' => null,
					'sexo' => $user['sexo'],
					'externo' => 1,
					'idRol' => 2,
                    'correo' => isset($user['correo']) ? $user['correo'] : null,
                    'password' => encriptar('Tempo01@'),
                    'estatus' => 1,
					'creadoPor' => $user['creadoPor'],
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => $user['creadoPor'],
                    'fechaModificacion' => $fecha,
                );
                $rows[] = $row;
            }
        
            $response['result'] = $this->GeneralModel->insertBatch($table, $rows);
            
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

	public function updateUser() {
		$fecha = date('Y-m-d H:i:s');
		$user = $this->input->post('dataValue[idUsuario]');
		
		$data = array();
	
		foreach ($this->input->post('dataValue') as $key => $value) {
			if (($value !== null || $value !== '') && $key != 'idUsuario') {
				$data[$key] = $value;
			}
		}

		$response['result'] = isset($user, $data) && !empty($user);
		
		if ($response['result']) {  
			$data['fechaModificacion'] = $fecha;
			$response['result'] = $this->GeneralModel->updateRecord('usuarios', $data, 'idUsuario', $user);
	
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

	public function getNameUser(){
		$idEspecialista = $this->input->post("dataValue", true);

		$getNameUser = $this->UsuariosModel->getNameUser($idEspecialista)->result();
		$response['result'] = count($getNameUser) > 0;
		if ($response['result']) {
			$response['msg'] = '¡Listado de usuarios cargado exitosamente!';
			$response['data'] = $getNameUser;
		}else {
			$response['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
	}

	public function decodePass(){

		$dt = $this->input->post('dataValue', true);

		$data['data'] = $this->UsuariosModel->decodePass($dt);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function updatePass(){

		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$password = $this->input->post('dataValue[password]');
		$newPass= $this->input->post('dataValue[newPassword]');

			if(!empty($newPass))
			{
				$data = array(
					"password" => encriptar($newPass),
				);
				
				$response=$this->GeneralModel->updateRecord('usuarios', $data, 'idUsuario', $idUsuario);
				echo json_encode(array("estatus" => true, "msj" => "Contraseña actualizada!" ), JSON_NUMERIC_CHECK);
					
			}else{
				echo json_encode(array("estatus" => false, "msj" => "Error en actualizar contraseña"));
			}	
	}
}
