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

        $citas = [
            0 => [
                'x' => 'Especialista 1',
                'y' => 20,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            1 => [
                'x' => 'Especialista 1',
                'y' => 20,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            2 => [
                'x' => 'Especialista 1',
                'y' => 60,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 150,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            3 => [
                'x' => 'Especialista 1',
                'y' => 50,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            4 => [
                'x' => 'Especialista 1',
                'y' => 20,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            5 => [
                'x' => 'Especialista 1',
                'y' => 40,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            6 => [
                'x' => 'Especialista 1',
                'y' => 20,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            7 => [
                'x' => 'Especialista 1',
                'y' => 30,
                'goals' => [
                    0 => [
                        'name' => 'Meta',
                        'value' => 100,
                        'strokeColor' => 'red',
                    ]
                ]
            ],
            
        ];

        $especialistas = $this->EspecialistasModel->getEspecialistasPorArea($area);
        $inicio = date('Y-m-01');
        $fin = date('Y-m-t');

        //$citas = [];
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

            //array_push($citas, $result);
        }

        return $this->json($citas);
    }
}

?>