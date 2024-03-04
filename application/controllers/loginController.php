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

		$this->ch        = $this->load->database('ch', TRUE);
        $this->beneficio = $this->load->database('beneficio', TRUE);

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
				$idRol = 3;
				$areaBeneficio = 3;
			case "585":
				$idRol = 3;
				$areaBeneficio = 6;
			case "686":
				$idRol = 3; 
				$areaBeneficio = 5;
			case "537":
				$idRol = 3;
				$areaBeneficio = 4;
			break;
			default:
				$idRol = 2;
				$areaBeneficio = NULL;
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
			"fechaIngreso" => $datosEmpleado['fingreso'],
			"externo" => 0,
			"creadoPor" => 1,
			"fechaCreacion" => date('Y-m-d H:i:s'),
			"modificadoPor" => 1,
			"fechaModificacion" => date('Y-m-d H:i:s'),
			"idAreaBeneficio" => $areaBeneficio,
		);

		$filteredArray = array_filter($insertData, 'strlen');
		$longitudArreglo = count($filteredArray);

		if (!isset($datosEmpleado['mail_emp'])){
			$longitudArreglo += 1;
		}

		if (!isset($datosEmpleado['telefono_personal'])){
			$longitudArreglo += 1;
		}

		if (!isset($areaBeneficio) && $idRol == 2){
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

	public function loginTest1($array = ''){
		$query =  $this->ch->query("CALL sp_usuarios()");
		$usuarios_ch = $query->result();

		$query =  $this->beneficio->query("SELECT *FROM usuarios as ben.u INNER JOIN $usuarios_ch as ch_u ON ch_u.idContrato = ben.u");
		$rs_beneficio = $query->result();

		$response = $rs_beneficio;

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	// Uso de sp y nuestra bd de app en sql server
	public function loginTest2($array = ''){
		// Crear una tabla temporal
		$this->ch->query("CREATE TEMPORARY TABLE tempTable (idContrato VARCHAR(100),numEmpleado VARCHAR(100),idSede INT,sexo VARCHAR(3),idPuesto INT,nombrePersona VARCHAR(100),priApellido VARCHAR(100),secApellido VARCHAR(100),fIngreso DATE,nSede INT,activo INT,mailEmp VARCHAR(255),nPuesto VARCHAR(255),telefonoPersonal VARCHAR(15))");
	
		// Ejecutar el procedimiento almacenado e insertar los resultados en la tabla temporal
		$query =  $this->ch->query("INSERT INTO tempTable CALL sp_usuarios()");
	
		// Hacer el FULL JOIN con la tabla de usuarios
		$query =  $this->beneficio->query("SELECT * FROM usuarios as ben.u FULL JOIN tempTable as ch_u ON ch_u.idContrato = ben.u");
		$rs_beneficio = $query->result();
	
		$response = $rs_beneficio;
	
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	// Uso de sp y nuestra bd de app en mysql
	public function loginTest3(){
		// Ejecutar el procedimiento almacenado
		$query =  $this->ch->query("CALL sp_usuarios()");
		$usuarios_ch = $query->result();
	
		// Liberar los resultados
		$query->free_result();
		$this->ch->close();
	
		// Reabrir la conexión
		$this->ch->reconnect();
	
		// Crear una tabla temporal
		$this->ch->query("CREATE TEMPORARY TABLE tempTable (idContrato VARCHAR(100),numEmpleado VARCHAR(100),idSede INT,sexo VARCHAR(3),idPuesto INT,nombrePersona VARCHAR(100),priApellido VARCHAR(100),secApellido VARCHAR(100),fIngreso DATE,nSede INT,activo INT,mailEmp VARCHAR(255),nPuesto VARCHAR(255),telefonoPersonal VARCHAR(15))");
	
		// Insertar los resultados del procedimiento almacenado en la tabla temporal
		foreach ($usuarios_ch as $usuario) {
			$this->ch->insert('tempTable', $usuario);
		}
	
		// Hacer el INNER JOIN con la tabla usuarios_bnfcs
		$query = $this->ch->query("SELECT * FROM usuarios_bnfcs AS b INNER JOIN tempTable AS t ON t.idContrato = b.idContrato");
		$result = $query->result();
	
		$response = $result;
	
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	// Uso de vista y nuestra bd de app en mysql
	public function loginTest4(){
		// Hacer el INNER JOIN con la tabla usuarios_bnfcs
		$query = $this->ch->query("SELECT * FROM usuarios_bnfcs AS b_u INNER JOIN v_usuarios AS v_u ON v_u.idContrato = b_u.idContrato");
		$result = $query->result();
	
		$response = $result;
	
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	// Uso de vista y nuestra bd de app en sql server
	public function loginTest5(){
		// Hacer el INNER JOIN con la tabla usuarios en SQL Server
		$query_beneficios = $this->beneficio->query("SELECT * FROM usuarios");
		$result_beneficios = $query_beneficios->result();
	
		// Hacer el INNER JOIN con la vista v_usuarios en MySQL
		$query_ch = $this->ch->query("SELECT * FROM v_usuarios");
		$result_ch = $query_ch->result();
	
		$response = $this->combine_results($result_beneficios, $result_ch);
	
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	function combine_results($result_beneficios, $result_ch) {
		$combined_results = array();
	
		// Supongamos que 'idContrato' es la clave común en ambas tablas
		foreach ($result_beneficios as $row_beneficios) {
			foreach ($result_ch as $row_ch) {
				if ($row_beneficios->idContrato == $row_ch->idContrato) {
					// Combina las filas en un solo objeto
					$combined_row = (object) array_merge((array) $row_beneficios, (array) $row_ch);
	
					// Añade el objeto combinado a los resultados combinados
					$combined_results[] = $combined_row;
				}
			}
		}
	
		return $combined_results;
	}

	public function loginFusion ($array = '') {
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		// var_dump($datosEmpleado); exit; die;
		$datosEmpleado->password =  encriptar($datosEmpleado->password);
		$data = $this->UsuariosModel->loginFusion($datosEmpleado->numempleado,$datosEmpleado->password)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function loginFusion2 ($array = '') {
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		// var_dump($datosEmpleado); exit; die;
		$datosEmpleado->password =  encriptar($datosEmpleado->password);
		$data = $this->UsuariosModel->loginFusion2($datosEmpleado->numempleado,$datosEmpleado->password)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function loginFusion3 ($array = '') {
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		// var_dump($datosEmpleado); exit; die;
		$datosEmpleado->password = $datosEmpleado->password;
		$data = $this->UsuariosModel->loginFusion3($datosEmpleado->numempleado,$datosEmpleado->password)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	public function loginFusion4 ($array = '') {
		//session_destroy();
		$datosEmpleado = $array == '' ? json_decode( file_get_contents('php://input')) : json_decode($array);
		// var_dump($datosEmpleado); exit; die;
		$datosEmpleado->password =  $datosEmpleado->password;
		$data = $this->UsuariosModel->loginFusion4($datosEmpleado->numempleado,$datosEmpleado->password)->result();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}
}

?>