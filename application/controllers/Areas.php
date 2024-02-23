<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Areas extends BaseController{
    public function __construct(){
        parent::__construct();

        $this->load->model('EspecialistasModel');
    }

    public function citas(){
        
        $area = $this->input->get('area');

        if(!$area){
            $area = $this->input->get('puesto');
        }

        $especialistas = $this->EspecialistasModel->getEspecialistasPorArea($area);
        
        $inicio = date('Y-m-01');
        $fin = date('Y-m-t');

        $citas = [];
        foreach ($especialistas as $key => $especialista) {
            $result = [
                'x' => $especialista->nombre,
                'y' => $this->EspecialistasModel->getTotal($especialista->idUsuario, $inicio, $fin),
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => $this->EspecialistasModel->getMeta($especialista->idUsuario)->meta,
                        'strokeColor' => 'red',
                    ]
                ]
            ];

            array_push($citas, $result);
        }

        return $this->json($citas);
    }
}

?>