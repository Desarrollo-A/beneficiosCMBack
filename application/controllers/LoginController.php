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
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->load->helper(array('form','funciones'));
        $this->load->library("email");
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
        $mail = $this->input->post('dataValue[params][mailForm]');
        $idContrato = $this->input->post('dataValue[params][idcontrato]');
        $mailCh = $this->input->post('dataValue[params][mail_emp]');

        switch ($datosEmpleado['idpuesto']){
            case "158": // QB
                $idRol = 3;
                $areaBeneficio = 1;
                break;
            case "585": // Psicologia
                $idRol = 3;
                $areaBeneficio = 4;
                break;
            case "686": // GUÍA ESPIRITUAL
                $idRol = 3; 
                $areaBeneficio = 3;
                break;
            case "537": // Nutrición
                $idRol = 3;
                $areaBeneficio = 2;
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
            echo json_encode(array("result" => false, "msg" => "No hay información del puesto" ), JSON_NUMERIC_CHECK);
        }else {
            
            $antiguedad = 0;
            $fingreso = $informacionDePuesto->fingreso;
            $dateOfEntry = new DateTime($fingreso);
            $currentDate = new DateTime();

            // Calcula la fecha de hace un mes
            $oneMonthAgo = (clone $currentDate)->sub(new DateInterval('P1M'));

            // Calcula la fecha de hace tres meses
            $threeMonthsAgo = (clone $currentDate)->sub(new DateInterval('P3M'));

            if ($dateOfEntry < $threeMonthsAgo) {
                // Tiene al menos 3 meses de antigüedad
                $antiguedad = 3;
            } elseif ($dateOfEntry < $oneMonthAgo) {
                // Tiene al menos 1 mes de antigüedad
                $antiguedad = 1;
            } else {
                // No tiene un mes de antigüedad
                $antiguedad = 0;
            }

            $canRegister = $informacionDePuesto->canRegister;

            if (($canRegister === 0 || $canRegister === '0' || $canRegister == NULL || $antiguedad < 3) && $informacionDePuesto->idPuesto != 393 && $idRol != 3 ) {
                if ($canRegister === 0 || $canRegister === '0' || $canRegister == NULL) {
                    echo json_encode(array("result" => false, "msg" => "Tu puesto actual no puede gozar de los beneficios"), JSON_NUMERIC_CHECK);
                } else {
                    echo json_encode(array("result" => false, "msg" => "Aún no cuentas con la antigüedad para gozar con los beneficios"), JSON_NUMERIC_CHECK);
                }

            }else if(($canRegister === 0 || $canRegister === '0' || $canRegister == NULL || $antiguedad < 1) && $informacionDePuesto->idPuesto = 393 && $idRol != 3){
                if ($canRegister === 0 || $canRegister === '0' || $canRegister == NULL) {
                    echo json_encode(array("result" => false, "msg" => "Tu puesto actual no puede gozar de los beneficios"), JSON_NUMERIC_CHECK);
                } else {
                    echo json_encode(array("result" => false, "msg" => "Aún no cuentas con la antigüedad para gozar con los beneficios"), JSON_NUMERIC_CHECK);
                }
            }else {
                $usuarioExiste = $this->GeneralModel->usuarioExiste($insertData["idContrato"]);
            
                if($usuarioExiste->num_rows() === 0){
                    $resultado = $this->GeneralModel->addRecordReturnId( $this->schema_cm.".usuarios", $insertData);

                    $this->UsuariosModel->insertTempMail($mail, $idContrato);
                    
                    $insertData = array(
                        "idUsuario" => $resultado,
                        "estatus" => 1,
                        "creadoPor" => 1,
                        "fechaCreacion" => date('Y-m-d H:i:s'),
                        "modificadoPor" => 1,
                        "fechaModificacion" => date('Y-m-d H:i:s')
                    );
    
                    $resultado = $this->GeneralModel->addRecord( $this->schema_cm.".detallepaciente", $insertData);
    
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
		$token = $this->headers('Token');

		$data = explode('.', $token);
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

       
        if(empty($data)){
            echo json_encode(array('response' => [],
                                    'message' => 'El número de empleado no se encuentra registrado',
                                    'result' => 0), JSON_NUMERIC_CHECK);
        }else if($datosEmpleado->password != $data[0]->password){
            echo json_encode(array('response' => [],
                'message' => 'La contraseña es incorrecta',
                'result' => 0), JSON_NUMERIC_CHECK);
        }else if($data[0]->activo == "0"){
            echo json_encode(array('response' => [],
            'message' => 'El colaborador no se encuentra activo',
            'result' => 0), JSON_NUMERIC_CHECK);
        }
        else{
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
                'permisos'              =>      $data[0]->permisos,
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

    public function recuperarPassword(){
        $dataValue = $this->input->post("dataValue");
        $noEmp = $dataValue["noEmp"];
        
        $getUsuario = $this->UsuariosModel->getCorreoEmpleado($noEmp)->result(); // se obtiene el correo del usuario activo en base al no de empleado
        $fecha = date('Y-m-d H:i:s');

        $this->ch->trans_begin();
        
        if(!empty($getUsuario)){ //  se valida que venga un dato en el result
            $mailUsuario = $getUsuario[0]->mail_emp;
            $token = $data["data"] = substr(md5(time()), 0, 6);

            $deleteToken = $this->UsuariosModel->deleteToken($mailUsuario); // se borra los tokens que se hayan almacenado anteriormente

            $dataMail["data"] = array(
                "nombre" => $getUsuario[0]->nombreUsuario,
                "noEmp" => $getUsuario[0]->num_empleado,
                "token" => $token
            );

            $data["mail"] = $mailUsuario;
            $data["view"] = "email-password";

		    $config['protocol']  = 'smtp';
		    $config['smtp_host'] = 'smtp.gmail.com';
		    $config['smtp_user'] = 'no-reply@ciudadmaderas.com';
		    $config['smtp_pass'] = 'JDe64%8q5D';
		    $config['smtp_port'] = 465;
		    $config['charset']   = 'utf-8';
		    $config['mailtype']  = 'html';
		    $config['newline']   = "\r\n";
		    $config['smtp_crypto']   = 'ssl';

            $html_message = $this->load->view($data["view"], $dataMail, true); // la variable de data["view"] para cargar una vista dinamica

            $this->email->initialize($config);
		    $this->email->from("testemail@ciudadmaderas.com"); // from("no-reply@ciudadmaderas.com");
		    $this->email->to($data["mail"]);
		    $this->email->message($html_message);
		    $subject = "Beneficios CM | Recuperar contraseña- " . $fecha;
		    $this->email->subject($subject);

		    if ($this->email->send()) {
                $saveToken = $this->UsuariosModel->saveToken($mailUsuario, $token); // se guarda el registro en la tabla

                $this->ch->trans_commit();
		    	$response["result"] = true;
		    	$response["msg"] = "Se ha enviado el correo";
                $response["mailEmp"] = $mailUsuario;
		    }
		    else {
                $this->ch->trans_rollback();
		    	$response["result"] = false;
		    	$response["msg"] = "Error al enviar el correo";                
		    }
        }
        else{
            $this->ch->trans_rollback();
            $response["result"] = false;
            $response["msg"] = "El número de empleado no está registrado";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function validarNumEmp(){
        $dataValue = $this->input->post("dataValue");
        $noEmp = $dataValue["noEmp"];

        $getCorreo = $this->UsuariosModel->getCorreoEmpleado($noEmp)->result();

        if(!empty($getCorreo)){
            $mailEmp = $getCorreo[0]->mail_emp; // correo de usuario
            $idEmp = $getCorreo[0]->idUsuario; // id de empleado
            
            $deleteToken = $this->UsuariosModel->deleteOldToken($mailEmp); // esta consulta es para eliminar el registro de los tokens que llevan mas de 5 minutos
            $checkToken = $this->UsuariosModel->checkTokenByMail($mailEmp)->result(); // para checkar si tiene algun token valido 

            if(!empty($checkToken)){
                $response["result"] = true;
                $response["msg"] = "Token valido disponible!";
                $response["mailEmp"] = $mailEmp;
                $response["idEmp"] = $idEmp;
            }
            else{
                $response["result"] = false;
                $response["msg"] = "Sin tokens validos";
            }            
        }
        else{
            $response["result"] = false;
            $response["msg"] = "El número de empleado no esta registrado";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function guardarNuevaPassword(){
        date_default_timezone_set('America/Mexico_City'); // zona default horario

        $dataValue = $this->input->post("dataValue");
        $noEmp = $dataValue["noEmp"];
        $code = $dataValue["code"];
        $password = $dataValue["password"];
        $mailEmp = $dataValue["mailEmp"];
        $idEmp = $dataValue["idEmp"];
        $banderaSuccess = true; // bandera para saber los procesos que pasan correctamente

        if(isset($noEmp) && isset($password) && isset($mailEmp) && isset($code) && isset($idEmp)){
            $this->ch->trans_begin();

            // se checa si existe el token
            $checkToken = $this->UsuariosModel->checkToken($code)->result();
            if(empty($checkToken)){ // si no hay resultados en buscar el token, se da false
                $banderaSuccess = false;
                $msg = "Token incorrecto";
            }
            else{
                $updateData = array( // datos a modificar en el usuario
                    "password" => encriptar($password),
                    "modificadoPor" => $idEmp,
                    "fechaModificacion" => date('Y-m-d H:i:s')
                );

                // despúes se hace el cambio de la contraseña y se comprueba que se haya hecho el cambio
                $update = $this->GeneralModel->updateRecord($this->schema_cm.".usuarios", $updateData, "idUsuario", $idEmp);
                if(!$update){
                    $banderaSuccess = false;
                    $msg = "Error al actualizar la contraseña!";
                }
                else{
                    // si existe el token se borra
                    $deleteToken = $this->UsuariosModel->deleteToken($mailEmp);
                }
            }
                
            if($banderaSuccess){
                $this->ch->trans_commit();
                $response["result"] = true;
                $response["msg"] = "Se ha actualizado la contraseña!";
            }
            else{
                $this->ch->trans_rollback();
                $response["result"] = false;
                $response["msg"] = $msg;
            }
        }
        else{
            $this->ch->trans_rollback();
            $response["result"] = false;
            $response["msg"] = "Faltan datos en el formulario!";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
}

?>
