<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Especialistas extends BaseController{
    public function __construct(){
        parent::__construct();

        $this->load->model('SedesModel');
        $this->load->model('EspecialistasModel');
    }

    public function sedes(){
        $idEspecialista = $this->input->get('idEspecialista');

        $sedes = $this->SedesModel->getPresencialXEspecialista($idEspecialista);

        $this->json($sedes);
    }

    public function horario(){
        $data = $this->post();

        $sede = $data->sede;
        $especialista = $data->especialista;

        $start = new DateTime($data->start);
        $interval = new DateInterval('P1D');
        $end = new DateTime($data->end);
        $end->modify('+1 day'); //Esto para incluir el ultimo dia, en PHP 8 se usa DatePeriod::INCLUDE_END_DATE

        $period = new DatePeriod(
            $start,
            $interval,
            $end,
        );

        foreach ($period as $date) {
            if($sede != 0){
                $is_ok = $this->SedesModel->addHorarioPresencial($date->format("Y-m-d"), $sede, $especialista);
            }else{
                $is_ok = $this->SedesModel->deleteHorarioPresencial($date->format("Y-m-d"), $sede, $especialista);
            }
        }

        if($is_ok){
            $response = [
                'status' => 'ok',
                'message' => 'Horario establecido',
            ];

            $this->json($response);
        }
    }

    public function horarios(){
        $idEspecialista = $this->input->get('idEspecialista');

        $horarios = $this->SedesModel->getHorariosEspecialista($idEspecialista);

        $this->json($horarios);
    }

    public function disponibles(){
        $especialista = $this->input->get('especialista');
        $sede = $this->input->get('sede');

        $dias = [];
        if($especialista != '' && $sede != ''){
            $result = $this->SedesModel->getDiasPresencialXEspe($sede, $especialista);

            foreach ($result as $dia) {
                array_push($dias, $dia['presencialDate']);
            }
        }

        $this->json($dias);
    }

    public function meta(){
        $especialista = $this->input->get('especialista');
        $inicio = date('Y-m-01');
        $fin = date('Y-m-t');

        $result = [
            'meta' => $this->EspecialistasModel->getMeta($especialista)->meta,
            'total' => $this->EspecialistasModel->getTotal($especialista, $inicio, $fin),
        ];

        $this->json($result);
    }
}