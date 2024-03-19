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
			FROM PRUEBA_beneficiosCM.usuarios us
			LEFT JOIN PRUEBA_beneficiosCM.areasbeneficios ab ON ab.idAreaBeneficio = us.idAreaBeneficio
			LEFT JOIN PRUEBA_beneficiosCM.metasporespecialista mpe ON mpe.idEspecialista = us.idUsuario
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

			$query = "SELECT * FROM PRUEBA_beneficiosCM.citas
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

			$query = "SELECT CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre, us.idUsuario
    		FROM PRUEBA_beneficiosCM.usuarios us
    		INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
    		WHERE
    			(us.idAreaBeneficio=$idAreaBeneficio
			OR us2.idpuesto = $idAreaBeneficio)
    		AND us.idRol = 3";

    	return $this->ch->query($query)->result();
    }

	public function checkModalitie($idEspecialista, $presencialDate){
		/* $query = $this->db->query("SELECT idSede from presencialXSede where idEspecialista = ? AND presencialDate = ? ", array($idEspecialista, $presencialDate)); */
		
		$query = $this->ch->query("SELECT idSede from PRUEBA_beneficiosCM.presencialxsede where idEspecialista = ? AND presencialDate = ? ", array($idEspecialista, $presencialDate));

		return $query;
	}

}