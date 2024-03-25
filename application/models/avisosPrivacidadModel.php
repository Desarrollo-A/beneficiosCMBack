<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AvisosPrivacidadModel extends CI_Model
{
	public function __construct()
	{
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

	function getEspecialidades()
	{
		$query = $this->ch->query("SELECT *, 
		CASE
		WHEN idOpcion = 1 THEN 585
		WHEN idOpcion = 2 THEN 537
		WHEN idOpcion = 3 THEN 686
		WHEN idOpcion = 4 THEN 158
		WHEN idOpcion = 585 THEN 585
		WHEN idOpcion = 537 THEN 537
		WHEN idOpcion = 686 THEN 686
		WHEN idOpcion = 158 THEN 158
		END AS 'idPuesto'
		from ". $this->schema_cm .".opcionesporcatalogo where idCatalogo = 1"); 
		return $query->result_array();
	}

	function getAvisoPrivacidadByEsp($idEspecialidad)
	{
		$query = $this->ch->query("SELECT hd.*, opc.nombre as nombreDocumento, opc2.nombre as nombreEspecialidad
		 FROM ". $this->schema_cm .".historialdocumento hd
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo opc ON opc.idOpcion = hd.tipoDocumento AND opc.idCatalogo = 11
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo opc2 ON opc2.idOpcion = hd.tipoEspecialidad AND opc2.idCatalogo = 1
		 WHERE hd.estatus=1 AND
		 hd.tipoDocumento = 1 AND hd.tipoEspecialidad = $idEspecialidad");
		return $query->result_array();
	}

	function revisaRamaActiva($idExpediente)
	{
		$query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".historialdocumento WHERE estatus=1 AND tipoDocumento = 1 AND idDocumento=$idExpediente;");
		return $query->result_array();
	}
}
