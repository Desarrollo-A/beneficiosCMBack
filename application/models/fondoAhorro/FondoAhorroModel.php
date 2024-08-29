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

    public function getInformacion()
	{

        $query = $this->ch->query("SELECT idOpcion AS id, nombre 
        FROM opcionesporcatalogo 
        WHERE opcionesporcatalogo.idCatalogo = 20 ORDER  BY idOpcion ASC");
		return $query;

	}

}