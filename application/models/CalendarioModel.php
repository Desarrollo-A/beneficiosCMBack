<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CalendarioModel extends CI_Model
{
    public function __construct()
	{
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
	}
    
    public function getAppointmentsByUser($year, $month, $idUsuario){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end',
            ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
            ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, ct.tipoCita, atc.tipoCita as modalidad, atc.idSede , usEspe2.idpuesto AS idPuesto, 
            us2.telefono_personal AS telPersonal, usEspe2.telefono_personal as telefonoEspecialista, usEspe2.idarea AS idArea, sed.nsede AS sede, 
            atc.idOficina, c.correo AS correo, c2.correo as correoEspecialista, 
            CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) AS especialista,
            usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion, dp.estatusPago, ec.idEncuesta,
            CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
            CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', 
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
            END AS 'color', 
            CASE WHEN usEspe2.idpuesto = 537 THEN 'nutrición' WHEN usEspe2.idpuesto = 585 THEN 'psicología' WHEN usEspe2.idpuesto = 686 THEN 'guía espiritual' WHEN usEspe2.idpuesto = 158 THEN 'quantum balance' END AS 'beneficio',
            ct.fechaIntentoPago 
            FROM ". $this->schema_cm .".citas AS ct 
            INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idPaciente 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
            INNER JOIN ". $this->schema_cm .".atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede 
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sed ON sed.idsede = atc.idSede 
            LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM ". $this->schema_cm .".citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
            LEFT JOIN ". $this->schema_cm .".detallepagos as dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".encuestascreadas AS ec ON ec.idArea = usEspe2.idpuesto AND ec.estatus = 1
            LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us2.idcontrato 
            LEFT JOIN ". $this->schema_cm .".correostemporales c2 ON c2.idContrato = usEspe2.idcontrato 
            WHERE YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ? AND ct.idPaciente = ? AND ct.estatus = ? AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?, ?) GROUP BY (id);",
            array( $year, $month, $idUsuario, 1, 1, 2, 3, 4, 5, 6, 7, 10)

        );

        return $query;
    }

    public function getBeneficiosPorSede($sede, $area)
	{
        $query = $this->ch->query(
            "SELECT DISTINCT us2.idpuesto as 'idPuesto', us2.npuesto as 'puesto'
            FROM ". $this->schema_cm .".usuarios AS us 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            RIGHT JOIN ". $this->schema_cm .".atencionxsede AS axs ON axs.idEspecialista = us.idUsuario
            INNER JOIN ". $this->schema_cm .".opcionesporcatalogo AS opc ON opc.idOpcion= axs.tipoCita
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON s.idsede = us2.idsede
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas as ofi ON ofi.idoficina = axs.idOficina
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS so ON so.idsede = ofi.idsede
            WHERE us.estatus = 1 AND s.estatus_sede = 1 AND axs.estatus = 1  AND us.idRol = 3 AND opc.idCatalogo = 5
            AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) ;", array($sede, $area)
        );

        return $query;
	}

    public function getEspecialistaPorBeneficioYSede($sede, $area, $beneficio)
    {
        $query = $this->ch->query(
            "SELECT DISTINCT us.idUsuario as id, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista
            FROM ". $this->schema_cm .".usuarios AS us
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
            RIGHT JOIN ". $this->schema_cm .".atencionxsede AS axs ON axs.idEspecialista = us.idUsuario 
            INNER JOIN ". $this->schema_cm .".opcionesporcatalogo AS opc ON opc.idOpcion= axs.tipoCita 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON s.idsede = us2.idsede 
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas as ofi ON ofi.idoficina = axs.idOficina
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS so ON so.idsede = ofi.idsede
            WHERE us.estatus = 1 AND s.estatus_sede = 1 AND axs.estatus = 1 AND us.idRol = 3 AND opc.idCatalogo = 5 
            AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) AND us2.idpuesto = ?;", array($sede, $area, $beneficio)
        );

        return $query;
    }

    public function getModalidadesEspecialista($sede, $especialista, $area)
    {
        $query = $this->ch->query(
            "SELECT CASE WHEN tipoCita = 1 then 'PRESENCIAL' WHEN tipoCita = 2 THEN 'EN LíNEA' END AS 'modalidad', us.idUsuario as id,
            us2.idpuesto,  CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista,
            ofi.direccion as ubicacionOficina, axs.tipoCita, axs.idAtencionXSede, us2.nsede as lugarAtiende 
            FROM ". $this->schema_cm .".atencionxsede AS axs
            INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = axs.idEspecialista 
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = axs.idOficina 
            WHERE axs.estatus = ? AND axs.idSede = ? AND ((axs.idEspecialista = ? AND axs.idArea is NULL ) OR (axs.idEspecialista = ? AND axs.idArea = ?));", 
            array(1, $sede, $especialista, $especialista, $area));

        return $query;
    }

    public function getModalidadesEspecialistaBene($sede, $especialista, $area)
    {
        $atXsed = "";

		// Excepcion de especialistas para hacer citas sin importar su sedes asignadas
		if($especialista == 7 || $especialista == 8 || $especialista == 6 || $especialista == 108){
            $query = $this->ch->query("SELECT CASE WHEN tipoCita = 1 then CONCAT('PRESENCIAL - ', ofi.direccion) WHEN tipoCita = 2 THEN 'EN LíNEA' END AS 'modalidad',
            us.idUsuario as id, us2.idpuesto, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, 
            ofi.direccion as ubicacionOficina, atxs.tipoCita, atxs.idAtencionXSede
            FROM ". $this->schema_cm .".usuarios us
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
            INNER JOIN ". $this->schema_cm .".atencionxsede atxs ON atxs.idEspecialista = us.idUsuario
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atxs.idOficina
            WHERE us.idUsuario = $especialista");
		}else{
			$query = $this->ch->query(
                "SELECT CASE WHEN tipoCita = 1 then 'PRESENCIAL' WHEN tipoCita = 2 THEN 'EN LíNEA' END AS 'modalidad', us.idUsuario as id,
                us2.idpuesto,  CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista,
                ofi.direccion as ubicacionOficina, axs.tipoCita, axs.idAtencionXSede, us2.nsede as lugarAtiende 
                FROM ". $this->schema_cm .".atencionxsede AS axs
                INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = axs.idEspecialista 
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
                LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = axs.idOficina 
                WHERE axs.estatus = ? AND axs.idSede = ? AND ((axs.idEspecialista = ? AND axs.idArea is NULL ) OR (axs.idEspecialista = ? AND axs.idArea = ?));", 
                array(1, $sede, $especialista, $especialista, $area));
		}
        return $query;
    }
    
    public function getDiasDisponiblesAtencionEspecialista($idUsuario, $idSede){
        $query = $this->ch->query(
        "SELECT * FROM ". $this->schema_cm .".presencialxsede 
        WHERE idEspecialista = ? AND idSede= ?
        AND MONTH(presencialDate) >= MONTH(CURDATE()) 
        AND MONTH(presencialDate) <= MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH));", array($idUsuario, $idSede));
    
        return $query;
    }

    public function getOficinaByAtencion($sede, $especialista, $modalidad)
    {
        $query = $this->ch->query(
            "SELECT axs.idAtencionXSede, axs.idEspecialista, axs.idSede, axs.tipoCita,  axs.estatus,
            ofi.idoficina AS 'idOficina', ofi.noficina AS oficina, ofi.direccion AS ubicación
            from ". $this->schema_cm .".atencionxsede AS axs
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON axs.idOficina = ofi.idoficina
            WHERE axs.estatus = 1 AND
            axs.idSede = ? AND axs.idEspecialista = ? AND axs.tipoCita = ?;", array($sede, $especialista, $modalidad)
        );

        return $query;
    }
    
    public function getHorarioBeneficio($beneficio, $especialista){

        $queryEspecialistas = $this->ch->query(
            "SELECT * FROM ". $this->schema_cm .".horariosespecificos WHERE idEspecialista = ? AND estatus = 1",
            array($especialista)
        );

        if ($queryEspecialistas->num_rows() > 0) {

            return $queryEspecialistas;
            
        }else{

            $queryBeneficio = $this->ch->query(
                "SELECT * FROM ". $this->schema_cm .".horariosporbeneficio WHERE idBeneficio = ?",
                array($beneficio)
            );

            return $queryBeneficio;
        }
    }

    public function getOccupiedRange($fechaInicio, $fechaFin, $idUsuario){
        $query = $this->ch->query(
            "SELECT idOcupado as id, titulo as title, fechaInicio as occupied, fechaInicio, fechaFinal 
            FROM ". $this->schema_cm .".horariosocupados 
            WHERE idEspecialista = ? AND estatus = ? AND 
            ((fechaInicio BETWEEN ? AND ?) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?));",
            array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );
        return $query;
    }

    public function getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio, ct.fechaFinal, 
            ct.estatusCita, ct.idPaciente, ct.idEspecialista
            FROM ". $this->schema_cm .".citas ct
            LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            WHERE (ct.idEspecialista = ? OR ct.idPaciente = ?) AND ct.estatusCita IN (?, ?, ?)
            AND ((fechaInicio BETWEEN ? AND ? ) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?))",
            array( $especialista, $usuario, 1, 6, 10,  $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );

        return $query;
    }

    public function getLastAppointment($usuario, $beneficio) {
        $query = $this->ch->query("SELECT ct.*, us2.idPuesto AS 'idPuesto', axs.tipoCita 
        FROM ". $this->schema_cm .".citas AS ct 
        INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idEspecialista 
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_cm .".atencionxsede AS axs ON axs.idAtencionXSede = ct.idAtencionXSede 
        WHERE ct.idPaciente = ? AND us2.idpuesto = ? ORDER BY idCita DESC LIMIT 1;", array($usuario, $beneficio));
    
        return $query;
    }
    
    public function isPrimeraCita($usuario, $beneficio) {
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".citas AS ct 
            INNER JOIN ". $this->schema_cm .".usuarios as us ON us.idUsuario = ct.idEspecialista
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            WHERE us2.activo = 1 AND ct.idPaciente = ? AND us2.idpuesto = ?;",
            array($usuario, $beneficio)
        );

        return $query;
    }

    public function getSedesDeAtencionEspecialista($idUsuario){
        $query = $this->ch->query(
        "SELECT axs.idSede as value, s.nsede AS label
        FROM ". $this->schema_cm .".atencionxsede AS axs
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON s.idsede = axs.idSede
        WHERE axs.idEspecialista=?  AND axs.tipoCita = ?", array($idUsuario, 1));

        return $query;
    }

    public function checkPresencial($idSede, $idEspecialista, $fecha){
        $query = $this->ch->query(
            "SELECT *from ". $this->schema_cm .".presencialxsede as pxs
            WHERE pxs.idSede = ? AND pxs.idEspecialista = ? AND presencialDate = ?;",
            array( $idSede, $idEspecialista, $fecha)
        );

        return $query;
    }

    public function checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND ((idPaciente = ?
            AND estatusCita IN (?, ?, ?))
            OR (idEspecialista = ? and estatusCita IN (?, ?, ?)))",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $dataValue["idPaciente"],
                1, 6, 10,
                $dataValue["idUsuario"],
                1, 6, 10
            )
        );
        
        return $query;
    }

    public function checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".horariosocupados WHERE 
            ((fechaInicio BETWEEN ? AND ?) 
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal) 
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idUsuario"],
                1
            )
        );

        return $query;
    }

    public function checkOccupiedId($dataValue, $fechaInicioSuma ,$fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".horariosocupados WHERE 
            ((fechaInicio BETWEEN ? AND ?) 
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal) 
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idUnico != ?
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["id"],
                $dataValue["idUsuario"],
                1
            )
        );

        return $query;
    }

    public function getReasons($puesto){
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".opcionesporcatalogo where idCatalogo = ?", $puesto);

        return $query->result();
    }

    public function checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idCita != ?
            AND ((idPaciente = ?
            AND estatusCita IN(?, ?, ?))
            OR (idEspecialista = ? AND estatusCita IN(?, ?, ?)))",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id"],
                $dataValue["idPaciente"],
                1,
                6,
                10,
                $dataValue["idUsuario"],
                1,
                6,
                10
            )
        );

        return $query;
    }

    public function getCitaById($idCita){
        $query = $this->ch->query("SELECT TRIM(CAST(idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', usEspe2.idpuesto AS idPuesto, ct.idEspecialista, 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) as nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
        ct.tipoCita, atc.tipoCita as modalidad, atc.idSede, us2.telefono_personal AS 'telPersonal', usEspe2.telefono_personal AS telefonoEspecialista, 
        CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina', CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación',
        usEspe2.idarea AS 'idArea', s.nsede AS 'sede', atc.idOficina, c.correo AS correo, c2.correo as correoEspecialista, 
        CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) as especialista,
        usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion, dp.estatusPago, ec.idEncuesta,
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
        END AS 'color',
        CASE 
        WHEN usEspe2.idpuesto = 537 THEN 'nutrición'
        WHEN usEspe2.idpuesto = 585 THEN 'psicología'
        WHEN usEspe2.idpuesto = 686 THEN 'guía espiritual'
        WHEN usEspe2.idpuesto = 158 THEN 'quantum balance'
        END AS 'beneficio'
        FROM ". $this->schema_cm .".citas ct
        INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idPaciente
        INNER JOIN ". $this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
        INNER JOIN ". $this->schema_cm .".atencionxsede AS atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON s.idsede = atc.idSede
        LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM ". $this->schema_cm .".citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle
        LEFT JOIN ". $this->schema_cm .".detallepagos as dp ON dp.idDetalle = ct.idDetalle
        LEFT JOIN ". $this->schema_cm .".encuestascreadas AS ec ON ec.idArea = usEspe2.idpuesto
        LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us2.idcontrato 
        LEFT JOIN ". $this->schema_cm .".correostemporales c2 ON c2.idContrato = usEspe2.idcontrato 
        WHERE idCita = ? GROUP BY (id)",
        array( $idCita ));

        return $query;
    }

    public function getOccupied($month, $idUsuario, $dates){
        $query = $this->ch->query(
            "SELECT idUnico as id, titulo as title, fechaInicio as 'start', fechaFinal as 'end',
            'purple' AS 'color', estatus, 'cancel' AS 'type'
            FROM ". $this->schema_cm .".horariosocupados
            WHERE YEAR(fechaInicio) IN (?, ?)
            AND MONTH(fechaInicio) IN (?, ?, ?)
            AND idEspecialista = ?  
            AND estatus = ?",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
        );
        return $query;
    }

    public function checkInvoice($idDetalle){
        $query = $this->ch->query("SELECT idDetalle FROM ". $this->schema_cm .".citas WHERE idDetalle = ? GROUP BY idDetalle HAVING COUNT(idDetalle) > ?", array($idDetalle, 2));

        return $query;
    }

    public function checkDetailPacient($user, $column){
        $query = $this->ch->query("SELECT $column FROM ". $this->schema_cm .".detallepaciente 
        WHERE idUsuario = ?;", array($user));
   
        return $query;
    }

    public function getEventReasons($idCita){
        $query = $this->ch->query("SELECT oxc.idOpcion, oxc.nombre FROM ". $this->schema_cm .".motivosporcita AS mpc
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo AS oxc ON oxc.idOpcion = mpc.idMotivo
        INNER JOIN ". $this->schema_cm .".citas AS ct ON ct.idCita = mpc.idCita
        INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        WHERE ct.idCita = ? AND idCatalogo = 
        CASE us2.idpuesto
        WHEN 537 THEN 8
        WHEN 585 THEN 7
        WHEN 802 THEN 7
        WHEN 859 THEN 7
        WHEN 686 THEN 9
        WHEN 158 THEN 6
        END", $idCita );

        return $query;
    }

    public function getDetallePago($folio){
        $query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".detallepagos WHERE folio = ?", array($folio));

        return $query;
    }

    public function getAtencionPorSede($especialista, $sede, $area, $modalidad)
    {
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".atencionxsede 
            WHERE estatus = 1 AND idEspecialista = ?
            AND ((idSede = ? AND idArea is NULL ) OR (idSede = ? AND idArea = ?))
            AND tipoCita = ? ;", array($especialista, $sede, $sede, $area, $modalidad)
        );
        return $query;
    }

    public function getIdAtencion($dataValue){
        $query = $this->ch->query(
            "SELECT idAtencionXSede FROM ". $this->schema_cm .".atencionxsede 
            WHERE idEspecialista = ?
            AND idSede = ( 
                SELECT idSede FROM ". $this->schema_cm .".usuarios AS us 
				INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
				WHERE idUsuario = ? ) AND estatus = ?", 
            array($dataValue["idUsuario"], $dataValue["idUsuario"], 1)
        );
        
        return $query;
    }

    public function getCitasFinalizadasUsuario($usuario, $mes, $año)
    {
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".citas
            WHERE idPaciente = ? AND MONTH(fechaInicio) = ?
            AND YEAR(fechaInicio) = ? AND estatusCita IN (4, 1, 6, 10) AND tipoCita IN (1, 2);", array($usuario, $mes, $año)
        );

        return $query;
    }

    public function getCitasSinPagarUsuario($usuario)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM ". $this->schema_cm .".citas AS ct
            WHERE ct.idPaciente = ? AND ct.idDetalle is NULL AND ct.estatusCita IN (?, ?);",array($usuario, 6, 10)
        );

        return $query;
    }

    public function getCitasSinEvaluarUsuario($usuario)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM ". $this->schema_cm .".citas AS ct
            WHERE ct.idPaciente = ? AND ct.evaluacion is NULL AND ct.estatusCita IN (?)",array($usuario, 4)
        );

        return $query;
    }

    public function getCitasSinFinalizarUsuario($usuario, $beneficio)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM ". $this->schema_cm .".citas AS ct
            INNER JOIN ". $this->schema_cm .".usuarios as us ON ct.idEspecialista = us.idUsuario
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
            WHERE ct.idPaciente = ? AND us2.idpuesto = ? AND ct.estatusCita IN (1, 6, 10);",array($usuario, $beneficio)
        );

        return $query;
    }

    public function getAppointment($month, $idUsuario, $dates){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end',
            ct.fechaInicio AS occupied, 'date' AS 'type', ct.estatusCita AS estatus, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre, ct.idPaciente, us2.telefono_personal AS telPersonal,
            c.correo AS correo,
            se.nsede AS sede, ofi.noficina as oficina, ct.idDetalle, ct.idAtencionXSede, us.externo, CONCAT(IFNULL(usEspCH.nombre_persona, ''), ' ', IFNULL(usEspCH.pri_apellido, ''), ' ', IFNULL(usEspCH.sec_apellido, '')) AS especialista, ct.fechaCreacion, usEspCH.tipo_puesto AS tipoPuesto,
            tf.fechasFolio, idEventoGoogle, ct.tipoCita, aps.tipoCita as modalidad, aps.idSede, dp.estatusPago, us2.idsede AS idSedePaciente,
            CASE
                WHEN ct.estatusCita = 0 THEN '#ff0000'
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
            END AS color,
            CASE
            WHEN usEspCH.idPuesto = 537 THEN 'nutrición'
            WHEN usEspCH.idPuesto= 585 THEN 'psicología'
            WHEN usEspCH.idPuesto = 686 THEN 'guía espiritual'
            WHEN usEspCH.idPuesto = 158 THEN 'quantum balance'
            END AS beneficio, ct.fechaIntentoPago 
            FROM ". $this->schema_cm .".citas AS ct
            INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEspCH ON usEspCH.idcontrato = usEspe.idContrato
            INNER JOIN ". $this->schema_cm .".atencionxsede AS aps ON ct.idAtencionXSede = aps.idAtencionXSede
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS se ON se.idsede = aps.idSede
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = aps.idOficina
            LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM ". $this->schema_cm .".citas WHERE estatusCita IN( ? ) AND citas.idCita = idCita GROUP BY citas.idDetalle) AS tf ON tf.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".detallepagos as dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
            WHERE YEAR(fechaInicio) IN (?, ?)
            AND MONTH(fechaInicio) IN (?, ?, ?)
            AND ct.idEspecialista = ?
            AND ct.estatus IN (1)
            AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?, ?)",
            array( 8, $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4, 5, 6, 7, 10 )
        );

        return $query;
    }

    public function getExternalAppointments($month, $idUsuario, $dates){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', ue.nombre,
            ct.fechaInicio AS occupied, 'date' AS 'type', ct.estatusCita AS estatus, ct.idPaciente, ue.telPersonal AS telPersonal, ue.correo AS correo,
            ct.idDetalle, ct.idAtencionXSede, us.externo, CONCAT(IFNULL(usEspCH.nombre_persona, ''), ' ', IFNULL(usEspCH.pri_apellido, ''), ' ', IFNULL(usEspCH.sec_apellido, '')) AS especialista, ct.fechaCreacion, usEspCH.tipo_puesto AS tipoPuesto,
            tf.fechasFolio, ct.idEventoGoogle, ct.tipoCita, aps.tipoCita as modalidad, aps.idSede, dp.estatusPago,
            CASE WHEN ct.estatusCita = 0 THEN '#ff0000'
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
                END AS color,
            CASE WHEN usEspCH.idPuesto = 537 THEN 'nutrición'
               WHEN usEspCH.idPuesto= 585 THEN 'psicología'
               WHEN usEspCH.idPuesto = 686 THEN 'guía espiritual'
               WHEN usEspCH.idPuesto = 158 THEN 'quantum balance'
               END AS beneficio, ct.fechaIntentoPago 
            FROM ".$this->schema_cm .".citas AS ct 
            LEFT JOIN ".$this->schema_cm .".usuarios as us ON us.idUsuario = ct.idPaciente
            LEFT JOIN ".$this->schema_cm .".usuariosexternos AS ue ON ue.idContrato = us.idContrato
            LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM 
                ".$this->schema_cm .".citas WHERE estatusCita IN( ? ) AND citas.idCita = idCita GROUP BY citas.idDetalle) AS tf ON tf.idDetalle = ct.idDetalle
            LEFT JOIN ".$this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
            LEFT JOIN ".$this->schema_ch .".beneficioscm_vista_usuarios AS usEspCH ON usEspCH.idcontrato = usEspe.idContrato
            LEFT JOIN ".$this->schema_cm .".atencionxsede AS aps ON ct.idAtencionXSede = aps.idAtencionXSede
            LEFT JOIN ".$this->schema_cm .".detallepagos AS dp ON dp.idDetalle = ct.idDetalle
            WHERE YEAR(fechaInicio) IN (?, ?) AND MONTH(fechaInicio) IN (?, ?, ?)   
            AND ct.estatus IN (1) AND ct.idEspecialista = ? AND us.externo = ? AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?, ?);",
            array( 8, $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 1, 2, 3, 4, 5, 6, 7, 10 )
        );

        return $query;
    }

    public function checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".citas AS ct WHERE
            ((ct.fechaInicio BETWEEN ? AND ?)
            OR (ct.fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN ct.fechaInicio AND ct.fechaFinal)
            OR (? BETWEEN ct.fechaInicio AND ct.fechaFinal))
            AND ct.idEspecialista = ? AND ct.estatusCita IN(?, ?, ?)",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idUsuario"],
                1,
                6,
                10
            )
        );
        
        return $query;
    }

    public function getPending($idUsuario){
        $query = $this->ch->query(
            "SELECT ct.idCita as id, ct.titulo, ct.fechaInicio as 'start', ct.fechaFinal as 'end', 
            CONCAT(IFNULL(usEsp2.nombre_persona, ''), ' ', IFNULL(usEsp2.pri_apellido, ''), ' ', IFNULL(usEsp2.sec_apellido, '')) AS especialista,
            c.correo as correo, sed.nsede as sede, ofi.noficina AS oficina,
            CASE 
            WHEN usEsp2.idpuesto = 537 THEN 'nutrición'
            WHEN usEsp2.idpuesto = 585 THEN 'psicología'
            WHEN usEsp2.idpuesto = 686 THEN 'guía espiritual' 
            WHEN usEsp2.idpuesto = 158 THEN 'quantum balance'
            END AS 'beneficio'
            FROM ". $this->schema_cm .".citas AS ct
            INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idPaciente
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".usuarios AS usEsp ON usEsp.idUsuario = ct.idEspecialista
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEsp2 ON usEsp2.idcontrato = usEsp.idContrato
            INNER JOIN ". $this->schema_cm .".atencionxsede AS ats ON ats.idAtencionXSede = ct.idAtencionXSede
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sed ON sed.idsede = ats.idSede
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = ats.idOficina
            LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us2.idcontrato 
            WHERE ct.estatus IN (1) AND estatusCita IN(?) AND ct.idEspecialista = ? AND fechaInicio < CURRENT_TIMESTAMP();", array(1, $idUsuario));

        return $query;
    }
    
    public function getPendientesPago($idUsuario){
        $query = $this->ch->query("SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', usEspe2.idpuesto AS idPuesto,
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre, ct.idPaciente, us2.telefono_personal, CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
        CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', sed.nsede AS sede, atc.idOficina, 
        c.correo AS correo, c2.correo AS correoEspecialista, ct.idEspecialista, 
        CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) AS especialista, ct.idDetalle, usEspe2.telefono_personal as telefonoEspecialista,
        usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion, dp.estatusPago, ec.idEncuesta,
        CASE WHEN usEspe2.idpuesto = 537 THEN 'Nutrición'
        WHEN usEspe2.idpuesto = 585 THEN 'Psicología'
        WHEN usEspe2.idpuesto = 686 THEN 'Guía espiritual'
        WHEN usEspe2.idpuesto = 158 THEN 'Quantum balance'
        END AS beneficio, ct.fechaIntentoPago 
        FROM ". $this->schema_cm .".citas AS ct
        INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idPaciente
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
        INNER JOIN ". $this->schema_cm .".atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede
        LEFT join ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
        INNER join ". $this->schema_ch .".beneficioscm_vista_sedes AS sed ON sed.idSede = atc.idSede
		LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM ". $this->schema_cm .".citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
        LEFT JOIN ". $this->schema_cm .".detallepagos as dp ON dp.idDetalle = ct.idDetalle
        LEFT JOIN ". $this->schema_cm .".encuestascreadas AS ec ON ec.idArea = usEspe2.idpuesto
        LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
        LEFT JOIN ". $this->schema_cm .".correostemporales AS c2 ON c2.idContrato = usEspe2.idcontrato 
        WHERE ct.estatus IN (1) AND ct.estatusCita IN(?) AND ct.idPaciente = ? GROUP BY (id)", array(6, $idUsuario));


        return $query;
    }

    public function getPendientesEvaluacion($idUsuario){
        $query = $this->ch->query("SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', usEspe2.idpuesto as idPuesto,
            ct.fechaInicio AS occupied, ct.estatusCita AS estatus, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre, ct.idPaciente, us2.telefono_personal, CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
            CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', sed.nsede AS sede, atc.idOficina, c.correo AS correo, c2.correo AS correoEspecialista,  ct.idEspecialista,
            CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) AS especialista, ct.idDetalle, usEspe2.telefono_personal as telefonoEspecialista,
            usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion, ct.idEspecialista AS idEspecialista, ec.idEncuesta, dp.estatusPago,
            CASE 
            WHEN usEspe2.idpuesto = 537 THEN 'Nutrición'
            WHEN usEspe2.idpuesto = 585 THEN 'Psicología'
            WHEN usEspe2.idpuesto = 686 THEN 'Guía espiritual'
            WHEN usEspe2.idpuesto = 158 THEN 'Quantum balance'
            END AS beneficio, ct.fechaIntentoPago 
            FROM ". $this->schema_cm .".citas AS ct
            INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = ct.idPaciente
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
            INNER JOIN ". $this->schema_cm .".atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede  
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sed ON sed.idSede = atc.idSede
		    LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM ". $this->schema_cm .".citas WHERE estatusCita IN(8) AND estatus IN (1)  GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
            INNER JOIN ". $this->schema_cm .".encuestascreadas AS ec ON ec.idArea = usEspe2.idpuesto
            LEFT JOIN ". $this->schema_cm .".detallepagos as dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
            LEFT JOIN ". $this->schema_cm .".correostemporales AS c2 ON c2.idContrato = usEspe2.idcontrato 
            WHERE AND ct.estatus IN (1) AND ct.estatusCita IN(?) AND ct.evaluacion is NULL AND ct.idPaciente = ? GROUP BY (id)", array(4, $idUsuario));

        return $query;
    }

    public function cancelaCitasPorBajaUsuario($idContrato){
        $query = $this->ch->query(
            "UPDATE ". $this->schema_cm .".citas SET estatusCita = 11, modificadoPor = 1, fechaModificacion = CURRENT_TIMESTAMP() 
            WHERE idPaciente = (SELECT idUsuario FROM ". $this->schema_cm .".usuarios WHERE idContrato = ?) AND estatus = 1;", array($idContrato));
        return $query;
    }

    public function getBeneficioActivo($idUsuario){
        $query = $this->ch->query("SELECT *FROM ". $this->schema_cm .".detallepaciente WHERE idUsuario = ?", $idUsuario);
        
        return $query;
    }

    function getDocumento($idEspecialidad)
	{
		$query = $this->ch->query("SELECT hd.*, opc.nombre as nombreDocumento, opc2.nombre as nombreEspecialidad
		 FROM ". $this->schema_cm .".historialdocumento hd
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo opc ON opc.idOpcion = hd.tipoDocumento AND opc.idCatalogo = 11
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo opc2 ON opc2.idOpcion = hd.tipoEspecialidad AND opc2.idCatalogo = 1
		 WHERE hd.estatus=1 AND
		 hd.tipoDocumento = 1 AND hd.tipoEspecialidad = $idEspecialidad order by fechaModificacion desc");
		return $query;
	}

    public function getSedeEsp($idEsp){
        $query = $this->ch->query("SELECT us2.idsede FROM ". $this->schema_cm .".usuarios us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
        WHERE us.idUsuario = $idEsp");
       return $query;
    }
}