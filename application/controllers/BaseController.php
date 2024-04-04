<?php

abstract class BaseController extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->database('default');

        $this->load->model('UsuariosModel');

        //$this->load->helper(array('form','funciones'));

        //$this->load->library('Token');
        //$this->load->library('GoogleApi');
        date_default_timezone_set('America/Mexico_City');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token, Authorization');

        $urls = array('192.168.30.128/auth/jwt/login','localhost','http://localhost','http://localhost:3030','http://192.168.30.128/auth/jwt/login','192.168.30.128','http://192.168.30.128:3030','127.0.0.1','https://rh.gphsis.com','rh.gphsis.com', 'https://prueba.gphsis.com/beneficiosmaderas', 'prueba.gphsis.com/beneficiosmaderas', 'https://prueba.gphsis.com', 'prueba.gphsis.com', 'https://beneficiosmaderasapi.gphsis.com', 'beneficiosmaderasapi.gphsis.com');
        
        // Lineas para la verificación de 
        $allowed_routes = ['LoginController/login', 'Usuario/getUserByNumEmp', 'Usuario/sendMail', 'Usuario/GetToken', 'LoginController/addRegistroEmpleado',
                            "Usuario/authorized", "Api/confirmarPago", "Api/encodedHash", "Usuario/loginCH", "Usuario/updateCH", "Usuario/bajaCH"];

        $uri = $this->uri->uri_string();

        if(!in_array($uri, $allowed_routes)){
            $response['status'] = 'error';

            if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
                $token = $this->headers('Token');

                if($token){
                    $datosToken = json_decode(base64_decode(explode(".", $token)[1]));

                    $numEmpleado = $datosToken->numEmpleado;

                    $data = $this->UsuariosModel->getUserByNumEmpleado($numEmpleado)->row();

                    if(!$data){
                        $response['msg'] = 'Token invalido';
                        $this->json($response);
                    }
                }else{
                    $response['msg'] = 'Falta token';
                    $this->json($response);
                }
            }
        }
    }

    public function headers($key = null){
        $key = strtolower($key);

        $data = $this->input->request_headers();

        if(!isset($data)){
            return;
        }

        if(isset($key)){
            if(isset($data[$key])){
                return $data[$key];
            }elseif (isset($data[ucfirst($key)])) {
                return $data[ucfirst($key)];
            }else{
                return null;
            }
        }

        return (object) $data;
    }

    public function post($key = null){
        $data = json_decode( file_get_contents('php://input'));

        if(!isset($data)){
            return null;
        }

        if(isset($key)){
            if(isset($data->$key)){
                return $data->$key;
            }else{
                return null;
            }
        }

        return $data;
    }

    public function json($object){
        header('Content-Type: application/json');

        echo json_encode($object, JSON_NUMERIC_CHECK);

        exit();
    }

    public function mail($email, $subject, $template){
        mail($email, $subject, $template);
    }

    public function event(){
        $data = [
            
        ];

        $token = array(
            "access_token" => "ya29.a0AfB_byD1S-PR70qgoO6vDwV3gs3rqBeqiIPIXQodhHXHvyGvHOK33AnLdIKJcSNIOl94ApSJXKSMNvF0TwuHQpT_kKq04WwsCpSdqvbt_F3BHbMlqXZrbG3V3wLBzCAivml6AFmBXU-OlwgwWiZ6f5vg0x7ieOk9GgaCgYKASASARESFQHGX2MiZNAeUrZHFoABYlyZpr0mLA0169",
            "authuser" => "0",
            "expires_in" => 3599,
            "hd" => "ciudadmaderas.com",
            "prompt" => "consent",
            "scope" => "email profile https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile openid",
            "token_type" => "Bearer",
        );

        $this->googleapi->createEvent($token, $data);
        echo "ok";
    }

    public function edit_event(){
        $data = [
            
        ];

        $token = array(
            "access_token" => "ya29.a0AfB_byC6s5S-jCTov830ZQILZFoTDQdogiKWsATHxiywpyDLylAezs5EfMV607EWx87VQ3d_4BxGeUNkgKz_Z_mni4KTDcBbPxWhhMHx43XqayqMLoEk0NQ7s5PbLtfTmv-yJwoawj8ozUp88PKzoGw78pWf7tQj2AaCgYKAX0SARESFQHGX2MivXjFVANhStfrCKhNjSwyOg0169",
            "authuser" => "0",
            "expires_in" => 3599,
            "hd" => "ciudadmaderas.com",
            "prompt" => "consent",
            "scope" => "email profile https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile openid",
            "token_type" => "Bearer",
        );

        $id_event = "1slffph3tf5ak69k8o2v3dmd58";

        $refresh_token = "1//0f5CBSyeSsdZKCgYIARAAGA8SNwF-L9Irkxr6LaqYpsRtU6bOHv81cRsjyywfjQGDRDZ8-or4k_dsj_V81g3TI0vU43EnL7ZBfn4";

        $token = $this->googleapi->refreshToken($refresh_token);

        print_r($token);
        exit();

        //$this->googleapi->editEvent($token, $id_event, $data);
    }
}

?>