<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class IndexModel extends CI_Model {

	public function __construct()
	{
		$this->ch = $this->load->database('ch', TRUE);
		$this->schema_cm = $this->config->item('schema_cm');
		$this->schema_ch = $this->config->item('schema_ch');
    	parent::__construct();
	}

	public function test($idUsuario){
		$query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".usuarios WHERE idUsuario = ?", $idUsuario);

		return $query;
	}

}
