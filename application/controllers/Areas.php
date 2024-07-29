<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Areas extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->load->model('EspecialistasModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');

        $this->load->model('AreasModel');
    }

    public function citas(){
        
        $area = $this->input->get('areas');
        $mes = $this->input->get('mes');

        $especialistas = $this->EspecialistasModel->getEspecialistasPorArea($area);
        
        $inicio = date('Y-m-01');
        $fin = date('Y-m-t');
        
        $citas = [];
        foreach ($especialistas as $key => $especialista) {
            $result = [
                'x' => $especialista->nombre,
                'y' => $this->EspecialistasModel->getTotal($especialista->idUsuario, $mes),
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => $this->EspecialistasModel->getMeta($especialista->idUsuario)->meta,
                        'strokeColor' => '#2FF665',
                    ]
                ]
            ];

            array_push($citas, $result);
        }

        return $this->json($citas);
    }

    public function list(){
        $areas = $this->AreasModel->getAreas();

        $this->json($areas);
    }
}

?>