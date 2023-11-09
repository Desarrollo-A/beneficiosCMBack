<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
	}
	public function index()
	{
		$this->load->view('welcome_message');
		$this->usuarios();
	}

	public function usuarios(){
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}
	public function login(){
		echo "llego aca";
		$data['data'] = $this->usuariosModel->usuarios();
		echo json_encode($data);
	}
}
