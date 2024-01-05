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
		if($dt === 'general'){

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, pa.oficina, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.sede
			INNER JOIN puestos ps ON ps.idPuesto = us.puesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatus
			WHERE op.idCatalogo = 2");
			return $query;

		}else if($dt === 'faltas'){

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, pa.oficina, ps.puesto AS area, sd.sede,ct.titulo, op.nombre as estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.sede
			INNER JOIN puestos ps ON ps.idPuesto = us.puesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatus
			WHERE op.idCatalogo = 2 AND ct.estatus = 3");
			return $query;

		}else if($dt === 'justificadas'){

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, pa.oficina, ps.puesto AS area, sd.sede,ct.titulo, op.nombre as estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.sede
			INNER JOIN puestos ps ON ps.idPuesto = us.puesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatus
			WHERE op.idCatalogo = 2 AND ct.estatus = 5");
			return $query;

		}
	}

}