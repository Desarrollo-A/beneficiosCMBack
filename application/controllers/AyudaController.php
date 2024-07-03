<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class AyudaController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->load->model('AyudaModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function getFaqs(){
        $data['data'] = $this->AyudaModel->getFaqs();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getManuales(){
        $data['data'] = $this->AyudaModel->getManuales();
		$this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }
}

?>