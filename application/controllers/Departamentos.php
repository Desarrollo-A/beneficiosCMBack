<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Departamentos extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->load->model('DepartamentosModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');

        $this->load->model('SedesModel');
    }

    public function getDepartamentos(){
        $rs = $this->DepartamentosModel->getDepartamentos();

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($rs, JSON_NUMERIC_CHECK));
    }
}

?>