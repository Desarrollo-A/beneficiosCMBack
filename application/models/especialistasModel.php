<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class especialistasModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function especialistas()
	{
		$query = $this->db-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1");
		return $query;
	}

}