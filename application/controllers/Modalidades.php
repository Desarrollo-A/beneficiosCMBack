<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Modalidades extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');

        $this->load->model('ModalidadesModel');
    }

    public function list(){
        $modalidades = $this->ModalidadesModel->getModalidades();

        $this->json($modalidades);
    }
}

?>