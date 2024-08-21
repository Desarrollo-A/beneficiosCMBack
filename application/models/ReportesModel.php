<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class ReportesModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

    public function citas($data)
	{

		$dt = $data["reporte"];
		$tipoUsuario = $data["tipoUsuario"];

		$tipoReporte = "";

		if($dt == '0'){
			$tipoReporte  = "AND ct.estatusCita < 9";
		}else if($dt == '2'){
			$tipoReporte = "AND ct.estatusCita IN (2, 7)";
		}else{
			$tipoReporte = "AND ct.estatusCita = $dt";
		}

		$usuarioCond = $tipoUsuario != 2 ? "AND pa.externo = $tipoUsuario" : "";

			$this->ch->query("SET @numPsi = 0;");
			$this->ch->query("SET @numSci = 0;");
			$this->ch->query("SET @numNut = 0;");
			$this->ch->query("SET @numEsp = 0;");
			$this->ch->query("SET @curRow = 0;");

			$query = $this->ch->query("SELECT CASE
				WHEN QUERY1.idPuesto = 585 THEN @numPsi := @numPsi + 1
				WHEN QUERY1.idPuesto = 537 THEN @numNut := @numNut + 1 
				WHEN QUERY1.idPuesto = 686 THEN @numEsp := @numEsp + 1
				WHEN QUERY1.idPuesto = 158 THEN @numSci := @numSci + 1
			END AS numCita, 
			QUERY1.* 
			FROM(
				SELECT ps.idpuesto, ops3.nombre AS nombreEstatusCita, ct.idCita, pa.idUsuario AS idColab, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, 
				us3.num_empleado AS numEmpleado, IFNULL (CONCAT (us3.nombre_persona,' ',us3.pri_apellido,' ',us3.sec_apellido), ext.nombre) AS paciente, 
				ps.nom_puesto AS area, IFNULL(sd.nsede, 'QRO') AS sede,ct.titulo, us3.narea, us3.npuesto, ct.archivoObservacion AS archivo,
				CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario, observaciones, IFNULL(us3.sexo, ext.sexo) AS sexo, 
				estatusCita, ct.fechaInicio, ct.fechaFinal, IFNULL(dep.ndepto, 'NO APLICA') AS depto, IFNULL(op2.nombre, 'Presencial') AS modalidad,  
				CASE 
					WHEN dp.cantidad IS NULL THEN 'SIN PAGO' ELSE CONCAT('$', ' ',dp.cantidad) 
				END AS monto, ops2.nombre AS tipoCita, IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'SIN MOTIVOS DE CITA') AS motivoCita, 
				IFNULL(oxc.nombre, 'Pendiente de pago') AS metodoPago, ct.fechaCreacion,
				CASE 
					WHEN $tipoUsuario = 1 THEN 'RIO DE LA LOZA'
					WHEN ofi.noficina IS NULL THEN 'VIRTUAL' 
					WHEN ofi.noficina IS NOT NULL THEN ofi.noficina 
				END AS oficina,
				CASE 
					WHEN pa.externo = 0 THEN 'Colaborador' 
					WHEN pa.externo = 1 THEN 'Externo' 
				END AS usuario,
				CASE 
					WHEN ct.estatusCita = 1 AND ct.tipoCita = 1 THEN '#ffe800'
					WHEN ct.estatusCita = 1 AND ct.tipoCita = 2 THEN '#0000ff' 
					WHEN ct.estatusCita = 1 AND ct.tipoCita = 3 THEN '#ffa500'
					WHEN ct.estatusCita = 2 THEN '#ff0000' 
					WHEN ct.estatusCita = 3 THEN '#808080' 
					WHEN ct.estatusCita = 4 THEN '#008000' 
					WHEN ct.estatusCita = 5 THEN '#ff4d67' 
					WHEN ct.estatusCita = 6 THEN '#00ffff' 
					WHEN ct.estatusCita = 7 THEN '#ff0000' 
					WHEN ct.estatusCita = 10 THEN '#33105D'
					WHEN ct.estatusCita = 11 THEN '#ff0000' 
				END AS color, 
				CASE
					WHEN ct.estatusCita = 1 AND ct.tipoCita = 1 THEN 'Por Asistir - Primera cita'
					WHEN ct.estatusCita = 1 AND ct.tipoCita = 2 THEN 'Por Asistir - En línea'  
					ELSE op.nombre
				END AS estatus, 
				CASE 
					WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado' 
					ELSE 'Exitoso' 
				END AS pagoGenerado,
				CASE 
					WHEN dp.fechaPago IS NULL THEN 'SIN FECHA DE PAGO' ELSE dp.fechaPago
				END AS fechaPago,
				us2.num_empleado AS numEspecialista
				FROM ". $this->schema_cm .".citas ct
				LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_cm .".usuarios pa ON pa.idUsuario = ct.idPaciente
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = pa.idContrato
				LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = pa.idContrato
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idOpcion = ct.estatusCita
				LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas ofi ON ofi.idoficina = axs.idOficina
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps2 ON ps2.idpuesto = us3.idpuesto
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = ps2.idArea
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dep ON dep.idDepto = ar.idDepto
				LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = CASE 
				WHEN ps.idpuesto = 537 THEN 8
				WHEN ps.idpuesto = 585 THEN 7
				WHEN ps.idpuesto = 686 THEN 9 
				WHEN ps.idpuesto = 158 THEN 6
				ELSE ps.idpuesto END 
				LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
				LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops2 ON ops2.idCatalogo = 10
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops3 ON ops3.idOpcion = ct.estatusCita AND ops3.idCatalogo = 2
				WHERE op.idCatalogo = 2 $usuarioCond $tipoReporte
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
					ORDER BY pa.idUsuario, ps.idPuesto, fechaCreacion) AS QUERY1
			");
			return $query;
	}

	public function getPacientes($dt){

		$area = $dt["esp"];
		$idRol = $dt["idRol"];
		$permisos = isset($dt["permisos"]) ? intval($dt["permisos"]) : 0;
		$idUs = $dt["idUs"];
		$tipoUsuario = $dt["tipoUsuario"];

		$usuarioCond = $tipoUsuario != 2 ? "AND us.externo = $tipoUsuario" : "";

		if($idRol == 1 || $idRol == 4 || $permisos == 5){
			
			switch($area){
				case 537:
					
					$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, us.idUsuario, 
					IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre, 
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, 
					IFNULL(us2.npuesto, 'NO APLICA') AS puesto, IFNULL(c.correo, ext.correo) AS correo, op.nombre AS estNut,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".detallepaciente dp 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = dp.idUsuario 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 13 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idpuesto = 537 
					LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario 
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = dp.estatusNut 
					LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
					WHERE estatusNut IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;

				case 585:
					
					$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, us.idUsuario, 
					IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre, 
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, 
					IFNULL(us2.npuesto, 'NO APLICA') AS puesto, IFNULL(c.correo, ext.correo) AS correo, op.nombre AS estPsi,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".detallepaciente dp 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = dp.idUsuario 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 13 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idpuesto = 585
					LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario 
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = dp.estatusPsi
					LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
					WHERE estatusPsi IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;
					
				case 158:

					$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, us.idUsuario, 
					IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre, 
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, 
					IFNULL(us2.npuesto, 'NO APLICA') AS puesto, IFNULL(c.correo, ext.correo) AS correo, op.nombre AS estQB,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".detallepaciente dp 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = dp.idUsuario 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 13 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idpuesto = 158
					LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario 
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = dp.estatusQB 
					LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
					WHERE estatusQB IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;

				case 686:

					$query = $this->ch-> query("SELECT DISTINCT dp.idDetallePaciente AS id, us.idUsuario, 
					IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre, 
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, 
					IFNULL(us2.npuesto, 'NO APLICA') AS puesto, IFNULL(c.correo, ext.correo) AS correo, op.nombre AS estGE,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".detallepaciente dp 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = dp.idUsuario 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 13 
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idpuesto = 686
					LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idRol = 3 AND us3.idContrato = us4.idcontrato 
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = us3.idUsuario 
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = dp.estatusGE
					LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
					WHERE estatusGE IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;
			}
		}else if($idRol == 3){

			switch($area){
				case 537:

					$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre,   
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, IFNULL(us2.npuesto, 'NO APLICA') AS puesto, op.nombre AS estNut,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".citas ct 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".detallepaciente dtp ON dtp.idUsuario = us.idUsuario
					LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = 13
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusNut
					WHERE ct.idEspecialista = $idUs AND estatusNut IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;

				case 585:

					$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre,   
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, IFNULL(us2.npuesto, 'NO APLICA') AS puesto, op.nombre AS estPsi,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".citas ct 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".detallepaciente dtp ON dtp.idUsuario = us.idUsuario
					LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = 13
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusPsi
					WHERE ct.idEspecialista = $idUs AND estatusPsi IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;

				case 158:

					$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre,   
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, IFNULL(us2.npuesto, 'NO APLICA') AS puesto, op.nombre AS estQB,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".citas ct 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".detallepaciente dtp ON dtp.idUsuario = us.idUsuario
					LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = 13
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusQB
					WHERE ct.idEspecialista = $idUs AND estatusQB IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;

				case 686:

					$query = $this->ch-> query("SELECT DISTINCT us.idUsuario, IFNULL (CONCAT((us2.nombre_persona), ' ',(us2.pri_apellido), ' ', (us2.sec_apellido)), ext.nombre) AS nombre,   
					IFNULL(us2.ndepto, 'NO APLICA') AS depto, IFNULL(us2.nsede, 'QRO') AS sede, IFNULL(us2.npuesto, 'NO APLICA') AS puesto, op.nombre AS estGE,
					CASE 
					WHEN us.externo = 0 THEN 'Colaborador' 
					WHEN us.externo = 1 THEN 'Externo' 
					END AS usuario
					FROM ". $this->schema_cm .".citas ct 
					LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
					LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
					LEFT JOIN ". $this->schema_cm .".detallepaciente dtp ON dtp.idUsuario = us.idUsuario
					LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = 13
					LEFT JOIN ". $this->schema_cm .".citas ci ON ci.idPaciente = us.idUsuario AND ci.idEspecialista = $idUs
					LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = cat.idCatalogo AND  op.idOpcion = dtp.estatusGE
					WHERE ct.idEspecialista = $idUs AND estatusGE IS NOT null AND ci.estatusCita = 4 $usuarioCond");
					break;
			}

		}
		
		return $query;
	}

	public function getCierrePacientes($dt){
		
		$idUsr = $dt["idUsr"];
		$idEsp = isset($dt["idEsp"][0]) ? $dt["idEsp"][0] : '0';
		$rol = $dt["roles"];
		$area = isset($dt["esp"][0]) ? $dt["esp"][0] : '0';
		$fechaI = $dt["fhI"];
		$fechaFn = $dt["fhF"];
		$mod = isset($dt["modalidad"][0]) ? $dt["modalidad"][0] : '0';
		$tipo = $dt["tipo"];
		$permisos = isset($dt["permisos"]) ? intval($dt["permisos"]) : 0;

		$usuarioCond = $tipo != 2 ? "AND us.externo = $tipo" : "";
		$usuarioCond2 = $tipo != 2 ? "AND us3.externo = $tipo" : "";

		$fecha = new DateTime($fechaFn);
		$fecha->modify('+1 day');
		$fechaF = $fecha->format('Y-m-d');

		if($area == "0" && ($rol == 4 || $permisos == 5) && $mod == "0"){

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente 
			WHERE ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF' AND ct.estatusCita = 4 $usuarioCond");
			return $query->result();

		}else if($area != "0" && $rol == 4 && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND us2.npuesto IN ('$ar') AND ct.estatusCita = 4 $usuarioCond2");
			return $query->result();

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);
			$nombres = implode("','", $dt["idEsp"]);

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND us2.npuesto IN ('$ar') AND ct.estatusCita = 4 
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres') $usuarioCond2");
			return $query->result();

		}else if($area == "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad')
			AND ct.estatusCita = 4 $usuarioCond2");
			return $query->result();

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF') 
			AND op2.nombre IN ('$modalidad') AND us2.npuesto IN ('$ar')
			AND ct.estatusCita = 4 $usuarioCond2");
			return $query->result();	

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$nombres = implode("','", $dt["idEsp"]);

			$query = $this->ch->query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaModificacion >= '$fechaI' 
			AND ct.fechaModificacion < '$fechaF') AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres')
			AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad') $usuarioCond2");
			return $query->result();

		}else if($rol == 3 && $mod == "0"){

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4 $usuarioCond2
			");
			return $query->result();
		
		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS TotalPacientes
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			INNER JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idPaciente 
			WHERE (ct.fechaModificacion >= '$fechaI' AND ct.fechaModificacion < '$fechaF')
			AND us.idUsuario = $idUsr AND ct.estatusCita = 4 AND op2.nombre IN ('$modalidad') $usuarioCond2");
			return $query->result();
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
		$permisos = isset($dt["permisos"]) ? $dt["permisos"] : 2;

		$mod = isset($dt["modalidad"][0]) ? $dt["modalidad"][0] : '0';

		$fecha = new DateTime($fechaFn);
		$fecha->modify('+1 day');
		$fechaF = $fecha->format('Y-m-d');

	if($reporte == 0){

		if($area == "0" && $mod == "0" && ($rol == 4 || $permisos == 5)){

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND us2.npuesto IN ('$ar')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND us2.npuesto IN ('$ar') 
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres')
			) AS sub");
			return $query;
		
		}else if($area == "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad') AND us2.npuesto IN ('$ar')
			) AS sub");
			return $query;	
		
		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres') 
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($rol == 3 && $mod == "0"){

			$query = $this->ch-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
				SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
				FROM (
					SELECT DISTINCT ct.idDetalle, dp.cantidad
					FROM ". $this->schema_cm .".citas ct
					INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
					INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
					WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
					AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
					AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM ". $this->schema_cm .".citas ct
				INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
				AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
				AND us.idUsuario = $idUsr AND op2.nombre IN ('$modalidad')
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}

	}else if($reporte !== 0 && $reporte !== 2)
	{

		if($area == "0" && $mod == "0" && ($rol == 4 || $permisos == 5)){

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			WHERE ct.estatusCita = $reporte AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE ct.estatusCita = $reporte AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND us2.npuesto IN ('$ar')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE ct.estatusCita = $reporte AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND us2.npuesto IN ('$ar')
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres') 
			) AS sub");
			return $query;
		
		}else if($area == "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;
		
		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte
			AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres') 
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM  ". $this->schema_cm .".citas ct
			INNER JOIN  ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN  ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN  ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN  ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE ct.estatusCita = $reporte AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad') AND us2.npuesto IN ('$ar')
			) AS sub");
			return $query;	

		}else if($rol == 3 && $mod == "0"){

			$query = $this->ch->query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM ". $this->schema_cm .".citas ct
				INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				WHERE ct.estatusCita = $reporte
				AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
				AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
				AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch->query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM ". $this->schema_cm .".citas ct
				INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE ct.estatusCita = $reporte
				AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
				AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
				AND us.idUsuario = $idUsr AND op2.nombre IN ('$modalidad')
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}
		
	}else if($reporte == 2)
	{

		if($area == "0" && $mod == "0" && ($rol == 4 || $permisos == 5)){

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM ( SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7) AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod == "0"){

			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7) 
			AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND ps.puesto IN ('$ar')
			) AS sub");
			return $query;
		
		}else if($area == "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7) AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod == "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7) AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND us2.npuesto IN ('$ar') 
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres') 
			) AS sub");
			return $query;
		
		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp != "0" && $mod != "0"){

			$nombres = implode("','", $dt["idEsp"]);
			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch->query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7)
			AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) IN ('$nombres')  
			AND op2.nombre IN ('$modalidad')
			) AS sub");
			return $query;

		}else if($area != "0" && ($rol == 4 || $permisos == 5) && $idEsp == "0" && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);
			$ar = implode("','", $dt["esp"]);

			$query = $this->ch-> query("SELECT SUM(sub.TotalCantidad) AS TotalIngreso
			FROM (SELECT DISTINCT ct.idDetalle, dp.cantidad AS TotalCantidad
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
			LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
			WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7) 
			AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
			AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
			AND op2.nombre IN ('$modalidad') AND us2.npuesto IN ('$ar')
			) AS sub");
			return $query;	

		}else if($rol == 3 && $mod == "0"){

			$query = $this->ch-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM citas ct
				INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7)
				AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
				AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
				AND us.idUsuario = $idUsr
				) AS distinct_ct
			) AS subconsulta;");
			return $query;

		}else if($rol == 3 && $mod != "0"){

			$modalidad = implode("','", $dt["modalidad"]);

			$query = $this->ch-> query("SELECT COALESCE(SUM(TotalIngreso), 0) AS TotalIngreso
			FROM (
			SELECT SUM(distinct_ct.cantidad) AS TotalIngreso
			FROM (
				SELECT DISTINCT ct.idDetalle, dp.cantidad
				FROM ". $this->schema_cm .".citas ct
				INNER JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
				LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
				WHERE (ct.estatusCita = 2 OR ct.estatusCita = 7)
				AND (ct.fechaCreacion >= '$fechaI' AND ct.fechaCreacion < '$fechaF')
				AND (dp.estatusPago = 1 OR dp.estatusPago = 3)
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

		$query = $this->ch-> query("SELECT * FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
		INNER JOIN ". $this->schema_cm .".usuarios AS us2 ON us2.idContrato = us.idcontrato 
		WHERE us2.idRol =  3");
		return $query;

		}else{

			$area1 = isset($dt["esp"][0]) ? $dt["esp"][0] : '';
			$area2 = isset($dt["esp"][1]) ? $dt["esp"][1] : '';
			$area3 = isset($dt["esp"][2]) ? $dt["esp"][2] : '';
			$area4 = isset($dt["esp"][3]) ? $dt["esp"][3] : '';
			
			$query = $this->ch-> query("SELECT CONCAT(IFNULL(us.nombre_persona, ''), ' ', IFNULL(us.pri_apellido, ''), ' ', IFNULL(us.sec_apellido, '')) AS nombre, us2.idUsuario 
			FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
			INNER JOIN ". $this->schema_cm .".usuarios AS us2 ON us2.idContrato = us.idcontrato 
			WHERE us2.idRol =  3 AND us.npuesto IN ('$area1', '$area2', '$area3', '$area4')");
			return $query;

		}

	}

	public function getEspeUser($dt){

		$query = $this->ch-> query("SELECT us.idpuesto AS idPuesto, us.npuesto AS puesto
		FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
		INNER JOIN ". $this->schema_cm .".usuarios AS us2 ON us2.idContrato = us.idcontrato 
		WHERE us2.idUsuario = $dt;");
		return $query;

	}

	public function getAppointmentHistory($dt){

        $idUsuario = $dt["idUser"];
        $idRol = $dt["idRol"];
        $idEspe = $dt["idEspe"];
        $espe = $dt["espe"];
		$tipoUsuario = $dt["tipoUsuario"];

		$usuarioCond = $tipoUsuario != 2 ? "AND us.externo = $tipoUsuario" : "";

        if($idRol == 1  || $idRol == 4){

        $query = $this->ch->query("SELECT IFNULL (CONCAT((us3.nombre_persona), ' ',(us3.pri_apellido), ' ', (us3.sec_apellido)), ext.nombre) AS nombre, 
        CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, ct.idPaciente, ct.titulo,
        oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
        CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
        IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'SIN MOTIVOS DE CITA') AS motivoCita
        FROM ". $this->schema_cm .".citas ct 
        LEFT JOIN ". $this->schema_cm .".catalogos ca ON ca.idCatalogo = 2
        LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
        LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
		LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us.idContrato
        LEFT JOIN ". $this->schema_cm .".usuarios es ON es.idUsuario = ct.idEspecialista
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = es.idContrato
        LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
        LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
        LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
        LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = CASE 
            WHEN us2.idpuesto = 537 THEN 8
            WHEN us2.idpuesto = 585 THEN 7
            WHEN us2.idpuesto = 686 THEN 9
            WHEN us2.idpuesto = 158 THEN 6
            ELSE us2.idpuesto END 
          LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo
        WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND us2.idpuesto = $espe $usuarioCond
        GROUP BY us2.nombre_persona, us2.pri_apellido,us2.sec_apellido,us3.nombre_persona, us3.pri_apellido,    
          us3.sec_apellido, ext.nombre, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita, ct.fechaInicio, ct.fechaFinal, mpc.idCita
        ORDER BY ct.fechaInicio, ct.fechaFinal DESC");

        return $query;

        }else if($idRol == 3){
    
            $query = $this->ch->query("SELECT IFNULL (CONCAT((us3.nombre_persona), ' ',(us3.pri_apellido), ' ', (us3.sec_apellido)), ext.nombre) AS nombre, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, ct.idPaciente, ct.titulo, 
            oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
            CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
            IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'SIN MOTIVOS DE CITA') AS motivoCita
            FROM ". $this->schema_cm .".citas ct 
            LEFT JOIN ". $this->schema_cm .".catalogos ca ON ca.idCatalogo = 2
            LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
            LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
			LEFT JOIN ". $this->schema_cm .".usuariosexternos ext ON ext.idcontrato = us.idContrato
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us.idContrato
            LEFT JOIN ". $this->schema_cm .".usuarios es ON es.idUsuario = ct.idEspecialista
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = es.idContrato
            LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
            LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
            LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = CASE 
                WHEN us2.idpuesto = 537 THEN 8
                WHEN us2.idpuesto = 585 THEN 7
                WHEN us2.idpuesto = 686 THEN 9
                WHEN us2.idpuesto = 158 THEN 6
                ELSE us2.idpuesto END 
              LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo
            WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND us2.idpuesto = $espe AND es.idUsuario = $idEspe $usuarioCond
            GROUP BY us2.nombre_persona, us2.pri_apellido,us2.sec_apellido,us3.nombre_persona, us3.pri_apellido,    
              us3.sec_apellido, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita, ct.fechaInicio, ct.fechaFinal, mpc.idCita
            ORDER BY ct.fechaInicio, ct.fechaFinal DESC");
            return $query;

        }

    }

	public function demandaDepartamentos(){

		$query = $this->ch->query("SELECT
			us.iddepto AS id,
			us.ndepto AS label,
			COUNT(CASE WHEN ct.estatusCita = 4 THEN 1 END) AS value
		FROM
			". $this->schema_ch .".beneficioscm_vista_usuarios us
		LEFT JOIN ". $this->schema_cm .".usuarios us2 ON
			us2.idContrato = us.idcontrato
		LEFT JOIN ". $this->schema_cm .".citas ct ON
			ct.idPaciente = us2.idUsuario
		LEFT JOIN ". $this->schema_cm .".datopuesto dp ON
			dp.idPuesto = us.idpuesto
		WHERE
			dp.canRegister = 1
		GROUP BY
			us.ndepto");
		return $query;

	}

	public function allDemandaAreas(){

		$query = $this->ch->query("SELECT us.idarea AS id, us.narea AS label, 
		COUNT(CASE WHEN ct.estatusCita = 4 THEN 1 END) AS value
		FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us ON us.idarea = ar.idsubarea 
		LEFT JOIN ". $this->schema_cm .".usuarios us2 ON us2.idContrato = us.idcontrato 
		LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us2.idUsuario
		GROUP BY us.narea");
		return $query;

	}

	public function demandaAreas($dt){

		$query = $this->ch->query("SELECT us.idarea AS id, us.narea AS label,
		COUNT(CASE WHEN ct.estatusCita = 4 THEN 1 END) AS value
		FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us ON us.idarea = ar.idsubarea 
		LEFT JOIN ". $this->schema_cm .".usuarios us2 ON us2.idContrato = us.idcontrato 
		LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us2.idUsuario
		WHERE dep.iddepto = $dt
		GROUP BY us.narea");
		return $query;

	}

	public function demandaPuestos($dt){

		$query = $this->ch->query("SELECT us.idpuesto AS id, us.npuesto  AS label, 
		COUNT(CASE WHEN ct.estatusCita = 4 THEN 1 END) AS value
		FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea  
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us ON us.idpuesto = ps.idpuesto  
		LEFT JOIN ". $this->schema_cm .".usuarios us2 ON us2.idContrato = us.idcontrato 
		LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us2.idUsuario
		WHERE ar.idsubarea = $dt
		GROUP BY us.npuesto ");
		return $query;

	}

}
