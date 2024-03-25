<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('EspecialistasModel');
	}
	public function index()
	{
		$this->citas();
	}

	public function citas(){
		echo json_encode('Servicio funcionando...'); 
	}

	public function especialistas(){
		$data['data'] = $this->EspecialistasModel->especialistas();
		echo json_encode($data);
	}
}