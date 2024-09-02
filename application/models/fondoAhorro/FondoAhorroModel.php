<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class FondoAhorroModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

    public function getFondo($id)
	{

        $query = $this->ch->query("SELECT *
        FROM fondosahorros 
        WHERE idContrato = $id AND estatusFondo IN(1,2,3,4,5)
		ORDER BY fechaCreacion DESC
		LIMIT 1;");
		return $query;
	}
}