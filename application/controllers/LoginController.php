<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class LoginController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('UsuariosModel');
		$this->load->model('GeneralModel');
		$this->load->model('MenuModel');
        $this->ch = $this->load->database('ch', TRUE);

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
        $this->ch->trans_begin();
        $datosEmpleado = $this->input->post('dataValue[params]');

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
            "password" => isset($datosEmpleado['password']) ? encriptar($datosEmpleado['password']) : NULL,
            "idRol" => $idRol,
            "externo" => 0,
            "idAreaBeneficio" => $areaBeneficio,
            "estatus" => 1,
            "creadoPor" => 1,
            "fechaCreacion" => date('Y-m-d H:i:s'),
            "modificadoPor" => 1,
            "fechaModificacion" => date('Y-m-d H:i:s'),
        );

        $informacionDePuesto = $this->GeneralModel->getInfoPuesto($datosEmpleado['idcontrato'])->row();
        
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
                    $last_id = $this->ch->insert_id();
                    var_dump($last_id);
                    
                    $insertData = array(
                        "idUsuario" => $last_id,
                        "estatus" => 1,
                        "creadoPor" => 1,
                        "fechaCreacion" => date('Y-m-d H:i:s'),
                        "modificadoPor" => 1,
                        "fechaModificacion" => date('Y-m-d H:i:s')
                    );
    
                    $resultado = $this->GeneralModel->addRecord('detallepaciente', $insertData);
    
                    if ($this->ch->trans_status() === FALSE){
                        $this->ch->trans_rollback();
                        
                        if(strpos($resultado['message'], "UNIQUE")){
                            echo json_encode(array("result" => false, "msg" => "El número de empleado ingresado ya se encuentra registrado" ), JSON_NUMERIC_CHECK);
                        }else{
                            echo json_encode(array("result" => false, "msg" => "Hubo un error al registrarse" ), JSON_NUMERIC_CHECK);
                        }
                    } else {
                        $this->ch->trans_commit();
                        echo json_encode(array("result" => true, "msg" => "Te has registrado con éxito"), JSON_NUMERIC_CHECK);
                    }
                }
                else{
                    echo json_encode(array("result" => false, "msg" => "El número de empleado ingresado ya se encuentra registrado" ), JSON_NUMERIC_CHECK);
                }
            }
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
        $datosEmpleado->password = encriptar($datosEmpleado->password);
        $data = $this->UsuariosModel->login($datosEmpleado->numempleado,$datosEmpleado->password)->result();
        // var_dump($data); exit; die;
        if(empty($data)){
            echo json_encode(array('response' => [],
                                    'message' => 'El número de empleado no se encuentra registrado',
                                    'result' => 0), JSON_NUMERIC_CHECK);
        }else{
            $datosSesion = array(
                'id_usuario'            =>      $data[0]->idUsuario,
                'idContrato'            =>      $data[0]->idContrato,
                'numEmpleado'           =>      $data[0]->numEmpleado,
                'nombre'                =>      $data[0]->nombre,
                'telPersonal'           =>      $data[0]->telPersonal,
                'correo'                =>      $data[0]->correo,
                'idPuesto'              =>      $data[0]->idPuesto,
                'sede'                  =>      $data[0]->idSede,
                'idArea'                =>      $data[0]->idArea,
            );
            date_default_timezone_set('America/Mexico_City');
            $time = time();
            $tokenPart1 = base64_encode(json_encode(array("alg" => "HS256", "typ"=>"JWT"), JSON_NUMERIC_CHECK));
            $tokenPart2 = base64_encode(json_encode(array("numEmpleado" => $data[0]->numEmpleado, "iat" => $time,"exp" => $time + (24 * 60 * 60)), JSON_NUMERIC_CHECK));
            $tokenPart3 = base64_encode(json_encode($data[0], JSON_NUMERIC_CHECK));
            $datosSesion['token'] = $tokenPart1.'.'.$tokenPart2;
            $this->session->set_userdata($datosSesion);
            if($array == ''){
                echo json_encode(array( 'user' => $data[0], 'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3, 'result' => 1), JSON_NUMERIC_CHECK);
            } else{
                return json_encode(array('user' => $data[0], 'accessToken' => $tokenPart1.'.'.$tokenPart2.'.'.$tokenPart3, 'result' => 1), JSON_NUMERIC_CHECK);
            }               
        }
    }
}

?>
