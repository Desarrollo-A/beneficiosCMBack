<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class citasModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function citas()
	{
		$query = $this->db-> query("SELECT ct.idCita, ct.idEspecialista , ct.idPaciente, ct.idPaciente, ct.estatus as area, ct.fechaInicio as fechaInicio, ct.fechaFinal as fechaFinal,
		o.nombre as estatus
		FROM citas ct
		INNER JOIN opcionesPorCatalogo o ON o.idOpcion = ct.estatus");
		return $query->result();
	}

}