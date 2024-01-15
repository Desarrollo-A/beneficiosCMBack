<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('BASEPATH') OR exit('No direct script access allowed');

class loginController extends CI_Controller {


	public function __construct()
	{

		parent::__construct();
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type,Origin, authorization, X-API-KEY,X-Requested-With,Accept,Access-Control-Request-Method');
        header('Access-Control-Allow-Method: GET, POST, PUT, DELETE,OPTION');

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
            $this->load->helper(array('form','funciones'));
            $this->load->model(array('usuariosModel','generalModel'));
			$this->load->library(array('session'));
        } else {
            die ("Access Denied");     
            exit;  
        }
	}

	public function index()
	{
		
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}
	public function addRegistroEmpleado(){
		$this->db->trans_begin();
		$datosEmpleado = json_decode(file_get_contents('php://input'));
		$datosEmpleado = $datosEmpleado->params;
		$insertData = array(
			"numContrato" => $datosEmpleado->idcontrato,
			"numEmpleado" => $datosEmpleado->num_empleado,
			"nombre" => $datosEmpleado->nombre_completo,
			"telPersonal" => $datosEmpleado->tel_personal,
			"telOficina" => $datosEmpleado->nom_oficina,
			"area" => $datosEmpleado->area,
			"puesto" => $datosEmpleado->puesto,
			"oficina" => $datosEmpleado->nom_oficina,
			"sede" => $datosEmpleado->sede,
			"correo" => $datosEmpleado->email_empresarial,
			"password" => encriptar($datosEmpleado->password),
			"estatus" => 1,
			"creadoPor" => 0,
			"fechaCreacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => 0,
			"fechaModificacion" => date('Y-m-d H:i:s')
		);
		$resultado = $this->generalModel->agregarRegistro('usuarios',$insertData);
		if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
			if(strpos($resultado['message'], "UNIQUE")){
				echo json_encode(array("estatus" => 0, "mensaje" => "El número de empleado ingresado ya se encuentra registrado" ));
			}else{
				echo json_encode(array("estatus" => -1, "mensaje" => "Hubo un error al registrase" ));
			}
        } else {
            $this->db->trans_commit();
			echo json_encode(array("estatus" => 1, "mensaje" => "Te has registrado con éxito"));
        }
	
	}
	public function me(){
		$datosSession = json_decode( file_get_contents('php://input'));
		$arraySession = explode('.',$datosSession->token);
		$datosUser = json_decode(base64_decode($arraySession[2]));
		echo json_encode(array('user' => $datosUser,
									'result' => 1));
	}

	public function logout()
	{
		$this->session->sess_destroy();
	}
	public function login($array = ''){
		session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		$datosEmpleado->password =  encriptar($datosEmpleado->password);
		$data = $this->usuariosModel->login($datosEmpleado->numempleado,$datosEmpleado->password);
		if(empty($data)){
			echo json_encode(array('response' => [],
									'message' => 'El número de empleado no se encuentra registrado',
									'result' => 0));
		}else{
			$datosSesion = array(
				'id_usuario' 	        => 		$data[0]->idUsuario,
				'numEmpleado'           =>      $data[0]->numEmpleado,
				'numContrato'           =>      $data[0]->numContrato,
				'nombre' 		        => 		$data[0]->nombre,
				'telPersonal' 		    => 		$data[0]->telPersonal,
				'puesto' 		        => 		$data[0]->puesto,
				'oficina' 		        => 		$data[0]->oficina,
				'sede' 		    		=> 		$data[0]->idSede,
				'correo' 		    	=> 		$data[0]->correo,
			);
			session_start();
			date_default_timezone_set('America/Mexico_City');
			$time = time();
                    $dataTimeToken = array(
						"userId"=>$data[0]->numContrato,
                        "iat" => $time, // Tiempo en que inició el token
                        "exp" => $time + (24 * 60 * 60), // Tiempo en el que expirará el token (24 horas)
                    );
			$tokenPart1 = base64_encode(json_encode(array("alg" => "HS256", "typ"=>"JWT")));
			$tokenPart2 = base64_encode(json_encode(array("numEmpleado" => $data[0]->numEmpleado, "iat" => $time,"exp" => $time + (24 * 60 * 60))));
			$tokenPart3 = base64_encode(json_encode($data[0]));
			$datosSesion['token'] = $tokenPart1.'.'.$tokenPart2;
			$this->session->set_userdata($datosSesion);
			if($array == ''){
				echo json_encode(array('user' => $data[0],
									'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3,
									'result' => 1));
			} else{
				return json_encode(array('user' => $data[0],
				'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3,
				'result' => 1));
			}
							
		}
	}
}
