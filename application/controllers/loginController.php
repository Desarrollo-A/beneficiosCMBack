<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class LoginController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('UsuariosModel');
		$this->load->model('GeneralModel');
		$this->load->model('menuModel');

		$this->load->helper(array('form','funciones'));
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function usuarios(){
		$data['data'] = $this->UsuariosModel->usuarios();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
	
	public function addRegistroEmpleado(){
		$this->db->trans_begin();
		$datosEmpleado = $this->input->post('dataValue[params]');

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
			"idContrato" => isset($datosEmpleado['idcontrato']) ? $datosEmpleado['idcontrato'] : NULL,
			"numEmpleado" => isset($datosEmpleado['num_empleado']) ? $datosEmpleado['num_empleado'] : NULL,
			"nombre" => isset($datosEmpleado['nombre_persona']) ? $datosEmpleado['nombre_persona'].' '.$datosEmpleado['pri_apellido'].' '.$datosEmpleado['sec_apellido'] : NULL,
			"telPersonal" => isset($datosEmpleado['telefono_personal']) ? $datosEmpleado['telefono_personal'] : NULL,
			"idPuesto" => isset($datosEmpleado['idpuesto']) ? $datosEmpleado['idpuesto'] : NULL,
			"idSede" => isset($sede) ? $sede : NULL,
			"correo" => isset($datosEmpleado['mail_emp']) ? $datosEmpleado['mail_emp'] : NULL ,
			"password" => isset($datosEmpleado['password']) ? encriptar($datosEmpleado['password']) : NULL,
			"estatus" => 1,
			"idRol" => $idRol,
			"sexo" => $datosEmpleado['sexo'],
			"idArea" => $datosEmpleado['idarea'],
			"fechaIngreso" => $datosEmpleado['fingreso'],
			"externo" => 0,
			"creadoPor" => 0,
			"fechaCreacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => 0,
			"fechaModificacion" => date('Y-m-d H:i:s'),
		);

		$filteredArray = array_filter($insertData, 'strlen');
		$longitudArreglo = count($filteredArray);

		if (!isset($datosEmpleado['mail_emp'])){
			$longitudArreglo += 1;
		}

		if (!isset($datosEmpleado['telefono_personal'])){
			$longitudArreglo += 1;
		}

		if(count($insertData) == $longitudArreglo){
			$informacionDePuesto = $this->GeneralModel->getInfoPuesto($insertData["idPuesto"])->row();
			
			if (!$informacionDePuesto) {
				echo json_encode(array("result" => false, "msg" => "No hemos encontrado la información del puesto" ), JSON_NUMERIC_CHECK);
			}else {
				$canRegister = $informacionDePuesto->canRegister;

				if ($canRegister === 0 || $canRegister === '0') {
					echo json_encode(array("result" => false, "msg" => "Por el momento no puede gozar de los beneficios"), JSON_NUMERIC_CHECK);
				}else {
					$usuarioExiste = $this->GeneralModel->usuarioExiste($insertData["idContrato"]);
				
					if($usuarioExiste->num_rows() === 0){
						$resultado = $this->GeneralModel->addRecord('usuarios',$insertData);
						$last_id = $this->db->insert_id();
						
						$insertData = array(
							"idUsuario" => $last_id,
							"estatus" => 1,
							"creadoPor" => 1,
							"fechaCreacion" => date('Y-m-d H:i:s'),
							"modificadoPor" => 1,
							"fechaModificacion" => date('Y-m-d H:i:s')
						);
		
						$resultado = $this->GeneralModel->addRecord('detallePaciente', $insertData);
		
						if ($this->db->trans_status() === FALSE){
							$this->db->trans_rollback();
							
							if(strpos($resultado['message'], "UNIQUE")){
								echo json_encode(array("result" => false, "msg" => "El número de empleado ingresado ya se encuentra registrado" ), JSON_NUMERIC_CHECK);
							}else{
								echo json_encode(array("result" => false, "msg" => "Hubo un error al registrarse" ), JSON_NUMERIC_CHECK);
							}
						} else {
							$this->db->trans_commit();
							echo json_encode(array("result" => true, "msg" => "Te has registrado con éxito"), JSON_NUMERIC_CHECK);
						}
					}
					else{
						echo json_encode(array("result" => false, "msg" => "El número de empleado ingresado ya se encuentra registrado" ), JSON_NUMERIC_CHECK);
					}
				}
			}
		} else {
			echo json_encode(array("result" => false, "msg" => "Faltan valores en tu perfil" ), JSON_NUMERIC_CHECK);
		}
	}
  
	public function me(){
		$datosSession = json_decode( file_get_contents('php://input'));
		$arraySession = explode('.',$datosSession->token);
		$datosUser = json_decode(base64_decode($arraySession[2]));
		echo json_encode(array('user' => $datosUser, 'result' => 1), JSON_NUMERIC_CHECK);
	}

	public function check(){
		$headers = (object) $this->input->request_headers();
		$data = explode('.', $headers->token);
		$user = json_decode(base64_decode($data[2]));

		echo json_encode(array('user' => $user, 'result' => 1), JSON_NUMERIC_CHECK);
	}

	public function logout()
	{
		$this->session->sess_destroy();
	}
	
	public function login($array = ''){
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		$datosEmpleado->password =  encriptar($datosEmpleado->password);
		$data = $this->UsuariosModel->login($datosEmpleado->numempleado,$datosEmpleado->password)->result();
		if(empty($data)){
			echo json_encode(array('response' => [],
									'message' => 'El número de empleado no se encuentra registrado',
									'result' => 0), JSON_NUMERIC_CHECK);
		}else{
			$datosSesion = array(
				'id_usuario' 	        => 		$data[0]->idUsuario,
				'idContrato'            =>      $data[0]->idContrato,
				'numEmpleado'           =>      $data[0]->numEmpleado,
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
						"userId"=>$data[0]->idContrato,
                        "iat" => $time, // Tiempo en que inició el token
                        "exp" => $time + (24 * 60 * 60), // Tiempo en el que expirará el token (24 horas)
                    );
			$tokenPart1 = base64_encode(json_encode(array("alg" => "HS256", "typ"=>"JWT"), JSON_NUMERIC_CHECK));
			$tokenPart2 = base64_encode(json_encode(array("numEmpleado" => $data[0]->numEmpleado, "iat" => $time,"exp" => $time + (24 * 60 * 60)), JSON_NUMERIC_CHECK));
			$tokenPart3 = base64_encode(json_encode($data[0], JSON_NUMERIC_CHECK));
			$datosSesion['token'] = $tokenPart1.'.'.$tokenPart2;
			$this->session->set_userdata($datosSesion);

			if($array == ''){
				echo json_encode(array('user' => $data[0],
									'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3,
									'result' => 1), JSON_NUMERIC_CHECK);
			} else{
				return json_encode(array('user' => $data[0],
				'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3,
				'result' => 1), JSON_NUMERIC_CHECK);
			}
							
		}
	}
}

?>