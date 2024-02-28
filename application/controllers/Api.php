<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Api extends BaseController{
    public function __construct(){
		parent::__construct();
		$this->load->model('calendarioModel');

		$this->load->model('GeneralModel');
		$this->load->model('UsuariosModel');
		$this->load->model('EspecialistasModel');
		$this->load->library("email");
		$this->load->library('GoogleApi');
	}

    public function index()
	{
		$this->load->view('welcome_message');
	}

	public function confirmarPago()
	{
        echo('hola');
    }
}