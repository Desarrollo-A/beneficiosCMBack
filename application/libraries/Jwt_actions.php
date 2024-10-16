<?php
require_once './vendor/autoload.php';

use Firebase\JWT\JWT;

    class jwt_actions extends CI_Controller{
        private $_CI;   
        public function __construct()
        {
           

        }

        function authorize_externals($controller, $requestHeaders){
            // $this->helper($requestHeaders);
            if (!isset($requestHeaders) || $requestHeaders == '')
                {echo json_encode(array("status" => 400, "message" => "La petición no cuenta con el encabezado Authorization."), JSON_UNESCAPED_UNICODE);
                die ("Access Denied");       }
            else {
                $tkn = $requestHeaders;
                $response = $this->validateToken_authorize_externals($tkn, $controller);
                $res = json_decode($response);
                if($res->status != 200){
                    die ("Access Denied");
                }
            }
        }

        function authorize($controller, $requestHeaders){
            $this->helper($requestHeaders);
            $tkn = $this->generateToken($controller);
            $response = $this->validateToken_authorize($tkn, $controller);
            $res = json_decode($response);
            if($res->status != 200){
                redirect(base_url().'login');
            }
        }

        function generateToken_externals($controller){
            $CI =& get_instance();
            $CI->load->library('session');
            $time = time();
            $JwtSecretKey = $this->getSecretKey($controller);
            $data = array(
                "iat" => $time, // Tiempo en que inició el token
                "exp" => $time + (24 * 60 * 60), // Tiempo en el que expirará el token (24 horas)
                "data" => array("sistema" => 'CAJA'),
            );
            $token = JWT::encode($data, $JwtSecretKey);
            return $token;
        }

        function generateToken($controller){
            
            $CI =& get_instance();
            $CI->load->library('session');
            $time = time();
            $JwtSecretKey = $this->getSecretKey($controller);
            $data = array(
                "iat" => $time, // Tiempo en que inició el token
                "exp" => $time + (24 * 60 * 60), // Tiempo en el que expirará el token (24 horas)
                "data" => array("id_rol" => $CI->session->userdata('id_rol'), "id_usuario" => $CI->session->userdata('id_usuario')),
            );
            $token = JWT::encode($data, $JwtSecretKey);
            return $token;
        }
    

        function getSecretKey($controller){

            $obj = (object) array(
                '62' => '679231_8076+4591_',
                '2000' => '38hnH3S4LE9KFH99' // LEGALARIO
            );
            return $obj->$controller;
        }

        function getSecretKey2(){
           
            return 'ThisIsMySecretKey';
        }

        function validateToken($token)
        {
            $time = time();
            $JwtSecretKey = $this->getSecretKey2();
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
                return json_encode(array("status" => 200, "message" => "Autenticado con éxito.", "data"=> $result));
            }
        }

        function validateToken_authorize($token, $controller)
        {
            $CI =& get_instance();
            $CI->load->library('session');
            $time = time();
            $JwtSecretKey = $this->getSecretKey($controller);
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
                $validate= true;
                $keys = array_keys((array)$result->data );
                foreach($keys as $key){
                    if($result->data->$key != $CI->session->userdata($key) || $result->data->$key == null){
                        $validate = false;
                    }
                }
                if($validate){
                    return json_encode(array("status" => 200, "message" => "Autenticado con éxito.", "data"=> $result));
                }else{
                    return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Verificación de firma fallida", "message" => "Estructura no válida del token enviado."));
                }
            }
        }

        function validateToken_authorize_externals($token, $controller){
            $CI =& get_instance();
            $CI->load->library('session');
            $time = time();
            $JwtSecretKey = $this->getSecretKey($controller);
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
                if(!$this->getSistema($result->data->username) || $result->data->username == null){
                    $validate = false;
                }else{
                    $validate= true;
                }
                if($validate){
                    return json_encode(array("status" => 200, "message" => "Autenticado con éxito.", "data"=> $result));
                }else{
                    return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Verificación de firma fallida", "message" => "Estructura no válida del token enviado."));
                }
            }
        }

        function helper($requestHeaders){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type,Origin, authorization, X-API-KEY,X-Requested-With,Accept,Access-Control-Request-Method');
            header('Access-Control-Allow-Method: GET, POST, PUT, DELETE,OPTION');
            $urls = array('https://prueba.gphsis.com','prueba.gphsis.com','localhost','http://localhost','127.0.0.1','https://rh.gphsis.com','rh.gphsis.com','https://maderascrm.gphsis.com','maderascrm.gphsis.com');
            date_default_timezone_set('America/Mexico_City');
            

            //echo $_SERVER['HTTP_ORIGIN'];
            if(isset($requestHeaders['origin'])){
                $origin = $requestHeaders;
            }else if(array_key_exists('HTTP_ORIGIN',$_SERVER)){
                $origin = $_SERVER['HTTP_ORIGIN'];
            }else if(array_key_exists('HTTP_PREFERER',$_SERVER)){
                $origin = $_SERVER['HTTP_PREFERER'];
            }
            else{
                $origin = $_SERVER['HTTP_HOST'];
            }
            if(in_array($origin,$urls) || strpos($origin,"192.168")){
              
                }else{
                    die ("Access Denied");       
                }
        }

        function getSistema($sistema){
            $obj = (object) array(
                'caja' => 1,
                'suma_outs_9346' => 2
            );

            if($obj->$sistema)
                return true;
            else
                return false;
        }

        function decodeData($controller, $token){
            $time = time();
            $JwtSecretKey = $this->getSecretKey($controller);
            $result = JWT::decode($token, $JwtSecretKey, array('HS256'));
            if (in_array($result, array('ALR001', 'ALR003', 'ALR004', 'ALR005', 'ALR006', 'ALR007', 'ALR008', 'ALR009', 'ALR010', 'ALR012', 'ALR013'))) {
                return json_encode(array("timestamp" => $time, "status" => 503, "error" => "Servicio no disponible", "exception" => "Servicio no disponible", "message" => "El servidor no está listo para manejar la solicitud. Por favor, inténtelo de nuevo más tarde."));
            } else if ($result == 'ALR002') {
                return json_encode(array("timestamp" => $time, "status" => 400, "error" => "Solicitud incorrecta", "exception" => "Número incorrecto de parámetros", "message" => "Verifique la estructura del token enviado."));
            } else if ($result == 'ALR011') {
                return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Verificación de firma fallida", "message" => "Estructura no válida del token enviado."));
            } else if ($result == 'ALR014') {
                return json_encode(array("timestamp" => $time, "status" => 401, "error" => "No autorizado", "exception" => "Token caducado", "message" => "El tiempo de vida del token ha expirado."));
            } else
                return $result;
        }

        function validateUserPass($userName, $password) {
            if(
                ($userName == 'ojqd58DY3@' && $password == 'I2503^831NQqHWxr') ||
                ($userName == 'legalario' && $password == 'JExFR0FMQVJJTzIwMDAk')
            )
                return json_encode(array("status" => 200, "message" => "Usuario y contraseña autenticados con éxito."));
            else
                return json_encode(array("status" => 404, "message" => "El usuario no se ha podido identificar"));
        }
        
    }
?>