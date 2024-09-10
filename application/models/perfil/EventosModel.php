<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class EventosModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

	public function getEventos($idContrato, $idSede, $idDepto){
        $query = $this->ch->query(
			"SELECT *FROM ". $this->schema_cm .".eventos AS ev
            LEFT JOIN ". $this->schema_cm .".asistenciasEventos AS asis ON asis.idEvento = ev.idEvento AND asis.idContrato = ?
            LEFT JOIN ". $this->schema_cm .".alcanceEvento AS alc ON alc.idEvento = ev.idEvento
            WHERE alc.idSede = ? 
              AND alc.idDepartamento = ? 
              AND ev.estatus = 1 
              AND (asis.idContrato IS NULL OR asis.estatus = 1)
              AND alc.estatus = 1;", array($idContrato, $idSede, $idDepto));
       	return $query;
    }
}