<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');

		date_default_timezone_set('America/Mexico_City');

		$urls = array('192.168.30.128/auth/jwt/login','localhost','http://localhost','http://localhost:3030','http://192.168.30.128/auth/jwt/login','192.168.30.128','http://192.168.30.128:3030','127.0.0.1','https://rh.gphsis.com','rh.gphsis.com','https://maderascrm.gphsis.com','maderascrm.gphsis.com');
        date_default_timezone_set('America/Mexico_City');

        if(isset($this->input->request_headers()['origin']))
            $origin = $this->input->request_headers()['origin'];
        else if(array_key_exists('HTTP_ORIGIN',$_SERVER))
            $origin = $_SERVER['HTTP_ORIGIN'];
        else if(array_key_exists('HTTP_PREFERER',$_SERVER))
            $origin = $_SERVER['HTTP_PREFERER'];
        else
            $origin = $_SERVER['HTTP_HOST'];

        if(in_array($origin,$urls) || strpos($origin,"192.168")) {
			$this->load->database('default');
			$this->load->model('usuariosModel');
			$this->load->model('generalModel');

            $this->load->helper(array('form','funciones'));
			$this->load->library(array('session'));
        } else {
            die ("Access Denied");     
            exit;  
        }
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
					'puesto' => isset($col['puesto']) ? $col['puesto'] : null,
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
        
            $response['result'] = $this->generalModel->insertBatch($table, $rows);
            
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

	public function getNameUser(){
		$idEspecialista = $this->input->post("dataValue", true);

		$rs = $this->usuariosModel->getNameUser($idEspecialista);
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
}
