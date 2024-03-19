<?php
defined('BASEPATH') or exit('No direct script access allowed');

class avisosPrivacidadModel extends CI_Model
{

	function getEspecialidades()
	{
		/* $query = $this->db->query("SELECT *, 
		'idPuesto' =  CASE
		WHEN idOpcion = 1 THEN 585
		WHEN idOpcion = 2 THEN 537
		WHEN idOpcion = 3 THEN 686
		WHEN idOpcion = 4 THEN 158
		WHEN idOpcion = 585 THEN 585
		WHEN idOpcion = 537 THEN 537
		WHEN idOpcion = 686 THEN 686
		WHEN idOpcion = 158 THEN 158
		END
		from opcionesPorCatalogo where idCatalogo = 1"); */

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
		from PRUEBA_beneficiosCM.opcionesporcatalogo where idCatalogo = 1"); 
		return $query->result_array();
	}

	function getAvisoPrivacidadByEsp($idEspecialidad)
	{
		/* $query = $this->db->query("SELECT hd.*, opc.nombre as nombreDocumento, opc2.nombre as nombreEspecialidad
 		FROM historialDocumento hd
		LEFT JOIN opcionesPorCatalogo opc ON opc.idOpcion = hd.tipoDocumento AND opc.idCatalogo = 11
		LEFT JOIN opcionesPorCatalogo opc2 ON opc2.idOpcion = hd.tipoEspecialidad AND opc2.idCatalogo = 1
 		WHERE hd.status=1 AND
 		hd.tipoDocumento = 1 AND hd.tipoEspecialidad = $idEspecialidad"); */

		 $query = $this->ch->query("SELECT hd.*, opc.nombre as nombreDocumento, opc2.nombre as nombreEspecialidad
		 FROM PRUEBA_beneficiosCM.historialdocumento hd
		LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo opc ON opc.idOpcion = hd.tipoDocumento AND opc.idCatalogo = 11
		LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo opc2 ON opc2.idOpcion = hd.tipoEspecialidad AND opc2.idCatalogo = 1
		 WHERE hd.estatus=1 AND
		 hd.tipoDocumento = 1 AND hd.tipoEspecialidad = $idEspecialidad");
		return $query->result_array();
	}

	function revisaRamaActiva($idExpediente)
	{
		/* $query = $this->db->query("SELECT * FROM historialDocumento WHERE status=1 AND tipoDocumento = 1 AND idDocumento=$idExpediente;"); */
		
		$query = $this->ch->query("SELECT * FROM PRUEBA_beneficiosCM.historialdocumento WHERE estatus=1 AND tipoDocumento = 1 AND idDocumento=$idExpediente;");
		return $query->result_array();
	}
}
