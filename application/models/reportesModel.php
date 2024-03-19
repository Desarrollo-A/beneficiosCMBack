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

		if($dt == '0'){

			/* $query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ofi.oficina, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaInicio, dep.depto, op2.nombre AS modalidad,
			ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita, 
			'color' = CASE
	            WHEN ct.estatusCita = 0 THEN '#ff0000'
	            WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	            WHEN ct.estatusCita = 2 THEN '#ff0000'
	            WHEN ct.estatusCita = 3 THEN '#808080'
	            WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
	        END,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			LEFT JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN puestos ps2 ON ps2.idPuesto = pa.idPuesto
			LEFT JOIN areas ar ON ar.idArea = ps2.idArea
			LEFT JOIN departamentos dep ON dep.idDepto = ar.idDepto
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
			WHERE op.idCatalogo = 2 AND ct.estatusCita < 6
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us.nombre, 
  				pa.nombre, 
  				ps.puesto, 
  				sd.sede, 
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us.sexo, 
  				ofi.oficina, 
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.depto,
				op2.nombre,
				axs.tipoCita
			"); */

			$query = $this->ch->query("SELECT ct.idCita, pa.idUsuario AS idColab, CONCAT (us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS especialista, 
			CONCAT (us3.nombre_persona, us3.pri_apellido, us3.sec_apellido) AS paciente, ps.nom_puesto AS area, sd.nsede AS sede,ct.titulo, op.nombre AS estatus, 
			CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario, observaciones, us2.sexo, 
			ofi.noficina AS oficina, ct.estatusCita, ct.fechaInicio, dep.ndepto AS depto, op2.nombre AS modalidad,
			IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita, IFNULL(GROUP_CONCAT(oxc.nombre SEPARATOR ', '), 'Pendiente de pago') AS metodoPago,
			CASE 
		        WHEN ct.estatusCita = 0 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
		        WHEN ct.estatusCita = 2 THEN '#ff0000'
		        WHEN ct.estatusCita = 3 THEN '#808080'
		        WHEN ct.estatusCita = 4 THEN '#008000'
		        WHEN ct.estatusCita = 5 THEN '#ff4d67'
		        WHEN ct.estatusCita = 6 THEN '#00ffff'
		        WHEN ct.estatusCita = 7 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
    		END AS color,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM PRUEBA_beneficiosCM.citas ct
			LEFT JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN PRUEBA_beneficiosCM.usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us3 ON us3.idcontrato = pa.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas ofi ON ofi.idoficina = axs.idOficina
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps2 ON ps2.idpuesto = us3.idpuesto
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_area ar ON ar.idsubarea = ps2.idArea
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_departamento dep ON dep.idDepto = ar.idDepto
			LEFT JOIN PRUEBA_beneficiosCM.catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idpuesto = 537 THEN 8
			WHEN ps.idpuesto = 585 THEN 7
			WHEN ps.idpuesto = 686 THEN 9 
			WHEN ps.idpuesto = 158 THEN 6
			ELSE ps.idpuesto END 
			LEFT JOIN PRUEBA_beneficiosCM.detallepagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN PRUEBA_beneficiosCM.motivosporcita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
			WHERE op.idCatalogo = 2 AND ct.estatusCita < 8
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us2.nombre_persona, 
  				us2.pri_apellido,    
  				us2.sec_apellido,
  				us3.nombre_persona,  
  				us3.pri_apellido, 
  				us3.sec_apellido,
  				ps.nom_puesto,
  				sd.nsede,
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us2.sexo, 
  				ofi.noficina,
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.ndepto,
				op2.nombre,
				axs.tipoCita
			");

			return $query;

		}else if($dt !== '0' || $dt !== '2'){

			/* $query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ofi.oficina, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaInicio, dep.depto, op2.nombre AS modalidad,
			ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita,
			'color' = CASE
	            WHEN ct.estatusCita = 0 THEN '#ff0000'
	            WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	            WHEN ct.estatusCita = 2 THEN '#ff0000'
	            WHEN ct.estatusCita = 3 THEN '#808080'
	            WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
	        END,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			LEFT JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN puestos ps2 ON ps2.idPuesto = pa.idPuesto
			LEFT JOIN areas ar ON ar.idArea = ps2.idArea
			LEFT JOIN departamentos dep ON dep.idDepto = ar.idDepto
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
			WHERE op.idCatalogo = 2 AND ct.estatusCita = $dt
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us.nombre, 
  				pa.nombre, 
  				ps.puesto, 
  				sd.sede, 
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us.sexo, 
  				ofi.oficina, 
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.depto,
				op2.nombre,
				axs.tipoCita
			"); */

			$query = $this->ch->query("SELECT ct.idCita, pa.idUsuario AS idColab, CONCAT (us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS especialista, 
			CONCAT (us3.nombre_persona, us3.pri_apellido, us3.sec_apellido) AS paciente, ps.nom_puesto AS area, sd.nsede AS sede,ct.titulo, op.nombre AS estatus, 
			CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario, observaciones, us2.sexo, 
			ofi.noficina AS oficina, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaInicio, dep.ndepto AS depto, op2.nombre AS modalidad,
			IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita,
			CASE 
		        WHEN ct.estatusCita = 0 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
		        WHEN ct.estatusCita = 2 THEN '#ff0000'
		        WHEN ct.estatusCita = 3 THEN '#808080'
		        WHEN ct.estatusCita = 4 THEN '#008000'
		        WHEN ct.estatusCita = 5 THEN '#ff4d67'
		        WHEN ct.estatusCita = 6 THEN '#00ffff'
		        WHEN ct.estatusCita = 7 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
    		END AS color,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM PRUEBA_beneficiosCM.citas ct
			LEFT JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN PRUEBA_beneficiosCM.usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us3 ON us3.idcontrato = pa.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas ofi ON ofi.idoficina = axs.idOficina
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps2 ON ps2.idpuesto = us3.idpuesto
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_area ar ON ar.idsubarea = ps2.idArea
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_departamento dep ON dep.idDepto = ar.idDepto
			LEFT JOIN PRUEBA_beneficiosCM.catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idpuesto = 537 THEN 8
			WHEN ps.idpuesto = 585 THEN 7
			WHEN ps.idpuesto = 686 THEN 9 
			WHEN ps.idpuesto = 158 THEN 6
			ELSE ps.idpuesto END 
			LEFT JOIN PRUEBA_beneficiosCM.detallepagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN PRUEBA_beneficiosCM.motivosporcita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo
			WHERE op.idCatalogo = 2 AND ct.estatusCita = $dt
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us2.nombre_persona, 
  				us2.pri_apellido,    
  				us2.sec_apellido,
  				us3.nombre_persona,  
  				us3.pri_apellido, 
  				us3.sec_apellido,
  				ps.nom_puesto,
  				sd.nsede,
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us2.sexo, 
  				ofi.noficina,
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.ndepto,
				op2.nombre,
				axs.tipoCita
			");
			return $query;
		}else if($dt == '2'){
			
			/* $query = $this->db-> query("SELECT ct.idCita, pa.idUsuario AS idColab, us.nombre especialista, pa.nombre paciente, ps.puesto AS area, sd.sede,ct.titulo, op.nombre AS estatus, 
			CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, observaciones, us.sexo, 
			ofi.oficina, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaInicio, dep.depto, op2.nombre AS modalidad,
			ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita,
			'color' = CASE
	            WHEN ct.estatusCita = 0 THEN '#ff0000'
	            WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	            WHEN ct.estatusCita = 2 THEN '#ff0000'
	            WHEN ct.estatusCita = 3 THEN '#808080'
	            WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
	        END,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM citas ct
			LEFT JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN sedes sd ON sd.idSede = axs.idSede
			LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
			LEFT JOIN puestos ps2 ON ps2.idPuesto = pa.idPuesto
			LEFT JOIN areas ar ON ar.idArea = ps2.idArea
			LEFT JOIN departamentos dep ON dep.idDepto = ar.idDepto
			LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idPuesto = 537 THEN 8
			WHEN ps.idPuesto = 585 THEN 7
			WHEN ps.idPuesto = 686 THEN 9
			WHEN ps.idPuesto = 158 THEN 6
			ELSE ps.idPuesto END 
			LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
			WHERE op.idCatalogo = 2 AND ct.estatusCita = 2 AND ct.estatusCita = 7
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us.nombre, 
  				pa.nombre, 
  				ps.puesto, 
  				sd.sede, 
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us.sexo, 
  				ofi.oficina, 
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.depto,
				op2.nombre,
				axs.tipoCita
			"); */

			$query = $this->ch->query("SELECT ct.idCita, pa.idUsuario AS idColab, CONCAT (us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS especialista, 
			CONCAT (us3.nombre_persona, us3.pri_apellido, us3.sec_apellido) AS paciente, ps.nom_puesto AS area, sd.nsede AS sede,ct.titulo, op.nombre AS estatus, 
			CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario, observaciones, us2.sexo, 
			ofi.noficina AS oficina, oxc.nombre AS metodoPago, ct.estatusCita, ct.fechaInicio, dep.ndepto AS depto, op2.nombre AS modalidad,
			IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita,
			CASE 
		        WHEN ct.estatusCita = 0 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
		        WHEN ct.estatusCita = 2 THEN '#ff0000'
		        WHEN ct.estatusCita = 3 THEN '#808080'
		        WHEN ct.estatusCita = 4 THEN '#008000'
		        WHEN ct.estatusCita = 5 THEN '#ff4d67'
		        WHEN ct.estatusCita = 6 THEN '#00ffff'
		        WHEN ct.estatusCita = 7 THEN '#ff0000'
		        WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
    		END AS color,
			CASE 
			WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
			ELSE 'Exitoso'
			END AS pagoGenerado
			FROM PRUEBA_beneficiosCM.citas ct
			LEFT JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN PRUEBA_beneficiosCM.usuarios pa ON pa.idUsuario = ct.idPaciente
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_usuarios us3 ON us3.idcontrato = pa.idContrato
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idOpcion = ct.estatusCita
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas ofi ON ofi.idoficina = axs.idOficina
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_puestos ps2 ON ps2.idpuesto = us3.idpuesto
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_area ar ON ar.idsubarea = ps2.idArea
			LEFT JOIN PRUEBA_CH.beneficioscm_vista_departamento dep ON dep.idDepto = ar.idDepto
			LEFT JOIN PRUEBA_beneficiosCM.catalogos cat ON cat.idCatalogo = CASE 
			WHEN ps.idpuesto = 537 THEN 8
			WHEN ps.idpuesto = 585 THEN 7
			WHEN ps.idpuesto = 686 THEN 9 
			WHEN ps.idpuesto = 158 THEN 6
			ELSE ps.idpuesto END 
			LEFT JOIN PRUEBA_beneficiosCM.detallepagos dp ON dp.idDetalle = ct.idDetalle
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
			LEFT JOIN PRUEBA_beneficiosCM.motivosporcita mpc ON mpc.idCita = ct.idCita
  			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
			  WHERE op.idCatalogo = 2 AND (ct.estatusCita = 2 OR ct.estatusCita = 7)
			GROUP BY 
  				ct.idCita, 
  				pa.idUsuario, 
  				us2.nombre_persona, 
  				us2.pri_apellido,    
  				us2.sec_apellido,
  				us3.nombre_persona,  
  				us3.pri_apellido, 
  				us3.sec_apellido,
  				ps.nom_puesto,
  				sd.nsede,
  				ct.titulo, 
  				op.nombre, 
  				ct.fechaInicio, 
  				ct.fechaFinal, 
  				observaciones, 
  				us2.sexo, 
  				ofi.noficina,
  				oxc.nombre, 
  				ct.estatusCita, 
  				ct.fechaModificacion,
				dep.ndepto,
				op2.nombre,
				axs.tipoCita
			");
			return $query;
		}
	}

	public function getPacientes($dt){

	$area = $dt["esp"];
	$idRol = $dt["idRol"];
	$idUs = $dt["idUs"];

	if($idRol == 1 || $idRol == 4){
		
		switch($area){
			case 537:
				/* $query = $this->db-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                us.nombre, 
                dep.depto, 
                sd.sede, 
                pu.puesto,
                us.correo, 
                sd.sede,
				op.nombre AS estNut
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dep ON dep.idDepto = ar.idDepto
				INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				INNER JOIN usuarios us2 ON us2.idRol = 3 AND us2.idPuesto = 537
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us2.idUsuario
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusNut
				WHERE estatusNut IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre, 
                us2.ndepto AS depto,
                us2.nsede AS sede,
                us2.npuesto AS puesto,
                us2.mail_emp AS correo,
				op.nombre AS estNut
				FROM PRUEBA_beneficiosCM.detallepaciente dp 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN PRUEBA_beneficiosCM.catalogos ct ON ct.idCatalogo = 13
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us4 ON us4.idpuesto = 537
				INNER JOIN PRUEBA_beneficiosCM.usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
				INNER JOIN PRUEBA_beneficiosCM.citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario
				LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusNut
				WHERE estatusNut IS NOT null AND ci.estatusCita = 4");
				
				break;
			case 585:
				/* $query = $this->db-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                us.nombre, 
                dep.depto, 
                sd.sede, 
                pu.puesto,
                us.correo, 
                sd.sede, 
				op.nombre AS estPsi
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dep ON dep.idDepto = ar.idDepto
				INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				INNER JOIN usuarios us2 ON us2.idRol = 3 AND us2.idPuesto = 585
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us2.idUsuario
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusPsi
				WHERE estatusPsi IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre, 
                us2.ndepto AS depto,
                us2.nsede AS sede,
                us2.npuesto AS puesto,
                us2.mail_emp AS correo,
				op.nombre AS estPsi
				FROM PRUEBA_beneficiosCM.detallepaciente dp 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN PRUEBA_beneficiosCM.catalogos ct ON ct.idCatalogo = 13
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us4 ON us4.idpuesto = 585
				INNER JOIN PRUEBA_beneficiosCM.usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
				INNER JOIN PRUEBA_beneficiosCM.citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario
				LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusPsi
				WHERE estatusPsi IS NOT null AND ci.estatusCita = 4");

				break;
			case 158:
				/* $query = $this->db-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                us.nombre, 
                dep.depto, 
                sd.sede, 
                pu.puesto,
                us.correo, 
                sd.sede, 
                op.nombre AS estQB
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dep ON dep.idDepto = ar.idDepto
				INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				INNER JOIN usuarios us2 ON us2.idRol = 3 AND us2.idPuesto = 158
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us2.idUsuario
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusQB
				WHERE estatusQB IS NOT NULL AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre, 
                us2.ndepto AS depto,
                us2.nsede AS sede,
                us2.npuesto AS puesto,
                us2.mail_emp AS correo,
				op.nombre AS estQB
				FROM PRUEBA_beneficiosCM.detallepaciente dp 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN PRUEBA_beneficiosCM.catalogos ct ON ct.idCatalogo = 13
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us4 ON us4.idpuesto = 158
				INNER JOIN PRUEBA_beneficiosCM.usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
				INNER JOIN PRUEBA_beneficiosCM.citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario
				LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusQB
				WHERE estatusQB IS NOT null AND ci.estatusCita = 4");
				break;
			case 686:
				/* $query = $this->db-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                us.nombre, 
                dep.depto, 
                sd.sede, 
                pu.puesto,
                us.correo, 
                sd.sede,
				op.nombre AS estGE
				FROM detallePaciente dp 
				INNER JOIN usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dep ON dep.idDepto = ar.idDepto
				INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN catalogos ct ON ct.idCatalogo = 13
				INNER JOIN usuarios us2 ON us2.idRol = 3 AND us2.idPuesto = 686
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us2.idUsuario
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusGE
				WHERE estatusGE IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, 
                us.idUsuario, 
                CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre, 
                us2.ndepto AS depto,
                us2.nsede AS sede,
                us2.npuesto AS puesto,
                us2.mail_emp AS correo,
				op.nombre AS estGE
				FROM PRUEBA_beneficiosCM.detallepaciente dp 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = dp.idUsuario
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN PRUEBA_beneficiosCM.catalogos ct ON ct.idCatalogo = 13
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us4 ON us4.idpuesto = 686
				INNER JOIN PRUEBA_beneficiosCM.usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
				INNER JOIN PRUEBA_beneficiosCM.citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario
				LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND  op.idOpcion = dp.estatusGE
				WHERE estatusGE IS NOT null AND ci.estatusCita = 4");
				break;
		}
	}else if($idRol == 3){

		switch($area){
			case 537:
				/* $query = $this->db-> query("SELECT DISTINCT us.idUsuario, us.nombre, dp.depto, sd.sede, ps.puesto, op.nombre AS estNut FROM citas ct 
				INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dp ON  dp.idDepto = ar.idDepto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				INNER JOIN detallePaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusNut
				WHERE ct.idEspecialista = $idUs AND estatusNut IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,   
				us2.ndepto AS depto, us2.nsede AS sede, us2.npuesto AS puesto, op.nombre AS estNut 
				FROM PRUEBA_beneficiosCM.citas ct 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN detallepaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusNut
				WHERE ct.idEspecialista = $idUs AND estatusNut IS NOT null AND ci.estatusCita = 4");
				break;
			case 585:
				/* $query = $this->db-> query("SELECT DISTINCT us.idUsuario, us.nombre, dp.depto, sd.sede, ps.puesto, op.nombre AS estPsi FROM citas ct 
				INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dp ON  dp.idDepto = ar.idDepto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				INNER JOIN detallePaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusPsi
				WHERE ct.idEspecialista = $idUs AND estatusPsi IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,   
				us2.ndepto AS depto, us2.nsede AS sede, us2.npuesto AS puesto, op.nombre AS estPsi
				FROM PRUEBA_beneficiosCM.citas ct 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN detallepaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusPsi
				WHERE ct.idEspecialista = $idUs AND estatusPsi IS NOT null AND ci.estatusCita = 4");
				break;
			case 158:
				/* $query = $this->db-> query("SELECT DISTINCT us.idUsuario, us.nombre, dp.depto, sd.sede, ps.puesto, op.nombre AS estQB FROM citas ct 
				INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dp ON  dp.idDepto = ar.idDepto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				INNER JOIN detallePaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusQB
				WHERE ct.idEspecialista = $idUs AND estatusQB IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,   
				us2.ndepto AS depto, us2.nsede AS sede, us2.npuesto AS puesto, op.nombre AS estQB
				FROM PRUEBA_beneficiosCM.citas ct 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN detallepaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusQB
				WHERE ct.idEspecialista = $idUs AND estatusQB IS NOT null AND ci.estatusCita = 4");
				break;
			case 686:
				/* $query = $this->db-> query("SELECT DISTINCT us.idUsuario, us.nombre, dp.depto, sd.sede, ps.puesto, op.nombre AS estGE FROM citas ct 
				INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN areas ar ON ar.idArea = us.idArea
				INNER JOIN departamentos dp ON  dp.idDepto = ar.idDepto
				INNER JOIN sedes sd ON sd.idSede = us.idSede
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				INNER JOIN detallePaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesPorCatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusGE
				WHERE ct.idEspecialista = $idUs AND estatusGE IS NOT null AND ci.estatusCita = 4"); */

				$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,   
				us2.ndepto AS depto, us2.nsede AS sede, us2.npuesto AS puesto, op.nombre AS estQB
				FROM PRUEBA_beneficiosCM.citas ct 
				INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
				INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				INNER JOIN detallepaciente dtp ON dtp.idUsuario = us.idUsuario
				INNER JOIN catalogos cat ON cat.idCatalogo = 13
				INNER JOIN citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
				LEFT JOIN opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusGE
				WHERE ct.idEspecialista = $idUs AND estatusGE IS NOT null AND ci.estatusCita = 4");
				break;
		}

	}
		
		return $query;
	}

	/* public function getResumenTerapias($dt){

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
	} */

	/* public function getEspeQua(){

		$query = $this->db-> query("SELECT * FROM usuarios WHERE idRol = 3 AND idPuesto = 158");
		return $query;

	} */

	public function getCierrePacientes($dt){
		
		$idUsr = $dt["idUsr"];
		$idEsp = isset($dt["idEsp"][0]) ? $dt["idEsp"][0] : '0';
		$rol = $dt["roles"];
		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';
		$fechaI = $dt["fhI"];
		$fechaFn = $dt["fhF"];
		$mod = isset($dt["modalidad"][0]) ? $dt["modalidad"][0] : '0';

		$fecha = new DateTime($fechaFn);
		$fecha->modify('+1 day');
		$fechaF = $fecha->format('Y-m-d');

		if($area == "0" && $rol == 4 && $mod == "0"){

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas
			WHERE fechaModificacion >= '$fechaI' AND fechaModificacion < '$fechaF'"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas
			WHERE fechaModificacion >= '$fechaI' AND fechaModificacion < '$fechaF' AND estatusCita = 4");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND ps.puesto IN ('$ar') AND ct.estatusCita = 4"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND us2.npuesto IN ('$ar') AND ct.estatusCita = 4");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);
			$nombres = implode("','", $dt["idEsp"]);

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND ps.puesto IN ('$ar') AND ct.estatusCita = 4 AND us.nombre IN ('$nombres')"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND us2.npuesto IN ('$ar') AND ct.estatusCita = 4 
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres')");
			return $query;

		}else if($area == "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad')
			AND ct.estatusCita = 4"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad')
			AND ct.estatusCita = 4");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad') AND ps.puesto IN ('$ar')
			AND ct.estatusCita = 4"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad') AND us2.npuesto IN ('$ar')
			AND ct.estatusCita = 4");
			return $query;	

		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$nombres = implode("','", $dt["idEsp"]);

			/* $query = $this->db->query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' 
			AND ct.fechaModificacion < '$fechaF') AND us.nombre IN ('$nombres')
			AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad')"); */

			$query = $this->ch->query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' 
			AND ct.fechaModificacion < '$fechaF') AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres')
			AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad')");
			return $query;

		}else if($rol == 3 && $mod == "0"){

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4
			");
			return $query;
		
		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			/* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM citas ct
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad')"); */

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM PRUEBA_beneficiosCM.citas ct
			INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN PRUEBA_beneficiosCM.atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad')");
			return $query;
		
		}

	}

	public function getCierreIngresos($dt){
		
		$idUsr = $dt["idUsr"];
		$idEsp = isset($dt["idEsp"][0]) ? $dt["idEsp"][0] : '0';
		$rol = $dt["roles"];
		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';
		$fechaI = $dt["fhI"];
		$fechaFn = $dt["fhF"];
		$reporte = $dt["reporte"];

		$mod = isset($dt["modalidad"][0]) ? $dt["modalidad"][0] : '0';

		$fecha = new DateTime($fechaFn);
		$fecha->modify('+1 day');
		$fechaF = $fecha->format('Y-m-d');

	if($reporte == 0){

		if($area == "0" && $mod == "0" && $rol == 4){

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);
	
			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar') AND us.nombre IN ('$nombres')
			) AS sub");
			return $query;
		
		}else if($area == "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad') AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;	
		
		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.nombre IN ('$nombres') AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($rol == 3 && $mod == "0"){

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
				SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
				FROM (
					SELECT DISTINCT ct.idDetalle, dp.cantidad
					FROM citas ct
					INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
					INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
					INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
					WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
					AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
				SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
				AND us.idUsuario = $idUsr AND op2.nombre IN ('$modalidad')
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}

	}else if($reporte !== 0 && $reporte !== 2)
	{

		if($area == "0" && $mod == "0" && $rol == 4){

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			WHERE ct.estatusCita = $reporte AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.estatusCita = $reporte AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);
	
			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.estatusCita = $reporte AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar') AND us.nombre IN ('$nombres')
			) AS sub");
			return $query;
		
		}else if($area == "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;
		
		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte
			AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.nombre IN ('$nombres') AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad') AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;	

		}else if($rol == 3 && $mod == "0"){

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				WHERE ct.estatusCita = $reporte
				AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
				AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE ct.estatusCita = $reporte
				AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
				AND us.idUsuario = $idUsr AND op2.nombre IN ('$modalidad')
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}
		
	}else if($reporte == 2)
	{

		if($area == "0" && $mod == "0" && $rol == 4){

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7 AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7 AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;
		
		}else if($area == "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7 AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7 AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND ps.puesto IN ('$ar') AND us.nombre IN ('$nombres')
			) AS sub");
			return $query;
		
		}else if($area != "0" && $rol == 4 && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7
			AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.nombre IN ('$nombres') AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->db-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM citas ct
			INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN puestos ps On ps.idPuesto = us.idPuesto
			LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = 2 AND ct.estatusCita = 7 
			AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND op2.nombre IN ('$modalidad') AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;	

		}else if($rol == 3 && $mod == "0"){

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				WHERE ct.estatusCita = 2 AND ct.estatusCita = 7
				AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
				AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->db-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
				INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
				LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE ct.estatusCita = 2 AND ct.estatusCita = 7
				AND (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
				AND us.idUsuario = $idUsr AND op2.nombre IN ('$modalidad')
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}
	}
		
	}

	public function getSelectEspe($dt){

		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';

		if($area == "0"){

		/* $query = $this->db-> query("SELECT * FROM usuarios us
		INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
		WHERE us.idRol =  3"); */

		$query = $this->ch-> query("SELECT * FROM PRUEBA_CH.beneficioscm_vista_usuarios AS us
		INNER JOIN PRUEBA_beneficiosCM.usuarios AS us2 ON us2.idContrato = us.idcontrato 
		WHERE us2.idRol =  3");
		return $query;

		}else{

			$area1 = isset($dt["esp"][0]) ? $dt["esp"][0] : '';
			$area2 = isset($dt["esp"][1]) ? $dt["esp"][1] : '';
			$area3 = isset($dt["esp"][2]) ? $dt["esp"][2] : '';
			$area4 = isset($dt["esp"][3]) ? $dt["esp"][3] : '';

			/* $query = $this->db-> query("SELECT * FROM usuarios us
			INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
			WHERE us.idRol =  3 AND ps.puesto IN ('$area1', '$area2', '$area3', '$area4')"); */
			
			$query = $this->ch-> query("SELECT CONCAT(us.nombre_persona,' ', us.pri_apellido,' ', us.sec_apellido) AS nombre, us2.idUsuario 
			FROM PRUEBA_CH.beneficioscm_vista_usuarios AS us
			INNER JOIN PRUEBA_beneficiosCM.usuarios AS us2 ON us2.idContrato = us.idcontrato 
			WHERE us2.idRol =  3 AND us.npuesto IN ('$area1', '$area2', '$area3', '$area4')");
			return $query;

		}

	}

	public function getEspeUser($dt){

		/* $query = $this->db-> query("SELECT ps.idPuesto, ps.puesto
		FROM usuarios us
		INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
		WHERE us.idUsuario = $dt;"); */

		$query = $this->ch-> query("SELECT us.idpuesto AS idPuesto, us.npuesto AS puesto
		FROM PRUEBA_CH.beneficioscm_vista_usuarios AS us
		INNER JOIN PRUEBA_beneficiosCM.usuarios AS us2 ON us2.idContrato = us.idcontrato 
		WHERE us2.idUsuario = $dt;");
		return $query;

	}

}