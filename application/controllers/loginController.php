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
        parent::__construct();
        $urls = array('192.168.30.128/auth/jwt/login','localhost','http://localhost:3030','http://192.168.30.128/auth/jwt/login','192.168.30.128','http://192.168.30.128:3030','127.0.0.1','https://rh.gphsis.com','rh.gphsis.com','https://maderascrm.gphsis.com','maderascrm.gphsis.com');
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
            $this->load->model(array('usuariosModel'));
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
		$mensaje = "";
		$datos = $_POST;
		//var_dump($datos);
		$datosEmpleado = json_decode(file_get_contents('php://input'));
		var_dump($datosEmpleado);
		

		//return ["estatus" => 1, "mensaje" => $mensaje];
	}
	public function me(){
		$datosSession = json_decode( file_get_contents('php://input'));
		//var_dump($datosSession);
		//echo $datosSession->token;
		$arraySession = explode('.',$datosSession->token);
		//var_dump($arraySession);
		$datosUser = json_decode(base64_decode($arraySession[2]));
		echo json_encode(array('user' => $datosUser,
									'result' => 1));
	}

	public function logout()
	{
		$this->session->sess_destroy();
	}
	public function login(){
		session_destroy();
		$datosEmpleado = json_decode( file_get_contents('php://input') );
		//var_dump($datosEmpleado);
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
				'telOficina' 		    => 		$data[0]->telOficina,
				'area' 		        	=> 		$data[0]->area,
				'puesto' 		        => 		$data[0]->puesto,
				'oficina' 		        => 		$data[0]->oficina,
				'sede' 		    		=> 		$data[0]->sede,
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
					//var_dump($dataTimeToken);
			$tokenPart1 = base64_encode(json_encode(array("alg" => "HS256", "typ"=>"JWT")));
			$tokenPart2 = base64_encode(json_encode(array("numEmpleado" => $data[0]->numEmpleado, "iat" => $time,"exp" => $time + (24 * 60 * 60))));
			$tokenPart3 = base64_encode(json_encode($data[0]));
			$datosSesion['token'] = $tokenPart1.'.'.$tokenPart2;
			$this->session->set_userdata($datosSesion);
			echo json_encode(array('user' => $data[0],
									'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3,
									'result' => 1));
		}
	}
}
