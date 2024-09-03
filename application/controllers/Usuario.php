<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Usuario extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->ch = $this->load->database('ch', TRUE);
		$this->load->model('CalendarioModel');
		$this->load->model('UsuariosModel');
		$this->load->model('GeneralModel');
		$this->load->model('MenuModel');
		$this->load->library("email");
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
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
		$token = $this->headers('Token');

		$data = explode('.', $token);
		$user = json_decode(base64_decode($data[2]));

		$id_user = intval($user->idUsuario);
		$id_rol = intval($user->idRol);
		$permisos = intval($user->permisos);

		echo json_encode($this->MenuModel->getMenu($id_user, $id_rol, $permisos));
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
		if (property_exists($headers, 'Token')) {
			$data = explode('.', $headers->Token);
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
		}else {
			$result = [
				"idRol" => null,
				"idUsuario" => null,
				"authorized" => false,
			];
	
			echo json_encode($result);
		}
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
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
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
            $rows = array();
            foreach ($data as $user) {

				$complemento = date('YmdHis');
				$iniciales = getIniciales($user['nombre']);

				// Insert hacia la tabla de usuarios externos
                $row = array(
					'idUsuarioExt' => null,
					'idContrato' => $iniciales.$complemento,
                    'nombre' => $user['nombre'],
                    'correo' => isset($user['correo']) ? $user['correo'] : null,
                    'telPersonal' => isset($user['telPersonal']) ? $user['telPersonal'] : null,
					'sexo' => $user['sexo'],
					'idRol' => 2,
                    'estatus' => 1,
					'creadoPor' => $user['creadoPor'],
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => $user['creadoPor'],
                    'fechaModificacion' => $fecha,
                );
                $rows[] = $row;

				// Insert hacia la tabla de usuarios
				$row2 = array(
					'idContrato' => $iniciales.$complemento,
					'password' => null,
					'idRol' => 2,
					'externo' => 1,
					'idAreaBeneficio' => NULL, 
                    'estatus' => 1,
					'creadoPor' => $user['creadoPor'],
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => $user['creadoPor'],
                    'fechaModificacion' => $fecha,
                );
				$rows2[] = $row2;
            }
        
            $res = $this->GeneralModel->insertBatch($this->schema_cm.'.'.$table, $rows);
			$res2 = $this->GeneralModel->insertBatchAndGetIds($this->schema_cm .'.usuarios', $rows2);

			foreach ($res2 as $index => $id) {
				$row3 = array(
					'idUsuario' => $id,
					'estatusNut' => NULL,
					'estatusPsi' => NULL,
					'estatusQB' => NULL,
					'estatusGE' => NULL, 
                    'estatus' => 1,
					'creadoPor' => $user['creadoPor'],
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => $user['creadoPor'],
                    'fechaModificacion' => $fecha,
				);
				$rows3[] = $row3;
			}
			$res3 = $this->GeneralModel->insertBatch($this->schema_cm .'.detallepaciente', $rows3);
			
			$response['result'] = $res3;
            
            if ($response['result']) {
                $response['msg'] = "¡Listado insertado exitosamente!";
            } else {
                $response['msg'] = "¡No se ha podido insertar los datos!";
            }
        } else {
            $response['msg'] = "¡Parametros invalidos!";
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

	public function updateUser() {
		$fecha = date('Y-m-d H:i:s');
		$user = $this->input->post('dataValue[idUsuario]');
		
		$data = array();
	
		// Recorre $_POST y agrega los campos con valores (incluido 0) al array $data
		foreach ($this->input->post('dataValue') as $key => $value) {
			if (($value !== null || $value !== '') && $key != 'idUsuario') {
				$data[$key] = $value;
			}
		}

		$response['result'] = isset($user, $data) && !empty($user);
		
		if ($response['result']) {  
			$data['fechaModificacion'] = $fecha;
			$data['modificadoPor'] = $user;
			$response['result'] = $this->GeneralModel->updateRecord($this->schema_cm .'.usuarios', $data, 'idUsuario', $user);
	
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

	// Actualizar usuarios externos como personal beneficiaria lamat.
	public function updateExternalUser() {
		$fecha = date('Y-m-d H:i:s');
		$user = $this->input->post('dataValue[idUsuarioExt]');
		
		$data = array();
	
		// Recorre $_POST y agrega los campos con valores (incluido 0) al array $data
		foreach ($this->input->post('dataValue') as $key => $value) {
			if (($value !== null || $value !== '') && $key != 'idUsuarioExt') {
				$data[$key] = $value;
			}
		}

		$response['result'] = isset($user, $data) && !empty($user);
		
		if ($response['result']) {  
			$data['fechaModificacion'] = $fecha;
			$response['result'] = $this->GeneralModel->updateRecord($this->schema_cm .'.usuariosexternos', $data, 'idContrato', $user);
	
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
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function decodePass(){
		$token = $this->headers('Token');

		$array = json_decode(base64_decode(explode(".", $token)[1]));
		$dt = $array->numEmpleado;

		$data['data'] = $this->UsuariosModel->decodePass($dt);
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function decodePassAdmin(){

		$data['data'] = $this->UsuariosModel->decodePassAdmin();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function updatePass(){
		$idUsuario = $this->input->post('dataValue[idUsuario]');
		$password = $this->input->post('dataValue[password]');
		$newPass = $this->input->post('dataValue[newPassword]');

			if(!empty($newPass))
			{
				$data = array(
					"password" => encriptar($newPass),
				);
				
				$response=$this->GeneralModel->updateRecord($this->schema_cm .'.usuarios', $data, 'idUsuario', $idUsuario);
				echo json_encode(array("estatus" => true, "msj" => "Contraseña actualizada" ));
					
			}else{
				echo json_encode(array("estatus" => false, "msj" => "Error en actualizar contraseña"));
			}	
	}

	public function loginCH(){
		$auth = $this->headers('Authorization');
		$token = null;

		$result = (object) [
			'result' => 'error',
			'msg' => 'Error'
		];

		if(!isset($auth)){
			$result->msg = 'Falta header de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$matches = array();
	    if (preg_match('/Basic (.+)/', $auth, $matches)) {
	        if (isset($matches[1])) {
	            $token = $matches[1];
	        }
	    }

	    if(!isset($token)){
			$result->msg = 'Falta token de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$decoded = base64_decode($token);

		list($username, $password) = explode(":", $decoded);
		$usuario = $this->UsuariosModel->loginAPI($username, encriptar(trim($password)))->row();
		
		if(!isset($usuario)){
			$result->msg = 'No existe el usuario';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$accessToken = $this->token->generateToken($usuario);

		$result = (object) [
			'result' => true,
			'msg' => '¡Inicio de sesión exitoso!',
			'accessToken' => $accessToken,
		];

		$this->json($result, JSON_NUMERIC_CHECK);
	}

	public function updateCH(){
		$new_data = $this->input->POST();
		$auth = $this->headers('Authorization');
		$token = null;
		$idContrato = $this->input->POST('idcontrato');
		$fecha = date('Y-m-d H:i:s');

		$result = (object) [
			'result' => false,
			'msg' => 'Error'
		];

		if(!isset($auth)){
			$result->msg = 'Falta header de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$matches = array();
	    if (preg_match('/Bearer (.+)/', $auth, $matches)) {
	        if (isset($matches[1])) {
	            $token = $matches[1];
	        }
	    }

	    if(!isset($token)){
			$result->msg = 'Falta token de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$decoded = (object) $this->token->validateToken($token);

		if(!$decoded->status){
			$result->msg = $decoded->message;
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$usuario = $decoded->data;

		if($usuario->idRol != 1){
			$result->msg = 'No tiene permisos para esta acción';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		if(!isset($idContrato)){
			$result->msg = 'Falta el id de contrato';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$empleado = $this->UsuariosModel->getUserByIdContrato($idContrato)->row();

		if(!isset($empleado)){
			$result->msg = 'No existe el empleado en la base de datos';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		if(!$new_data){
			$result->msg = 'No hay datos';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$new_data = (array) $new_data;
		$data = array();

		if (isset($new_data["nombre_persona"]) || isset($new_data["pri_apellido"]) || isset($new_data["sec_apellido"])) {
			$data['nombre'] = '';
			if (isset($new_data["nombre_persona"])) {
				$data['nombre'] .= $new_data["nombre_persona"];
			}
			if (isset($new_data["pri_apellido"])) {
				$data['nombre'] .= ' ' . $new_data["pri_apellido"];
			}
			if (isset($new_data["sec_apellido"])) {
				$data['nombre'] .= ' ' . $new_data["sec_apellido"];
			}
			$data['nombre'] = trim($data['nombre']); // Elimina espacios al inicio y al final
		}

		isset($new_data["num_empleado"]) 	  ? $data['numEmpleado']  = $new_data["num_empleado"] : '';
		isset($new_data["telefono_personal"]) ? $data['telPersonal']  = $new_data["telefono_personal"] : '';
		isset($new_data["tel_oficina"]) 	  ? $data['telOficina']   = $new_data["tel_oficina"] : '';
		isset($new_data["idpuesto"]) 		  ? $data['idPuesto'] 	  = $new_data["idpuesto"] : '';
		isset($new_data["idsede"]) 			  ? $data['idSede'] 	  = $new_data["idsede"] : '';
		isset($new_data["mail_emp"]) 		  ? $data['correo'] 	  = $new_data["mail_emp"] : '';
		isset($new_data["activo"]) 			  ? $data['estatus'] 	  = $new_data["activo"] : '';
		isset($new_data["sexo"]) 			  ? $data['sexo'] 		  = $new_data["sexo"] : '';
		isset($new_data["fingreso"]) 		  ? $data['fechaIngreso'] = $new_data["fingreso"] : '';
		
		$data["fechaModificacion"] = $fecha;
		$data["modificadoPor"] = 1;

		$updated = $this->GeneralModel->updateRecord($this->schema_cm .".usuarios", $data, "idContrato", $idContrato);

		if($updated){
			$result->result = true;
			$result->msg = 'Empleado actualizado';
			$result->data = $data;
		}else{
			$result->msg = 'No se pudo actualizar el empleado';
		}

		$this->json($result, JSON_NUMERIC_CHECK);
	}

	public function bajaCH(){
		$auth = $this->headers('Authorization');
		$token = null;
		$idContrato = $this->input->POST('idcontrato');
		$fecha = date('Y-m-d H:i:s');

		$result = (object) [
			'result' => false,
			'msg' => 'Error'
		];

		if(!isset($auth)){
			$result->msg = 'Falta header de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$matches = array();
	    if (preg_match('/Bearer (.+)/', $auth, $matches)) {
	        if (isset($matches[1])) {
	            $token = $matches[1];
	        }
	    }

	    if(!isset($token)){
			$result->msg = 'Falta token de autorización';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$decoded = (object) $this->token->validateToken($token);

		if(!$decoded->status){
			$result->msg = $decoded->message;
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$usuario = $decoded->data;

		if($usuario->status != 1){
			$result->msg = 'No tiene permisos para esta acción';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		if(!isset($idContrato)){
			$result->msg = 'Falta el id de contrato';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$empleado = $this->UsuariosModel->getUserByIdContrato($idContrato)->row();

		if(!isset($empleado)){
			$result->msg = 'No existe el empleado en la base de datos';
			$this->json($result, JSON_NUMERIC_CHECK);
		}

		$data["estatus"] = 0;
		$data["fechaModificacion"] = $fecha;
		$data["modificadoPor"] = 1;

		$updated = $this->GeneralModel->updateRecord($this->schema_cm .".usuarios", $data, "idContrato", $idContrato);
		$cancela = $this->CalendarioModel->cancelaCitasPorBajaUsuario($idContrato);

		if($updated && $cancela){
			$result->result = true;
			$result->msg = 'Proceso completo exitoso';
		}else{
			$result->msg = 'No se pudo dar de baja el empleado';
		}

		$this->json($result, JSON_NUMERIC_CHECK);
	}

	public function sendMail() {

			$correoVar = $this->input->post('dataValue', true);
			$correo = substr($correoVar, 1, -1);

			$query_mailexist = $this->ch->query("SELECT * FROM ". $this->schema_cm .".correostemporales ct
			WHERE ct.correo = '$correo'");

			if ($query_mailexist->num_rows() > 0) {
				echo json_encode(array("estatus" => false, "msj" => "El correo ingresado ya está en uso"), JSON_NUMERIC_CHECK);
			}else{

			$this->ch->query("DELETE FROM ". $this->schema_cm .".tokenregistro WHERE correo = ?", $correo);

			$config['protocol']  = 'smtp';
			$config['smtp_host'] = 'smtp.gmail.com';
			$config['smtp_user'] = 'no-reply@ciudadmaderas.com'; // testemail@ciudadmaderas.com // no-reply@ciudadmaderas.com
			$config['smtp_pass'] = 'JDe64%8q5D'; // Feb2024@Te# // JDe64%8q5D
			$config['smtp_port'] = 465;
			$config['charset']   = 'utf-8';
			$config['mailtype']  = 'html';
			$config['newline']   = "\r\n"; 
			$config['smtp_crypto']   = 'ssl';

			$data["data"] = substr(md5(time()), 0, 6);
			
			$html_message = $this->load->view("email-verificacion", $data, true);
					
			$this->load->library("email");
			$this->email->initialize($config);
			$this->email->from("no-reply@ciudadmaderas.com");
			$this->email->to($correo);
			$this->email->message($html_message);
			$this->email->subject("Código de verificación Beneficios Maderas");

			if ($this->email->send()) {
				echo json_encode(array("estatus" => true, "msj" => "Envío exitoso" ), JSON_NUMERIC_CHECK); 
				$this->ch->query("INSERT INTO ". $this->schema_cm .".tokenregistro (correo, token, fechaCreacion) 
					VALUES (?,?, NOW())", 
					array($correo, $data));
			} else {
				echo json_encode(array("estatus" => false, "msj" => "Ocurrió un error"), JSON_NUMERIC_CHECK);
			}

		}
	}

	public function getToken(){
		$dt = $this->input->post('dataValue', true);
		$data['data'] = $this->UsuariosModel->getToken($dt);
	}

	public function getUserByNumEmp(){
        $user = $this->input->post('dataValue[num_empleado]');
        $response['result'] = isset($user);
        if ($response['result']) {
            $rs = $this->UsuariosModel->getUserByNumEmp($user)->result();

			$response['result'] = count($rs) > 0;
            if ($response['result']) {
				$response['result'] = $rs[0]->activo == 1;
				if ($response['result']) {
					$response['msg'] = '¡Colaborador consultado exitosamente!';
					$response['data'] = $rs;
				}else {
					$response['msg'] = '¡Colaborador inactivo!';
				}
            } else {
                $response['msg'] = '¡No existe el colaborador!';
            }
        }else {
            $response['msg'] = "¡Parámetros inválidos!";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
}
