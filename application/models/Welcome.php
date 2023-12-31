<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('citasModel');
		$this->load->model('especialistasModel');
	}
	public function index()
	{
		$this->citas();
	}

	public function citas(){
		$data['data'] = $this->citasModel->citas();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->especialistasModel->especialistas();
		echo json_encode($data);
	}
}