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
		$this->load->model('MenuModel');

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

		$this->UsuariosModel->updateRefreshToken($user->idUsuario, $access_token->refresh_token);
	}

	public function menu()
	{
		$headers = (object) $this->input->request_headers();

		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		$id_user = intval($user->idUsuario);
		$id_rol = intval($user->idRol);

		echo json_encode($this->MenuModel->getMenu($id_user, $id_rol));
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

		$auth = $this->MenuModel->checkAuth($path, $id_user, $id_rol);

		//print_r($auth);
		//exit();

		$result = [
			"idRol" => $id_rol,
			"idUsuario" => $id_user,
			"authorized" => $auth,
		];

		echo json_encode($result);
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data);
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
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $table = $params['nombreTabla'];
        $data = $params['data'];
    
        $response['result'] = isset($table, $data) && !empty($data);
        if ($response['result']) {
            $fecha = date('Y-m-d H:i:s');
            $complemento = date('Ymd');
            $rows = array();
            foreach ($data as $col) {
                $iniciales = getIniciales($col['nombre']); // 
                $row = array(
                    'numContrato' => isset($col['nombre']) ? $iniciales.$complemento : null,
                    'numEmpleado' => isset($col['nombre']) ? $iniciales.$complemento : null,
                    'nombre' => isset($col['nombre']) ? $col['nombre'] : null,
                    'telPersonal' => isset($col['telPersonal']) ? $col['telPersonal'] : null,
                    'area' => isset($col['area']) ? $col['area'] : null,
					'idPuesto' => isset($col['idPuesto']) ? $col['idPuesto'] : null,
                    'oficina' => isset($col['oficina']) ? $col['oficina'] : null,
                    'sede' => isset($col['sede']) ? $col['sede'] : null,
                    'correo' => isset($col['correo']) ? $col['correo'] : null,
                    'password' => isset($col['password']) ? encriptar($col['password']) : encriptar('Tempo01@'),
                    'estatus' => 1,
					'creadoPor' => 1,
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => 1,
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
		echo json_encode($data);
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
				echo json_encode(array("estatus" => true, "msj" => "Contraseña actualizada!" ));
					
			}else{
				echo json_encode(array("estatus" => false, "msj" => "Error en actualizar contraseña"));
			}	
	}

	public function updateUserByCH() {
        $encodedUserData = file_get_contents('php://input'); // Obtener el cuerpo de la solicitud en formato base64
        $decodedUserData = base64_decode($encodedUserData); // Decodificar los datos base64
        $userData = json_decode($decodedUserData, true);  // Procesar los datos como desees, por ejemplo, decodificar JSON

		$fecha = date('Y-m-d H:i:s');
		foreach ($userData as $key => $value) {
			if (($value !== null || $value !== '') && $key != 'idContrato' && $key != 'idUsuario' ) {
				$data[$key] = $value;
			}
		}

		isset($data["nombre_persona"]) ? $userD['nombre'] = ($data["nombre_persona"] . ' ' . $data["pri_apellido"] . ' ' . $data["sec_apellido"]) : '';
		isset($data["telefono_personal"]) ? $userD['telPersonal'] = $data["telefono_personal"] : '';
		isset($data["idpuesto"]) ? $userD['idPuesto'] = intval($data["idpuesto"]) : '';
		isset($data["idsede"]) ? $userD['idSede'] = intval($data["idsede"]) : '';
		isset($data["mail_emp"]) ? $userD['correo'] = $data["mail_emp"] : '';
		isset($data["activo"]) ? $userD['estatus'] = $data["activo"] : '';
		$userD["fechaModificacion"] = $fecha;
		isset($data["idarea"]) ? $userD['idArea'] = $data["idarea"] : '';
		isset($data["sexo"]) ? $userD['sexo'] = $data["sexo"] : '';
		isset($data["fingreso"]) ? $userD['fechaIngreso'] = $data["fingreso"] : '';

		$idContrato = intval($data["idcontrato"]);

		$response['result'] = isset($idContrato);
		if ($response['result']) {
			$data['fechaModificacion'] = $fecha;
			$response['result'] = $this->GeneralModel->updateRecord('usuarios', $userD, 'idContrato', $idContrato);
			if ($response['result']) {
				$response['msg'] = "¡Usuario actualizado exitosamente!";
			} else {
				$response['msg'] = "¡Error al intentar actualizar los datos de usuario!";
			}
		} else {
			$response['msg'] = "¡Parámetros inválidos!";
		}

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($response));
	}
}
