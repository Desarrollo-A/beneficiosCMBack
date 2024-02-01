<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Usuario extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		$this->load->model('generalModel');
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
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
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
        $this->output->set_output(json_encode($data), JSON_NUMERIC_CHECK);
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
        $this->output->set_output(json_encode($data), JSON_NUMERIC_CHECK);
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
        $this->output->set_output(json_encode($response), JSON_NUMERIC_CHECK);
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
		$this->output->set_output(json_encode($response), JSON_NUMERIC_CHECK);
	}

	public function getNameUser(){
		$idEspecialista = $this->input->post("dataValue", true);

		$getNameUser = $this->usuariosModel->getNameUser($idEspecialista)->result();
		$response['result'] = count($getNameUser) > 0;
		if ($response['result']) {
			$response['msg'] = '¡Listado de usuarios cargado exitosamente!';
			$response['data'] = $getNameUser;
		}else {
			$response['msg'] = '¡No existen registros!';
		}
		$this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response), JSON_NUMERIC_CHECK);
	}

	public function decodePass(){

		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->usuariosModel->decodePass($dt);
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
				
				$response=$this->generalModel->updateRecord('usuarios', $data, 'idUsuario', $idUsuario);
				echo json_encode(array("estatus" => true, "msj" => "Contraseña actualizada!" ), JSON_NUMERIC_CHECK);
					
			}else{
				echo json_encode(array("estatus" => false, "msj" => "Error en actualizar contraseña"), JSON_NUMERIC_CHECK);
			}	
	}

	//////////////////////
	// START GOOGLE API//
	/////////////////////

	// public function test(){

    //     $email = "programador.analista40@ciudadmaderas.com";

    //     $this->googleapi->getAccessToken($email);
        
    //     $data = json_encode(array(
    //         'summary' =>'reunion nutriologo',
    //         'location' => 'avenidad el sauz 621 colonia los pinos, celaya guanajuato',
    //         'description' => ' cita con el nutriologo perez para bajarte la panza',
    //         'start' => array(
    //             'dateTime' => '2024-01-31T13:00:00',
    //             // 'dateTime' => $start.':00-04:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'end' => array(
    //             'dateTime' => '2024-01-31T14:50:00',
    //             //'dateTime' => $end.':00-04:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'attendees' => [
    //         	0 => [
    //         		'email' => $email,
    //         		'responseStatus' => 'accepted',
    //         	],
    //         	1 => [
    //         		'email' => "rimtzg@gmail.com",
    //         		'responseStatus' => 'needsAction',
    //         		'displayName' => 'Yo merengues'
    //         	]
    //         ],
    //         'source' => [
    //         	'title' => 'El google',
    //         	'url' => 'https://www.google.com'
    //         ],
    //         'reminders' => array(
    //             'useDefault' => FALSE,
    //             'overrides' => array(
    //                 array('method' => 'email', 'minutes' => 24 * 60),
    //                 array('method' => 'popup', 'minutes' => 60),
    //                 array('method' => 'popup', 'minutes' => 10),
    //             ),
    //         ),
    //         'visibility' => 'public',
    //     ), JSON_NUMERIC_CHECK);

    //     $event = $this->googleapi->createCalendarEvent('primary', $data);

    //     //print_r($event);

    //     $id = $event->id;

    //     //print_r($id);

    //     $data = json_encode(array(
    //         'summary' =>'reunion nutriologo (actualizado)',
    //         'location' => 'avenidad el sauz 621 colonia los pinos, celaya guanajuato',
    //         'description' => ' cita con el nutriologo perez para bajarte la panza',
    //         'start' => array(
    //             'dateTime' => '2024-01-31T13:00:00',
    //             // 'dateTime' => $start.':00-04:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'end' => array(
    //             'dateTime' => '2024-01-31T14:50:00',
    //             //'dateTime' => $end.':00-04:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'attendees' => [
    //         	0 => [
    //         		'email' => $email,
    //         		'responseStatus' => 'accepted',
    //         	],
    //         	1 => [
    //         		'email' => "rimtzg@gmail.com",
    //         		'responseStatus' => 'needsAction',
    //         		'displayName' => 'Yo merengues'
    //         	]
    //         ],
    //         'source' => [
    //         	'title' => 'El google',
    //         	'url' => 'https://www.google.com'
    //         ],
    //         'reminders' => array(
    //             'useDefault' => FALSE,
    //             'overrides' => array(
    //                 array('method' => 'email', 'minutes' => 24 * 60),
    //                 array('method' => 'popup', 'minutes' => 60),
    //                 array('method' => 'popup', 'minutes' => 10),
    //             ),
    //         ),
    //         'visibility' => 'public',
    //     ), JSON_NUMERIC_CHECK);

	//     $event = $this->googleapi->updateCalendarEvent('primary', $id, $data);

	//     //print_r($event);

	//     $this->googleapi->deleteCalendarEvent('primary', $id);

	//     print_r($event);

    //     exit();
    // }

	// public function authorized(){
	// 	/*
	// 	$token = $this->headers('Token');
	// 	$session = $this->token->validateToken($token);

	// 	$user = (object) $session['data'];
	// 	*/

	// 	$headers = (object) $this->input->request_headers();

	// 	//print_r($headers);
	// 	//exit();

	// 	$data = explode('.', $headers->token);
	// 	$user = json_decode(base64_decode($data[2]));

	// 	$path = $this->input->get('path');

	// 	//print_r($path);
	// 	//exit();

	// 	$id_user = intval($user->idUsuario);
	// 	$id_rol = intval($user->idRol);

	// 	$auth = $this->MenuModel->checkAuth($path, $id_user, $id_rol);

	// 	$result = [
	// 		"idRol" => $id_rol,
	// 		"idUsuario" => $id_user,
	// 		"authorized" => $auth,
	// 	];

	// 	$this->json($result);
	// }

	// public function login(){
	// 	$response = [
	// 		'message' => 'Error',
	// 		'result' => 0
	// 	];

	// 	$data = $this->post();
		
	// 	if(!(isset($data->password) || isset($data->numempleado))){
	// 		$response['message'] = 'Faltan datos';

	// 		$this->json($response);
	// 	}

	// 	$data->password = encriptar($data->password);

	// 	$user = $this->usuariosModel->login($data->numempleado, $data->password);

	// 	if(!$user){
	// 		$response['message'] = 'El empleado no se encuentra registrado';

	// 		$this->json($response);
	// 	}

	// 	$session = array(
	// 		'idUsuario'		=>	$user->idUsuario,
	// 		'idRol'			=>	$user->idRol,
	// 		'numEmpleado'	=>	$user->numEmpleado,
	// 		'numContrato'	=>	$user->numContrato,
	// 		'nombre'		=>	$user->nombre,
	// 		'telPersonal'	=>	$user->telPersonal,
	// 		'puesto'		=>	$user->puesto,
	// 		'oficina'		=>	$user->oficina,
	// 		'sede'			=>	$user->idSede,
	// 		'correo'		=>	$user->correo,
	// 		'idArea'		=>	$user->idArea,
	// 	);

	// 	$token = $this->token->generateToken($session);

	// 	if($token){
	// 		$response['token'] = $token;
	// 		$response['message'] = 'ok';
	// 		$response['result'] = 1;
	// 	}

	// 	$this->json($response);
	// }

	// public function check(){
	// 	$token = $this->headers('Token');

	// 	$session = $this->token->validateToken($token);

	// 	$this->json($session);
	// }

	// public function insertGoogleEvent () {
    //     $dataValue = $this->input->post("dataValue", true);

    //     $title  	  = $dataValue['title'];
	// 	$end    	  = $dataValue['end'];
	// 	$start  	  = $dataValue['start'];
	// 	$location  	  = $dataValue['location'];
	// 	$description  = $dataValue['description'];
    //     $attendees  = $dataValue['description'];

    //     $response['result'] = isset($title, $end, $start);
    //     if ($response['result']) {

    //     }else {

    //     }



    //     $data = json_encode(array(
    //         'summary' =>'reunion nutriologo',
    //         'location' => 'avenidad el sauz 621 colonia los pinos, celaya guanajuato',
    //         'description' => ' cita con el nutriologo perez para bajarte la panza',
    //         'start' => array(
    //             'dateTime' => '2024-01-31T13:00:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'end' => array(
    //             'dateTime' => '2024-01-31T14:50:00',
    //             'timeZone' => 'America/Mexico_City',
    //         ),
    //         'attendees' => [
    //             0 => [
    //                 'email' => $email,
    //                 'responseStatus' => 'accepted',
    //             ],
    //             1 => [
    //                 'email' => "rimtzg@gmail.com",
	// 				'responseStatus' => 'accepted',
    //                 'displayName' => 'Yo merengues'
    //             ]
    //         ],
    //         'reminders' => array(
    //             'useDefault' => FALSE,
    //             'overrides' => array(
    //                 array('method' => 'email', 'minutes' => 24 * 60),
    //                 array('method' => 'popup', 'minutes' => 60),
    //                 array('method' => 'popup', 'minutes' => 10),
    //             ),
    //         ),
    //         'visibility' => 'public',
    //     ), JSON_NUMERIC_CHECK);

    // }

	////////////////////
	// END GOOGLE API//
	///////////////////
}
