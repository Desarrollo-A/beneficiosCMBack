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

			$query = $this->db-> query("SELECT ct.idCita, ct.idEspecialista , ct.idPaciente, ct.idPaciente, ct.estatus as area, ct.fechaInicio as fechaInicio, ct.fechaFinal as fechaFinal,
			o.nombre as estatus
			FROM citas ct
			INNER JOIN opcionesPorCatalogo o ON o.idOpcion = ct.estatus");
			return $query->result();

		}else{

			$query = $this->db-> query("SELECT ct.idCita, ct.idEspecialista , ct.idPaciente, ct.idPaciente, ct.estatus as area, ct.fechaInicio as fechaInicio, ct.fechaFinal as fechaFinal,
			o.nombre as estatus
			FROM citas ct
			INNER JOIN opcionesPorCatalogo o ON o.idOpcion = ct.estatus WHERE ct.estatus = 7");
			return $query->result();

		}
	}

}