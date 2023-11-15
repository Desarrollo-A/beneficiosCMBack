<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class reportesModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function citas($dt)
	{
		if($dt == 'Reporte General'){

			$query = $this->db-> query("SELECT  ct.idCita, ct.idEspecialista , ct.idPaciente, ct.idPaciente, ct.estatus as area, ct.fechaInicio as fechaInicio, ct.fechaFinal as fechaFinal,
			op.nombre as estatus, observaciones FROM catalogos ca 
			INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
			INNER JOIN citas ct ON ct.estatus = op.idOpcion ");
			return $query->result();

		}else{

			$query = $this->db-> query("SELECT  ct.idCita, ct.idEspecialista , ct.idPaciente, ct.idPaciente, ct.estatus as area, ct.fechaInicio as fechaInicio, ct.fechaFinal as fechaFinal,
			op.nombre as estatus, observaciones FROM catalogos ca 
				INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
				INNER JOIN citas ct ON ct.estatus = op.idOpcion 
				WHERE op.idOpcion = 3");
			return $query->result();

		}
	}

	public function observacion($idCita, $descripcion)
	{
			$query = $this->db-> query("UPDATE citas SET observaciones = '$descripcion', modificadoPor = 1 WHERE idCita = $idCita");
			
            return true;
	}

}