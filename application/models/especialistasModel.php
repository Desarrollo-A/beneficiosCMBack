<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class EspecialistasModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

    public function especialistas(){
		/* $query = $this->db-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1"); */

		$query = $this->ch-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1");
		return $query;
	}

	public function getMeta($idEspecialista){
/*     	$query = "SELECT
			CASE
				WHEN mpe.metaCitas IS NULL THEN ab.metaCitas ELSE mpe.metaCitas END AS meta
			FROM usuarios us
			LEFT JOIN areasBeneficios ab ON ab.idAreaBeneficio = us.idAreaBeneficio
			LEFT JOIN metasPorEspecialista mpe ON mpe.idEspecialista = us.idUsuario
			WHERE us.idUsuario = $idEspecialista"; */

			$query = "SELECT
			CASE
				WHEN mpe.metaCitas IS NULL THEN ab.metaCitas ELSE mpe.metaCitas END AS meta
			FROM ". $this->schema_cm .".usuarios us
			LEFT JOIN ". $this->schema_cm .".areasbeneficios ab ON ab.idAreaBeneficio = us.idAreaBeneficio
			LEFT JOIN ". $this->schema_cm .".metasporespecialista mpe ON mpe.idEspecialista = us.idUsuario
			WHERE us.idUsuario = $idEspecialista";

        return $this->ch->query($query)->row();
    }

    public function getTotal($idEspecialista, $mes)
    {
        /* $query = "SELECT * FROM citas
        	WHERE
        		idEspecialista = $idEspecialista
        	AND estatusCita = 4
			AND MONTH(fechaFinal) = $mes"; */
			$query = "SELECT * FROM ". $this->schema_cm .".citas
        	WHERE
        		idEspecialista = $idEspecialista
        	AND estatusCita = 4
			AND MONTH(fechaFinal) = $mes";

        return $this->ch->query($query)->num_rows();
    }

    public function getEspecialistasPorArea($idAreaBeneficio){
    	/* $query = "SELECT *
    		FROM usuarios
    		WHERE
    			(idAreaBeneficio=$idAreaBeneficio
			OR idPuesto = $idAreaBeneficio)
    		AND idRol = 3"; */

			$query = "SELECT CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre, us.idUsuario
    		FROM ". $this->schema_cm .".usuarios us
    		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
    		WHERE
    			(us.idAreaBeneficio=$idAreaBeneficio
			OR us2.idpuesto = $idAreaBeneficio)
    		AND us.idRol = 3";

    	return $this->ch->query($query)->result();
    }

	public function checkModalitie($idEspecialista, $presencialDate){
		/* $query = $this->db->query("SELECT idSede from presencialXSede where idEspecialista = ? AND presencialDate = ? ", array($idEspecialista, $presencialDate)); */
		$query = $this->ch->query("SELECT idSede from ". $this->schema_cm .".presencialxsede where idEspecialista = ? AND presencialDate = ? ", array($idEspecialista, $presencialDate));
		return $query;
	}

}