<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class AsistenciaEvController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistenciaEv/AsistenciaEvModel');
        $this->load->model('GeneralModel');
        $this->load->library('GoogleApi');
        $this->ch = $this->load->database('ch', TRUE);
        date_default_timezone_set('America/Mexico_City');
        $this->schema_cm = $this->config->item('schema_cm');
    }

    public function getasistenciaEvento()
    {
        $data['data'] = $this->AsistenciaEvModel->getasistenciaEvento();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }
    
    public function getasistenciaEventoUser($idUsuario)
    {
        $data['data'] = $this->AsistenciaEvModel->getasistenciaEventoUser($idUsuario);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }
        
}