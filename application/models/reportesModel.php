<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class ReportesModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function citas($dt)
	{
		if($dt === 'general'){

			$query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ct.motivoCita, ofi.oficina, ops.nombre AS motivoCita, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaModificacion,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo =  cat.idCatalogo AND ops.idOpcion = ct.motivoCita
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			WHERE op.idCatalogo = 2");
			return $query;

		}else if($dt === 'faltas'){

			$query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ct.motivoCita, ofi.oficina, ops.nombre AS motivoCita, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaModificacion,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo =  cat.idCatalogo AND ops.idOpcion = ct.motivoCita
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			WHERE op.idCatalogo = 2 AND ct.estatusCita = 3");
			return $query;

		}else if($dt === 'justificadas'){

			$query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ct.motivoCita, ofi.oficina, ops.nombre AS motivoCita, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaModificacion,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo =  cat.idCatalogo AND ops.idOpcion = ct.motivoCita
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
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

	public function getResumenTerapias($dt){

		if($dt === 'general'){

			$query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ct.motivoCita, ofi.oficina, ops.nombre AS motivoCita, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaModificacion,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo =  cat.idCatalogo AND ops.idOpcion = ct.motivoCita
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			WHERE op.idCatalogo = 2 AND us.idPuesto =  158");
			return $query;
	
			}else if($dt === 'penalizacion'){
	
			$query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ct.motivoCita, ofi.oficina, ops.nombre AS motivoCita, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaModificacion,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			INNER JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo =  cat.idCatalogo AND ops.idOpcion = ct.motivoCita
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			WHERE op.idCatalogo = 2 AND us.idPuesto =  158 AND ct.estatusCita = 3");
			return $query;
	
			}
	}

	public function getEspeQua(){

		$query = $this->db-> query("SELECT * FROM usuarios WHERE idRol = 3 AND idPuesto = 158");
		return $query;

	}

	public function getCierrePacientes($dt){
		
		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';
		$fechaI = $dt["fhI"];
		$fechaF = $dt["fhF"];

		if($area == "0"){

			$query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas
			WHERE fechaModificacion BETWEEN '$fechaI' AND '$fechaF'");
			return $query;

		}else{

			$area1 = isset($dt["esp"][0]) ? $dt["esp"][0] : '';
			$area2 = isset($dt["esp"][1]) ? $dt["esp"][1] : '';
			$area3 = isset($dt["esp"][2]) ? $dt["esp"][2] : '';
			$area4 = isset($dt["esp"][3]) ? $dt["esp"][3] : '';

			$query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.fechaModificacion BETWEEN '$fechaI' AND '$fechaF' AND ps.puesto IN ('$area1', '$area2', '$area3', '$area4')");
			return $query;

		}

	}

	public function getCierreIngresos($dt){
		
		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';
		$fechaI = $dt["fhI"];
		$fechaF = $dt["fhF"];

		if($area == "0"){

			$query = $this->db-> query("SELECT SUM(dp.cantidad) AS TotalIngreso
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			WHERE ct.estatusCita = 4 AND ct.fechaModificacion BETWEEN '$fechaI' AND '$fechaF'");
			return $query;
		}else{

			$area1 = isset($dt["esp"][0]) ? $dt["esp"][0] : '';
			$area2 = isset($dt["esp"][1]) ? $dt["esp"][1] : '';
			$area3 = isset($dt["esp"][2]) ? $dt["esp"][2] : '';
			$area4 = isset($dt["esp"][3]) ? $dt["esp"][3] : '';

			$query = $this->db-> query("SELECT SUM(dp.cantidad) AS TotalIngreso
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.estatusCita = 4 AND ct.fechaModificacion BETWEEN '2024-02-01' AND '2024-02-12'
			AND ps.puesto IN ('$area1', '$area2', '$area3', '$area4')");
			return $query;

		}
	}

	public function getSelectEspe($dt){

		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';

		if($area == "0"){

		$query = $this->db-> query("SELECT * FROM usuarios us
		INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
		WHERE us.idRol =  3");
		return $query;

		}else{

			$area1 = isset($dt["esp"][0]) ? $dt["esp"][0] : '';
			$area2 = isset($dt["esp"][1]) ? $dt["esp"][1] : '';
			$area3 = isset($dt["esp"][2]) ? $dt["esp"][2] : '';
			$area4 = isset($dt["esp"][3]) ? $dt["esp"][3] : '';

			$query = $this->db-> query("SELECT * FROM usuarios us
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			WHERE us.idRol =  3 AND ps.puesto IN ('$area1', '$area2', '$area3', '$area4')");
			return $query;

		}

	}

}