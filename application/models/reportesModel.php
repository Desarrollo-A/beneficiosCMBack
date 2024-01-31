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

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, ct.motivoCita 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.idSede
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			WHERE op.idCatalogo = 2");
			return $query;

		}else if($dt === 'faltas'){

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre as estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones , us.sexo, ct.motivoCita 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.idSede
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			WHERE op.idCatalogo = 2 AND ct.estatusCita = 3");
			return $query;

		}else if($dt === 'justificadas'){

			$query = $this->db-> query("SELECT ct.idCita, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre as estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, ct.motivoCita 
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN sedes sd ON sd.idSede = us.idSede
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			WHERE op.idCatalogo = 2 AND ct.estatusCita = 5");
			return $query;

		}
	}

	public function getPacientes($dt){
		
		switch($dt){
			case 537:
				$query = $this->db-> query("SELECT dp.idDetallePaciente AS id, us.idUsuario, us.nombre, us.correo, sd.sede,
				op.nombre AS estNut
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusNut
				WHERE estatusNut IS NOT null");
				break;
			case 585:
				$query = $this->db-> query("SELECT dp.idDetallePaciente AS id, us.idUsuario, us.nombre, us.correo, sd.sede,
				op.nombre AS estPsi
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusPsi
				WHERE estatusPsi IS NOT null");
				break;
			case 158:
				$query = $this->db-> query("SELECT dp.idDetallePaciente AS id, us.idUsuario, us.nombre, us.correo, sd.sede,
				op.nombre AS estQB
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusQB
				WHERE estatusQB IS NOT null");
				break;
			case 686:
				$query = $this->db-> query("SELECT dp.idDetallePaciente AS id, us.idUsuario, us.nombre, us.correo, sd.sede,
				op.nombre AS estGE
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusGE
				WHERE estatusGE IS NOT null");
				break;
		}
		
		return $query;
	}

}