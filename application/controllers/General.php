<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('generalModel');
        header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}

	// function insertBatch()
    // {
    //     $obj = [];
    //     $obj['result'] = (isset($_POST['nombretabla']) || isset($_POST['data'])) && !empty($_POST);
    //     if($obj['result'])
    //     {
    //         $table = $_POST['nombretabla'];
    //         $data = $_POST['data'];

    //         $obj['result'] = $this->General_model->getResidencialesList($table, $data);
    //         if ($obj['result'])
    //             $obj['msg'] = "¡Listado insertado exitosamente!";    
    //         else {
    //             $obj['msg'] = "¡Surgió un problema al intentar insertar los datos!";
    //         }
    //     }else {
    //         $obj['msg'] = "Error con los parametros";
    //     }
    //     return echo json_encode($obj);
    // }
    

        // $obj['tabla'] = $table;
        // $obj['data'] = $data;
        // echo json_encode($obj);


    public function insertBatch2()
    {
        $table = $this->input->post('nombreTabla');
        $data = $this->input->post('data');
        $data = array($data);

        $response['result'] = isset($table, $data) && !empty($data);
        if ($response['result']) {
            $response['result'] = $this->generalModel->insertBatch($table, $data);

            if ($response['result']) {
                $response['msg'] = "¡Listado insertado exitosamente!";
            } else {
                $response['msg'] = "¡No se ha podido insertar los datos!";
            }
        } else {
            $response['msg'] = "¡Parametros invalidos!";
        }

        echo json_encode($response);
    }

    public function insertBatch()
    {
        //Recibimos los datos en crudo
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        // Acceso a valores
        $table = $params['nombreTabla'];
        $data = $params['data'];
    
        $response['result'] = isset($table, $data) && !empty($data);
        if ($response['result']) {
            $fecha = date('Y-m-d H:i:s');
            $rows = array();
            foreach ($data as $col) {
                $row = array(
                    'numContrato' => isset($col['contrato']) ? $col['contrato'] : null,
                    'numEmpleado' => isset($col['empleado']) ? $col['empleado'] : null,
                    'nombre' => isset($col['nombre']) ? $col['nombre'] : null,
                    'telPersonal' => isset($col['telPersonal']) ? $col['telPersonal'] : null,
                    'area' => isset($col['area']) ? $col['area'] : null,
                    'oficina' => isset($col['oficina']) ? $col['oficina'] : null,
                    'sede' => isset($col['sede']) ? $col['sede'] : null,
                    'correo' => isset($col['correo']) ? $col['correo'] : null,
                    'password' => isset($col['password']) ? $col['password'] : 'TEMPO01@',
                    'estatus' => 1,
                    'fechaCreacion' => $fecha,
                    'modificadoPor' => 1,
                    'fechaModificacion' => $fecha,
                );
                $rows[] = $row;
            }
        
            $response['result'] = $this->generalModel->insertBatch($table, $rows);
            
            if ($response['result']) {
                $response['msg'] = "¡Listado insertado exitosamente!";
            } else {
                $response['msg'] = "¡No se ha podido insertar los datos!";
            }
        } else {
            $response['msg'] = "¡Parametros invalidos!";
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }

}
