<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class LoginController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuariosModel');
		$this->load->model('generalModel');
		$this->load->model('menuModel');

		$this->load->helper(array('form','funciones'));
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}
	public function addRegistroEmpleado(){
		$this->db->trans_begin();
		$datosEmpleado = $this->input->post('params');

		switch ($datosEmpleado['nsede']){
			case 'QRO':
				$sede = 1;
			break;
			case 'LEON':
				$sede = 2;
			break;
			case 'SLP':
				$sede = 3;
			break;
			case 'CDMX':
				$sede = 4;
			break;
			case 'MERIDA':
				$sede = 5;
			break;
			case 'CANCUN':
				$sede = 9;
			break;
			case 'TIJUANA':
				$sede = 11;
			break;
			case 'SAN MIGUEL DE ALLENDE':
				$sede = 12;
			break;
			case 'TEXAS':
				$sede = 13;
			break;
			case 'MONTERREY':
				$sede = 14;
			break;
			case 'REGION BAJIO':
				$sede = 15;
			break;
			case 'REGION SUR':
				$sede = 16;
			break;
			case 'GUADALAJARA':
				$sede = 17;
			break;
			case 'AGUASCALIENTES':
				$sede = 18;
			break;
			case 'PUEBLA':
				$sede = 19;
			break;
		}

		switch ($datosEmpleado['idpuesto']){
			case "158":
			case "585":
			case "686": 
			case "537":
				$idRol = 3;
			break;
			default:
				$idRol = 2;
			break;
		}

		$insertData = array(
			"numContrato" =>  $datosEmpleado['num_empleado'],
			"numEmpleado" => $datosEmpleado['num_empleado'],
			"nombre" => $datosEmpleado['nombre_persona'] . $datosEmpleado['pri_apellido'] .  $datosEmpleado['sec_apellido'],
			"telPersonal" => $datosEmpleado['telefono_personal'],
			"telOficina" => NULL,
			"idPuesto" => $datosEmpleado['idpuesto'],
			"idSede" => $sede,
			"correo" => $datosEmpleado['mail_emp'],
			"password" => encriptar($datosEmpleado['password'] ),
			"estatus" => 1,
			"idRol" => $idRol,
			"sexo" => $datosEmpleado['sexo'],
			"idArea" => $datosEmpleado['idarea'],
			"fechaIngreso" => $datosEmpleado['fingreso'],
			"externo" => 0,
			"creadoPor" => 0,
			"fechaCreacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => 0,
			"fechaModificacion" => date('Y-m-d H:i:s')
		);

		$resultado = $this->generalModel->addRecord('usuarios',$insertData);
		$last_id = $this->db->insert_id();

		$insertData = array(
			"idUsuario" => $last_id,
			"estatus" => 1,
			"creadoPor" => 1,
			"fechaCreacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => 1,
			"fechaModificacion" => date('Y-m-d H:i:s')
		);
		$resultado = $this->generalModel->addRecord('detallePaciente',$insertData);

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
		echo json_encode(array('user' => $datosUser, 'result' => 1));
	}

	public function check(){
		$headers = (object) $this->input->request_headers();
		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		echo json_encode(array('user' => $user, 'result' => 1));
	}

	public function logout()
	{
		$this->session->sess_destroy();
	}
	
	public function login($array = ''){
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		$datosEmpleado->password =  encriptar($datosEmpleado->password);
		$data = $this->usuariosModel->login($datosEmpleado->numempleado,$datosEmpleado->password)->result();
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
				'idPuesto' 		        => 		$data[0]->idPuesto,
				'sede' 		    		=> 		$data[0]->idSede,
				'correo'		    	=> 		$data[0]->correo,
				'idArea'				=>		$data[0]->idArea,
			);
			if(!isset($_SESSION)) 
			{ 
				session_start(); 
			} 
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

?>