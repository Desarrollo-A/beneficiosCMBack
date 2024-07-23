<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Especialistas extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->ch = $this->load->database('ch', TRUE);
        $this->load->model('SedesModel');
        $this->load->model('EspecialistasModel');
        $this->load->model('CitasModel');
        $this->load->model('GeneralModel');
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function sedes(){
        $idEspecialista = $this->input->get('idEspecialista');

        $sedes = $this->SedesModel->getPresencialXEspecialista($idEspecialista);

        $this->json($sedes);
    }

    public function horario(){
        $data = $this->input->post('dataValue', true);

        $sede = $data['sede'];
        $especialista = $data['especialista'];

        $start = new DateTime($data['start']);
        $interval = new DateInterval('P1D');
        $end = new DateTime($data['end']);
        $end->modify('+1 day'); //Esto para incluir el ultimo dia, en PHP 8 se usa DatePeriod::INCLUDE_END_DATE

        $period = new DatePeriod(
            $start,
            $interval,
            $end
        );

        if(!iterator_count($period)){
            $data["result"] = false;
            $data["msg"] = 'Rango de fechas erróneo';
        
        }

        foreach ($period as $date) {
            $today = new DateTime();

            if($date < $today){

                $data["result"] = false;
                $data["msg"] = 'No puedes cambiar un horario de un día que ya paso.';
            }

            $start_day = $date->format("Y-m-d 00:00:00");
            $end_day = $date->format("Y-m-d 23:59:59");

            $has_citas = $this->CitasModel->getCitasPendientes($especialista, $sede, $start_day, $end_day);
            
            if($has_citas){
                $day = $date->format("Y-m-d");
                $data["result"] = false;
                $data["msg"] = 'No puedes cambiar el horario el'. $day .', por que tienes citas pendientes.';
            }

            if($sede != 0){
                $check_exist = $this->SedesModel->checkExist($date->format("Y-m-d"), $sede, $especialista);
                if($check_exist->num_rows() > 0){
                    $id = $check_exist->result();

                   
                    $values = [
                        "idSede" => $sede
                    ];

                    $is_ok = $this->GeneralModel->updateRecord($this->schema_cm .'.presencialxsede', $values, 'idEvento', $id[0]->idEvento);
                }
                else{
                    $is_ok = $this->SedesModel->addHorarioPresencial($date->format("Y-m-d"), $sede, $especialista);
                }
            }else{
                $is_ok = $this->SedesModel->deleteHorarioPresencial($date->format("Y-m-d"), $sede, $especialista);
            }
        }

        if($is_ok){
            $data["result"] = true;
            $data["msg"] = 'Horario establecido';
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
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
        $mes = $this->input->get('mes');

        $result = [
            'meta' => $this->EspecialistasModel->getMeta($especialista)->meta,
            'total' => $this->EspecialistasModel->getTotal($especialista, $mes),
        ];

        $this->json($result);
    }

    public function area(){
        $area = $this->input->get('area');

        $especialistas = $this->EspecialistasModel->getEspecialistasPorArea($area);

        $this->json($especialistas);
    }
}