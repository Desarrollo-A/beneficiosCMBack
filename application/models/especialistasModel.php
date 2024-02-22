<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class EspecialistasModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function especialistas(){
		$query = $this->db-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1");
		return $query;
	}

	public function getMeta($idEspecialista){
    	$query = "SELECT
			CASE
				WHEN mpe.metaCitas IS NULL THEN ab.metaCitas ELSE mpe.metaCitas END AS meta
			FROM usuarios us
			LEFT JOIN areasBeneficios ab ON ab.idAreaBeneficio = us.idAreaBeneficio
			LEFT JOIN metasPorEspecialista mpe ON mpe.idEspecialista = us.idUsuario
			WHERE 
				us.idUsuario = $idEspecialista";

        return $this->db->query($query)->row();
    }

    public function getTotal($idEspecialista, $fechaInicio, $fechaFin)
    {
        $query = "SELECT * FROM citas
        	WHERE
        		idEspecialista = $idEspecialista
        	AND estatusCita = 4
			AND fechaFinal BETWEEN '$fechaInicio' AND '$fechaFin'";

        return $this->db->query($query)->num_rows();
    }

    public function getEspecialistasPorArea($idAreaBeneficio){
    	$query = "SELECT *
    		FROM usuarios
    		WHERE
    			(idAreaBeneficio=$idAreaBeneficio
			OR idPuesto = $idAreaBeneficio)
    		AND idRol = 3";

    	return $this->db->query($query)->result();
    }

	

}