<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Sedes extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->load->model('EspecialistasModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');

        $this->load->model('SedesModel');
    }

    public function list(){
        $sedes = $this->SedesModel->getSedes();

        $this->json($sedes);
    }
}

?>