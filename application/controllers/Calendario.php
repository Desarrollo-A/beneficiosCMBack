<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario  extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('CalendarioModel');
	}


	function getBeneficiosDisponibles(){
		$query = $this->CalendarioModel->getBeneficiosDisponibles();
		print_r(json_encode($query));
	}

}
