<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "controllers/BaseController.php");

class Index extends BaseController{
    public function __construct(){
		parent::__construct();
        /* CARGAMOS MODELOS */
		$this->load->model('GeneralModel');
        $this->load->model('fundaciones/IndexModel');

        
        /* CARGAMOS LIBRERIAS */
        $this->load->library("email");
        $this->load->library('GoogleApi');
		
        /* CARGAMOS LAS FUNCIONES GENERALES */
        $this->load->helper(array('form','funciones'));
		
        /* CARGAMOS LA CONFIGURACIÓN DE LA BD */
        $this->ch = $this->load->database('ch', TRUE);
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

    public function test($idUsuario)
	{
        $response = $this->IndexModel->test();

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}
}