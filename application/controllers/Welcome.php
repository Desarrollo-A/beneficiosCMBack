<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('usuariosModel');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');
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

	public function citas(){
		$data['data'] = $this->usuariosModel->citas();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->usuariosModel->especialistas();
		echo json_encode($data);
	}
}
