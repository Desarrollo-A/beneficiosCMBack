<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Especialistas extends BaseController{
    public function __construct(){
        parent::__construct();

        $this->load->model('SedesModel');
    }

    public function sedes(){
        $idEspecialista = $this->input->get('idEspecialista');

        $sedes = $this->SedesModel->getPresencialXEspecialista($idEspecialista);

        echo json_encode($sedes);
    }

    public function horario(){
        $data = $this->post();

        $occuped = $this->SedesModel->checkIfPresencial($data->start, $data->end, $data->sede, $data->especialista);

        if($occuped){
            $response = [
                'status' => 'error',
                'message' => 'El horario esta ocupado'
            ];

            $this->json($response);
        }

        $is_ok = $this->SedesModel->addHorarioPresencial($data->start, $data->end, $data->sede, $data->especialista);

        if($is_ok){
            $response = [
                'status' => 'ok',
                'message' => 'Horario establecido',
            ];

            $this->json($response);
        }
    }
}