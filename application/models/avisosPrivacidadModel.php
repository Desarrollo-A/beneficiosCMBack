<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class avisosPrivacidadModel extends CI_Model{

	function getEspecialidades(){
		$query = $this->db->query("SELECT * FROM opcionesPorCatalogo WHERE idCatalogo=1");
		return $query->result_array();
	}

	function getAvisoPrivacidadByEsp($idEspecialidad){
		$query = $this->db->query("SELECT hd.*, opc.nombre as nombreDocumento, opc2.nombre as nombreEspecialidad
 		FROM historialDocumento hd
		LEFT JOIN opcionesPorCatalogo opc ON opc.idOpcion = hd.tipoDocumento AND opc.idCatalogo = 11
		LEFT JOIN opcionesPorCatalogo opc2 ON opc2.idOpcion = hd.tipoEspecialidad AND opc2.idCatalogo = 1
 		WHERE hd.status=1 AND
 		hd.tipoDocumento = 1 AND hd.tipoEspecialidad = $idEspecialidad");
		return $query->result_array();
	}

	function revisaRamaActiva($idExpediente){
		$query = $this->db->query("SELECT * FROM historialDocumento WHERE status=1 AND tipoDocumento = 1 AND idDocumento=$idExpediente;");
		return $query->result_array();
	}
}
