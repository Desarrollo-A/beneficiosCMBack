<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

require_once './vendor/autoload.php';  // Incluye el autoload de Composer
use Firebase\JWT\JWT;  // Usa la clase JWT de Firebase

class Api extends BaseController{
    public function __construct(){
		parent::__construct();
		$this->load->model('GeneralModel');
		$this->load->model('CalendarioModel');
        $this->load->library("email");
        $this->load->library('GoogleApi');
        $this->load->library('jwt_actions');
		$this->load->helper(array('form','funciones'));
		$this->ch = $this->load->database('ch', TRUE);
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

    public function index()
	{
		$this->load->view('welcome_message');
	}

    /* AUTHENTIFICACIÓN DE CRM */

    function getToken() {
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id))
            echo json_encode(array("status" => -1, "message" => "Algún parámetro no viene informado."), JSON_UNESCAPED_UNICODE);
        else {
            if ($data->id == "")
                echo json_encode(array("status" => -1, "message" => "Algún parámetro no tiene un valor especificado."), JSON_UNESCAPED_UNICODE);
            else {
                if (!in_array($data->id, array(9860, 2000)))
                    echo json_encode(array("status" => -1, "message" => "Sistema no reconocido."), JSON_UNESCAPED_UNICODE);
                else {
                    if ($data->id == 9860) // EJEMPLO
                        $arrayData = array("username" => "ojqd58DY3@", "password" => "I2503^831NQqHWxr");
                    else if ($data->id == 2000) // LEGALARIO
                        $arrayData = array("username" => "legalario", "password" => "JExFR0FMQVJJTzIwMDAk");
                    $time = time();
                    $JwtSecretKey = $this->jwt_actions->getSecretKey($data->id);
                    $data = array(
                        "iat" => $time, // Tiempo en que inició el token
                        "exp" => $time + (24 * 60 * 60), // Tiempo en el que expirará el token (24 horas)
                        "data" => $arrayData,
                    );
                    $token = JWT::encode($data, $JwtSecretKey);
                    echo json_encode(array("id_token" => $token));
                }
            }
        }
    }

    /* FIN AUTHENTIFICACIÓN DE CRM */

	public function encodedHash()
	{
		$hash = $this->input->post('dataValue[hash]');
		
		$response['result'] = isset($hash);
		if ($response['result']) {
			$key = APPPATH.'..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'private_key_BeneficioMaderas.pem';
			$nvoHash = SignData($hash, $key);
			$response['data'] = trim($nvoHash);
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function confirmarPago()
    {
        $fecha = date('Y-m-d H:i:s');
        $usuario = 1; //Banco
        $folio = $this->input->post('cl_folio');
        $referencia = $this->input->post('cl_referencia');
        $concepto = $this->input->post('t_concepto');
        $cantidad = $this->input->post('dl_monto');
        $metodoPago = $this->input->post('nl_tipoPago');
        $estatusPago = $this->input->post('nl_status');
        $fechaPago = $this->input->post('dt_fechaPago');
        $hash = $this->input->post('hash');

        // $values = [
        //     "folio" => $folio,
        //     "idConcepto" => $concepto,
        //     "referencia" => $referencia,
        //     "cantidad" => $cantidad,
        //     "metodoPago" => $metodoPago,
        //     "estatusPago" => $estatusPago,
        //     "fechaPago" => $fechaPago,
        //     "estatus" => 0,
        //     "creadoPor" => 666,
        //     "fechaCreacion" => $fecha,
        //     "modificadoPor" => 666,
        //     "fechaModificacion" => $fecha
        // ];
        // $rs = $this->GeneralModel->addRecord($this->schema_cm.".detallepagos", $values);

        $cadena = $folio.'|'.$concepto.'|'.$referencia.'|'.$cantidad.'|'.$fechaPago.'|'.$metodoPago.'|'.$estatusPago.'|';
		$key = APPPATH . '..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'public_key_BB.pem';
		// $response['result'] = VerifyData($hash, $cadena, $key);
		// $response['result'] =  $cadena == $hash;
		// if ($response['result']) {
			$values = [
				"folio" => $folio,
				"idConcepto" => $concepto,
				"referencia" => $referencia,
				"cantidad" => $cantidad,
				"metodoPago" => $metodoPago,
				"estatusPago" => $estatusPago,
				"fechaPago" => $fechaPago,
				"estatus" => 1,
				"creadoPor" => $usuario,
				"fechaCreacion" => $fecha,
				"modificadoPor" => $usuario,
				"fechaModificacion" => $fecha
			];
			$rs = $this->GeneralModel->addRecordReturnId($this->schema_cm .".detallepagos", $values);
			$response["result"] = $rs > 0;
			if ($response["result"]) {
				if (isset($rs)) {					
					$partes = explode('-', $referencia); // Sacamos el ultimo dato de la referencia
					$idCita = substr(end($partes), 1); //Cortamos la inicial del dato que es una letra para extraer solo el numero del id
                    
					if ($concepto == 1) { // Actualizamos el id de cita
						$upd = [
							"idDetalle" => $rs,
							"estatusCita" => $estatusPago == 2 ? null : 1,
							"modificadoPor" => $usuario,
							"fechaModificacion" => $fecha,
						];
						$response["result"] = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $upd, 'idCita', $idCita);
						if ($response["result"]) {
							$response["msg"] = "estatus_notificacion=0";
                            $this->creaEventoGoogleYNotifica($idCita);
                            $this->creaEvaluaciones($idCita);
                        }else {
							$response["msg"] = "¡Surgió un error al enlazar la cita con el pago!";
						}
					}else {
						$response["msg"] = "¡Se ha generado el detalle de pago con éxito!";
					}
				} else {
					$response["msg"] = 'No se encontró la información del detalle de pago';
				}
			} 
			else {
				$response["msg"] = "¡Surgió un error al intentar registrar el detalle de pago!";
			}
		// } else{
		// 	$response['msg'] = "¡Parámetros inválidos!";
		// }

        echo 'estatus_notificacion=0';
    }

    public function creaEventoGoogleYNotifica($idCita){
        $response['result'] = isset($idCita);
        if ($response['result']) {
            $rs = $this->CalendarioModel->getCitaById($idCita)->result();

            /* PROCESO DE MANDAR CORREO AL USUARIO */
            $fecha = date('Y-m-d H:i:s');
		    $config['protocol']  = 'smtp';
		    $config['smtp_host'] = 'smtp.gmail.com';
		    $config['smtp_user'] = 'no-reply@ciudadmaderas.com';
		    $config['smtp_pass'] = 'JDe64%8q5D';
		    $config['smtp_port'] = 465;
		    $config['charset']   = 'utf-8';
		    $config['mailtype']  = 'html';
		    $config['newline']   = "\r\n";
		    $config['smtp_crypto']   = 'ssl';

            $data = [
                "idUsuario" => $rs[0]->idPaciente,
                "view" => 'email-appointment',
                "tituloEmail" => 'Reservación',
                "temaEmail" => 'Has realizado con éxito tu reservación para el beneficio de ',
                "especialidad" => $rs[0]->beneficio,
                "especialista" => $rs[0]->especialista,
                "sede" => $rs[0]->sede,
                "oficina" => $rs[0]->ubicación,
                "fecha" => date('d/m/Y', strtotime($rs[0]->start)),
                "horaInicio" => date('h:i a', strtotime($rs[0]->start)),
                "horaFinal" => date('h:i a', strtotime($rs[0]->end)),
                "correo" => [$rs[0]->correo]
            ];

            $data2 = [
                "data" => $data,
            ];

		    $html_message = $this->load->view($data["view"], $data2, true); // la variable de data["view"] para cargar una vista dinamica

		    $this->email->initialize($config);
		    $this->email->from("no-reply@ciudadmaderas.com");
		    $this->email->to($data["correo"]); // 'correo' or 'correo, correo1' or [correo, correo1, correo2].
		    $this->email->message($html_message);
		    $subject = "Citas Beneficios CM - " . $fecha;
		    $this->email->subject($subject);

            $response["result"] = $this->email->send();

		    if ($response["result"]) {
		    	$response["msg"] = "Se ha enviado el correo";
		    }
		    else {
		    	$response["msg"] = "Error al enviar el correo";
		    }

		    $logData['fromEmail'] = $config['smtp_user'];
		    $logData['toEmail'] = implode(', ', $data["correo"]); // ['pedro', 'luis', 'equis']
		    $logData['subject'] = $subject;
		    $logData['content'] = $data["view"];
		    $logData['result'] = $response["result"];
		    $logData['estatus'] = 1;
		    $logData['creadoPor'] = $data["idUsuario"];
		    $logData['fechaCreacion'] = $fecha;
		    $logData['modificadoPor'] = $data["idUsuario"];
		    $logData['fechaModificacion'] = $fecha;

		    $this->GeneralModel->addRecord( $this->schema_cm.".emaillogs", $logData);

            // COMIENZA PROCESO DE EVENTO DE GOOGLE
            $response["result2"] = (!isset($rs[0]->idEventoGoogle) OR $rs[0]->idEventoGoogle === "");
            if ($response["result2"]) {
                $data = [
                    "email" => $rs[0]->correo,
                    "title" => $rs[0]->title,
                    "location" => $rs[0]->ubicación,
                    "description" => $rs[0]->title,
                    "start" => date('Y-m-d\TH:i:s', strtotime($rs[0]->start)),
                    "end" => date('Y-m-d\TH:i:s', strtotime($rs[0]->end)),
                    "attendees" => array(
                        array(
                            "email" => $rs[0]->correo,
                            "responseStatus" => "accepted"
                        ),
                        array(
                            "email" => $rs[0]->correoEspecialista,
                            "responseStatus" => "accepted"
                        )
                    ),
                ];

                $apivalue = $this->googleapi->getAccessToken($data["email"]);

                $data = json_encode(array(
                    'summary' => $data["title"],
                    'location' => $data["location"],
                    'description' => $data["description"],
                    'start' => array(
                        'dateTime' => $data["start"],
                        'timeZone' => 'America/Mexico_City',
                    ),
                    'end' => array(
                        'dateTime' => $data["end"],
                        'timeZone' => 'America/Mexico_City',
                    ),
                    'attendees' => $data["attendees"],
                    'source' => [
                        'title' => 'Beneficios Maderas',
                        'url' => 'https://beneficiosmaderas.gphsis.com/'
                    ],
                    'reminders' => array(
                        'useDefault' => FALSE,
                        'overrides' => array(
                            array('method' => 'email', 'minutes' => 24 * 60), // 1 dia antes
                            array('method' => 'email', 'minutes' => 4 * 60), // 4 horas antes
                            array('method' => 'popup', 'minutes' => 24 * 60), // 1 dia antes
                            array('method' => 'popup', 'minutes' => 4 * 60), // 4 horas antes
                        ),
                    ),
                    'visibility' => 'public',
                    'colorId' => '07'
                ), JSON_NUMERIC_CHECK);

                if ($apivalue == 1) {
                    $event = $this->googleapi->createCalendarEvent('primary', $data);
                    $response['result2'] = !isset($event->error);

                    if ($response['result2']) {
                        $response['msg2'] = "¡Evento registrado en el calendario de google!";
                        $upd = [
                            "idEventoGoogle" => $event->id,
                            "modificadoPor" => $rs[0]->idPaciente,
                            "fechaModificacion" => $fecha,
                        ];
                        $response["result3"] = $this->GeneralModel->updateRecord($this->schema_cm .".citas", $upd, 'idCita', $idCita);
				    	if ($response["result3"]) {
				    		$response["msg3"] = "Evento de google creado exitosamente";
                        }else {
				    		$response["msg3"] = "¡Surgió un error al enlazar la cita con el pago!";
				    	}
                    }else {
                        $response['msg2'] = "¡No se pudo insertar el evento en el calendario de google!"; 
                    }
                }else {
                    $response['msg2'] = "¡No se pudo insertar el evento en el calendario de google debido a proveedor!"; 
                }
            }else {
                $response['msg2'] = "¡Evento con id listo!"; 
            }
        }else {
            $response['msg'] = "¡Parámetros inválidos!";
        }

        // $this->output->set_content_type('application/json');
		// $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

    public function creaEvaluaciones($idCita){
        $response['result'] = isset($idCita);
        if ($response['result']) {

            $data = [
                'idCita' => $idCita
            ];
            $this->GeneralModel->addRecord($this->schema_cm.".evaluacionencuestas", $data);

        }else {
            $response['msg'] = "¡Parámetros inválidos!";
         }
    }

    // public function notificacionesLegalario(){
	// 	$auth = $this->headers('Authorization');
	// 	$token = null;
    //     $json = file_get_contents('php://input');
    //     $data = json_decode($json, true);

	// 	$fecha = date('Y-m-d H:i:s');

	// 	$result = (object) [
	// 		'result' => false,
	// 		'msg' => 'Error'
	// 	];

	// 	if(!isset($auth)){
	// 		$result->msg = 'Falta el header de Authorization';
	// 		$this->json($result, JSON_NUMERIC_CHECK);
	// 	}

	// 	$matches = array();
	//     if (preg_match('/Bearer (.+)/', $auth, $matches)) {
	//         if (isset($matches[1])) {
	//             $token = $matches[1];
	//         }
	//     }

	//     if(!isset($token)){
	// 		$result->msg = 'Falta token de Authorization';
	// 		$this->json($result, JSON_NUMERIC_CHECK);
	// 	}

	// 	$decoded = (object) $this->token->validateToken($token);

	// 	if(!$decoded->status){
	// 		$result->msg = $decoded->message;
	// 		$this->json($result, JSON_NUMERIC_CHECK);
	// 	}

	// 	$usuario = $decoded->data;

	// 	if($usuario->status != 1){
	// 		$result->msg = 'No tiene permisos para esta acción';
	// 		$this->json($result, JSON_NUMERIC_CHECK);
	// 	}

	// 	// if(!isset($data)){
	// 	// 	$result->msg = 'No hay datos';
	// 	// 	$this->json($result, JSON_NUMERIC_CHECK);
	// 	// }

	// 	$response["result"] = true;
	// 	$response["msg"] = "¡Datos recibidos!";
	// 	// $response["data"] = $data;

	// 	// $updated = $this->GeneralModel->updateRecord($this->schema_cm .".usuarios", $data, "idContrato", $idContrato);
	// 	// $cancela = $this->CalendarioModel->cancelaCitasPorBajaUsuario($idContrato);

	// 	// if($updated && $cancela){
	// 	// 	$result->result = true;
	// 	// 	$result->msg = 'Proceso completo exitoso';
	// 	// }else{
	// 	// 	$result->msg = 'No se pudo dar de baja el empleado';
	// 	// }

	// 	$this->output->set_content_type('application/json');
	// 	$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	// }

    function notificacionesLegalario() { 
        if (!isset(apache_request_headers()["Authorization"])){
            echo json_encode(array("status" => -1, "message" => "La petición no cuenta con el encabezado Authorization."), JSON_UNESCAPED_UNICODE);
        }else{
            if (apache_request_headers()["Authorization"] == ""){
                echo json_encode(array("status" => -1, "message" => "Token no especificado dentro del encabezado Authorization."), JSON_UNESCAPED_UNICODE);
            }else{
                $token = apache_request_headers()["Authorization"];
                $JwtSecretKey = $this->jwt_actions->getSecretKey(2000); 
                $valida_token = json_decode($this->validateToken($token, 2000));
                if ($valida_token->status !== 200){
                    echo json_encode($valida_token);
                }else {
                    $result = JWT::decode($token, $JwtSecretKey, array('HS256'));
                    $valida_token = Null;
                    foreach ($result->data as $key => $value) {
                        if(($key == "username" || $key == "password") && (is_null($value) || str_replace(" ","",$value) == '' || empty($value)))
                            $valida_token = false;
                    }
                    if(is_null($valida_token)){
                        $valida_token = true;
                    }
                    if(!empty($result->data) && $valida_token){
                        $checkSingup = $this->jwt_actions->validateUserPass($result->data->username, $result->data->password);
                    }else{
                        $checkSingup = null;
                        echo json_encode(array("status" => -1, "message" => "Algún parámetro (usuario y/o contraseña) no vienen informados. Verifique que ambos parámetros sean incluidos."), JSON_UNESCAPED_UNICODE);
                    }
                    if(!empty($checkSingup) && json_decode($checkSingup)->status == 200){
                        echo json_encode(array("status" => 1, "message" => "Registro guardado con éxito"), JSON_UNESCAPED_UNICODE);
                    } else
                        echo json_encode($checkSingup);
                }
            }
        }
    }

    function validateToken($token, $controller = null)
    {
        $time = time();
        if (is_null($controller))
            $JwtSecretKey = $this->jwt_key->getSecretKey();
        else
            $JwtSecretKey = $this->jwt_actions->getSecretKey($controller);
            $result = JWT::decode($token, $JwtSecretKey, array('HS256'));
        if (in_array($result, array('ALR001', 'ALR003', 'ALR004', 'ALR005', 'ALR006', 'ALR007', 'ALR008', 'ALR009', 'ALR010', 'ALR012', 'ALR013'))) {
            return json_encode(array("timestamp" => $time, "status" => 503, "error" => "Servicio no disponible", "exception" => "Servicio no disponible", "message" => "El servidor no está listo para manejar la solicitud. Por favor, inténtelo de nuevo más tarde."));
        } else if ($result == 'ALR002') {
            return json_encode(array("timestamp" => $time, "status" => 400, "error" => "Solicitud incorrecta", "exception" => "Número incorrecto de parámetros", "message" => "Verifique la estructura del token enviado."));
        } else if ($result == 'ALR011') {
            return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Verificación de firma fallida", "message" => "Estructura no válida del token enviado."));
        } else if ($result == 'ALR014') {
            return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Token caducado", "message" => "El tiempo de vida del token ha expirado."));
        } else {
            return json_encode(array("status" => 200, "message" => "Autenticado con éxito."));
        }
    }
}