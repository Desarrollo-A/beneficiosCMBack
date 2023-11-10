<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 */
class CalendarioModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}


	public function getBeneficiosDisponibles()
	{
		$query = $this->db-> query("SELECT *  FROM usuarios");
		return $query->result_array();
	}


}
