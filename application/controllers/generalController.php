<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class generalController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
		$this->load->model('generalModel');
	}

	public function usuarios(){
		$data['data'] = $this->generalModel->usuarios();
		echo json_encode($data);
	}

	public function especialistas(){
		$data['data'] = $this->generalModel->especialistas();
		echo json_encode($data);
	}

    public function usrCount(){
		$data['data'] = $this->generalModel->usrCount();
		echo json_encode($data);
	}

    public function citasCount(){
		$data['data'] = $this->generalModel->citasCount();
		echo json_encode($data);
	}
}
