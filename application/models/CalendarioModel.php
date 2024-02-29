<?php
defined('BASEPATH') or exit('No direct script access allowed');

class calendarioModel extends CI_Model
{
    // Mostrarlos en calendario
    public function getAppointmentsByUser($year, $month, $idUsuario){
        $query = $this->db->query(
            "SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
            ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, us.nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
            ct.tipoCita, atc.tipoCita as modalidad, atc.idSede ,usEspe.idPuesto, us.telPersonal, usEspe.telPersonal as telefonoEspecialista, 
            CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina', CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación',
            pue.idArea, sed.sede, atc.idOficina, us.correo, usEspe.correo as correoEspecialista, usEspe.nombre as especialista, 
            usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
            'color' = CASE
                WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500'
	              WHEN ct.estatusCita = 2 THEN '#ff0000'
	              WHEN ct.estatusCita = 3 THEN '#808080'
	              WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff'
	        END,
            beneficio = CASE 
            WHEN pue.idPuesto = 537 THEN 'nutrición'
            WHEN pue.idPuesto = 585 THEN 'psicología'
            WHEN pue.idPuesto = 686 THEN 'guía espiritual'
            WHEN pue.idPuesto = 158 THEN 'quantum balance'
            END
            FROM citas ct
            INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
            INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
            LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
            INNER join sedes sed ON sed.idSede = atc.idSede
            INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
            LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
                        FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
            WHERE YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ? AND ct.idPaciente = ?
            AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
            array( $year, $month, $idUsuario, 1, 2, 3, 4, 5, 6, 7)
        );

        return $query;
    }

    public function getOccupied($year, $month, $idUsuario, $dates){
        $query = $this->db->query(
            "SELECT idUnico as id, titulo as title, fechaInicio as 'start', fechaFinal as 'end',
            'purple' AS 'color', estatus, 'cancel' AS 'type'
            FROM horariosOcupados 
            WHERE YEAR(fechaInicio) in (?, ?)
            AND MONTH(fechaInicio) in (?, ?, ?)
            AND idEspecialista = ?  
            AND estatus = ?",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
        );
        return $query;
    }

    public function getOccupiedRange($fechaInicio, $fechaFin, $idUsuario){
        $query = $this->db->query(
            "SELECT idOcupado as id, titulo as title, fechaInicio as occupied, fechaInicio, fechaFinal FROM horariosOcupados
            WHERE idEspecialista = ? AND estatus = ?  AND
            ((fechaInicio BETWEEN ? AND ?) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?));",
            array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );
        return $query;
    }

    public function getHorarioBeneficio($beneficio){
        $query = $this->db->query(
            "SELECT *FROM horariosPorBeneficio WHERE idBeneficio = ?",
            array($beneficio)
        );
        return $query;
    }

    public function getAppointment($year, $month, $idUsuario, $dates){
        $query = $this->db->query(
            "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
            ct.fechaInicio AS occupied, 'date' AS 'type', ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, us.correo,
            se.sede, ofi.oficina, ct.idDetalle, ct.idAtencionXSede, us.externo, usEspe.nombre as especialista, ct.fechaCreacion, pue.tipoPuesto,
            tf.fechasFolio, idEventoGoogle, ct.tipoCita, aps.tipoCita as modalidad, aps.idSede,
            'color' = CASE
	            WHEN ct.estatusCita = 0 THEN '#ff0000'
	            WHEN ct.estatusCita = 1 AND aps.tipoCita = 1 THEN '#ffa500'
	            WHEN ct.estatusCita = 2 THEN '#ff0000'
	            WHEN ct.estatusCita = 3 THEN '#808080'
	            WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND aps.tipoCita = 2 THEN '#0000ff'
	        END,
            beneficio = CASE 
            WHEN pue.idPuesto = 537 THEN 'nutrición'
            WHEN pue.idPuesto = 585 THEN 'psicología'
            WHEN pue.idPuesto = 686 THEN 'guía espiritual'
            WHEN pue.idPuesto = 158 THEN 'quantum balance'
            END
            FROM citas ct
            FULL JOIN usuarios us ON us.idUsuario = ct.idPaciente
            FULL JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
            FULL JOIN atencionXSede aps ON ct.idAtencionXSede = aps.idAtencionXSede
            FULL JOIN sedes se ON se.idSede = aps.idSede
            FULL JOIN oficinas ofi ON ofi.idOficina = aps.idOficina
            FULL JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
            FULL JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio FROM citas WHERE estatusCita IN(?) AND citas.idCita = idCita GROUP BY citas.idDetalle) tf
            ON tf.idDetalle = ct.idDetalle
            WHERE YEAR(fechaInicio) in (?, ?)
            AND MONTH(fechaInicio) in (?, ?, ?)
            AND ct.idEspecialista = ?
            AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
            array( 8, $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4, 5, 6, 7 )
        );

        return $query;
    }

    // Función para checar las citas de ambos (Beneficiario y especialista)
    public function getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario){
        $query = $this->db->query(
            "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio, ct.fechaFinal, 
            ct.estatusCita, ct.idPaciente, ct.idEspecialista d
            FROM citas ct
            LEFT JOIN usuarios us ON us.idUsuario = ct.idPaciente
            WHERE (ct.idEspecialista = ? OR ct.idPaciente = ?) AND ct.estatusCita IN (?, ?)
            AND ((fechaInicio BETWEEN ? AND ? ) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?))",
            array( $especialista, $usuario, 1, 6, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );

        return $query;
    }

    public function checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->db->query(
            "SELECT *FROM horariosOcupados WHERE 
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

    public function checkPresencial($idSede, $idEspecialista, $modalidad, $fecha){
        $query = $this->db->query(
            "SELECT *from presencialXSede as pxs
            WHERE pxs.idSede = ? AND pxs.idEspecialista = ? AND presencialDate = ?;",
            array( $idSede, $idEspecialista, $fecha)
        );

        return $query;
    }

    public function checkOccupiedId($dataValue, $fechaInicioSuma ,$fechaFinalResta){
        $query = $this->db->query(
            "SELECT *FROM horariosOcupados WHERE 
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

    public function checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND ((idPaciente = ?
            AND estatusCita IN (?, ?))
            OR (idEspecialista = ? and estatusCita IN (?, ?)))",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $dataValue["idPaciente"],
                1, 6,
                $dataValue["idUsuario"],
                1, 6
            )
        );
        
        return $query;
    }

    public function checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idEspecialista = ? AND estatusCita IN(?, ?)",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idUsuario"],
                1,
                6
            )
        );
        
        return $query;
    }

    public function checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idCita != ?
            AND ((idPaciente = ?
            AND estatusCita = ?)
            OR (idEspecialista = ? AND estatusCita IN(?, ?)))",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id"],
                $dataValue["idPaciente"],
                1,
                $dataValue["idUsuario"],
                1,
                6
            )
        );

        return $query;
    }

    public function getIdAtencion($dataValue){
        $query = $this->db->query(
            "SELECT idAtencionXSede FROM atencionXSede 
            WHERE idEspecialista = ?
            AND idSede = ( SELECT idSede FROM usuarios WHERE idUsuario = ? ) AND estatus = ?", 
            array($dataValue["idUsuario"], $dataValue["idUsuario"], 1)
        );
        
        return $query;
    }

    public function getBeneficiosDisponibles()
    {
        $query = $this->db->query("
		SELECT * FROM opcionesPorCatalogo opc WHERE opc.idOpcion NOT IN(
			SELECT opc.idOpcion FROM opcionesPorCatalogo opc
			INNER JOIN usuarios u ON u.idArea = opc.idOpcion
			JOIN citas ct ON u.idUsuario = ct.idEspecialista
			WHERE opc.idCatalogo=1) 
		AND idCatalogo=1");
        return $query->result_array();
    }
    
    public function getBeneficiosPorSede($sede)
	{
        $query = $this->db->query(
            "SELECT DISTINCT u.idPuesto, p.puesto
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.idSede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            and axs.idSede = ?", $sede
        );

        return $query;
	}

    public function getEspecialistaPorBeneficioYSede($sede, $area, $beneficio)
    {
        $query = $this->db->query(
            "SELECT DISTINCT u.idUsuario as id, u.nombre AS especialista
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.idSede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) AND u.idPuesto = ?;", array($sede, $area, $beneficio)
        );

        return $query;
    }

    public function getModalidadesEspecialista($sede, $especialista, $area)
    {
        $query = $this->db->query(
            "SELECT modalidad = CASE WHEN tipoCita = 1 then 'PRESENCIAL' WHEN tipoCita = 2 THEN 'EN LíNEA' END,
            us.idUsuario as id, us.idPuesto, us.nombre AS especialista, o.ubicación as ubicacionOficina, axs.tipoCita, axs.idAtencionXSede, se.sede as lugarAtiende
            FROM atencionXSede axs
            INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
            LEFT JOIN oficinas o ON o.idoficina = axs.idOficina
            INNER JOIN sedes se ON se.idSede = us.idSede
            WHERE axs.estatus = ? AND axs.idSede = ? AND ((idEspecialista = ? AND idArea is NULL ) OR (idEspecialista = ? AND idArea = ?))", 
            array(1, $sede, $especialista, $especialista, $area));

        return $query;
    }

    public function getReasons($puesto){
        $query = $this->db->query("SELECT *from opcionesPorCatalogo where idCatalogo = ?", $puesto);

        return $query->result();
    }
    public function getOficinaByAtencion($sede, $beneficio, $especialista, $modalidad)
    {
        $query = $this->db->query(
            "SELECT axs.idAtencionXSede, axs.idEspecialista, axs.idSede, axs.tipoCita,  axs.estatus,
            ofi.idOficina, ofi.oficina, ofi.ubicación
            from atencionXSede AS axs
            INNER JOIN oficinas AS ofi ON axs.idOficina = ofi.idOficina
            WHERE axs.estatus = 1 AND
            axs.idSede = ? AND axs.idEspecialista = ? AND axs.tipoCita = ?", array($sede, $especialista, $modalidad)
        );

        return $query;
    }

    public function getHorariosDisponibles($sede, $beneficio, $especialista, $modalidad)
    {
        $query = $this->db->query(
            "SELECT axs.idAtencionXSede, axs.idEspecialista, axs.idSede, axs.tipoCita,  axs.estatus,
            ofi.idOficina, ofi.oficina, ofi.ubicación
            from atencionXSede AS axs
            INNER JOIN oficinas AS ofi ON axs.idOficina = ofi.idOficina
            WHERE axs.estatus = 1 AND
            axs.idSede = ? AND axs.idEspecialista = ? AND axs.tipoCita = ?", array($sede, $especialista, $modalidad)
        );

        return $query;
    }

    public function isPrimeraCita($usuario, $especialista)
    {
        $query = $this->db->query(
            "SELECT *FROM CITAS
            WHERE idPaciente = ? AND idEspecialista = ?;",
            array($usuario, $especialista)
        );

        return $query;
    }

    public function getCitasSinFinalizarUsuario($usuario, $beneficio)
    {
        $query = $this->db->query(
            "SELECT c.*, u.idPuesto FROM citas AS c
            INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
            WHERE c.idPaciente = ? AND u.idPuesto = ? AND c.estatusCita IN (1, 6);",array($usuario, $beneficio)
        );

        return $query;
    }

    public function getCitasSinEvaluarUsuario($usuario)
    {
        $query = $this->db->query(
            "SELECT c.*, u.idPuesto FROM citas AS c
            INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
            WHERE c.idPaciente = ? AND evaluacion is NULL AND c.estatusCita IN (?)",array($usuario, 4)
        );

        return $query;
    }

    public function getCitasSinPagarUsuario($usuario)
    {
        $query = $this->db->query(
            "SELECT c.*, u.idPuesto FROM citas AS c
            INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
            WHERE c.idPaciente = ? AND idDetalle is NULL AND c.estatusCita IN (?);",array($usuario, 6)
        );

        return $query;
    }

    public function getCitasFinalizadasUsuario($usuario, $mes, $año)
    {
        $query = $this->db->query(
            "SELECT *FROM citas
            WHERE idPaciente = ? AND MONTH(fechaInicio) = ?
            AND YEAR(fechaInicio) = ? AND estatusCita IN (4, 1) AND tipoCita IN (1, 2);", array($usuario, $mes, $año)
        );

        return $query;
    }

    public function getAtencionPorSede($especialista, $sede, $modalidad)
    {
        $query = $this->db->query(
            "SELECT *FROM atencionXSede 
            WHERE estatus = 1 AND idEspecialista = ? 
            AND idSede = ? AND tipoCita = ? ;", array($especialista, $sede, $modalidad)
        );

        return $query;
    }

    public function getPending($idUsuario){
        $query = $this->db->query("SELECT ct.idCita as id, ct.titulo, ct.fechaInicio as 'start', ct.fechaFinal as 'end', usEsp.nombre as especialista, usBen.correo, sed.sede, ofi.oficina,
        beneficio = CASE 
        WHEN pue.idPuesto = 537 THEN 'nutrición'
        WHEN pue.idPuesto = 585 THEN 'psicología'
        WHEN pue.idPuesto = 686 THEN 'guía espiritual'
        WHEN pue.idPuesto = 158 THEN 'quantum balance'
        END
        FROM citas ct
        INNER JOIN usuarios usBen ON usBen.idUsuario = ct.idPaciente
        INNER JOIN usuarios usEsp ON usEsp.idUsuario = ct.idEspecialista
        INNER JOIN puestos pue ON usEsp.idPuesto = pue.idPuesto
        INNER JOIN atencionXSede ats ON ats.idAtencionXSede = ct.idAtencionXSede
        INNER JOIN sedes sed ON sed.idSede = ats.idSede
        LEFT JOIN oficinas ofi ON ofi.idOficina = ats.idOficina
        WHERE estatusCita IN(?) AND ct.idEspecialista = ? AND fechaInicio < GETDATE()", array(1, $idUsuario));

        return $query;
    }

    public function getPendientesPago($idUsuario){
        $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina',
        CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación', sed.sede , atc.idOficina, us.correo, usEspe.correo as correoEspecialista, 
        usEspe.nombre as especialista, ct.idDetalle, usEspe.telPersonal as telefonoEspecialista,
        usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
        beneficio = CASE 
        WHEN pue.idPuesto = 537 THEN 'Nutrición'
        WHEN pue.idPuesto = 585 THEN 'Psicología'
        WHEN pue.idPuesto = 686 THEN 'Guía espiritual'
        WHEN pue.idPuesto = 158 THEN 'Quantum balance'
        END
        FROM citas ct
        INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
        INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
        LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
        INNER join sedes sed ON sed.idSede = atc.idSede
        INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
        LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
                        FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
        WHERE ct.estatusCita IN(?) AND ct.idPaciente = ?", array(6, $idUsuario));

        return $query;
    }

    public function getPendientesEvaluacion($idUsuario){
        $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina',
        CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación', sed.sede , atc.idOficina, us.correo, usEspe.correo as correoEspecialista, 
        usEspe.nombre as especialista, ct.idDetalle, usEspe.telPersonal as telefonoEspecialista,
        usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
        beneficio = CASE 
        WHEN pue.idPuesto = 537 THEN 'Nutrición'
        WHEN pue.idPuesto = 585 THEN 'Psicología'
        WHEN pue.idPuesto = 686 THEN 'Guía espiritual'
        WHEN pue.idPuesto = 158 THEN 'Quantum balance'
        END
        FROM citas ct
        INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
        INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
        LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
        INNER join sedes sed ON sed.idSede = atc.idSede
        INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
        LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
                        FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
        WHERE ct.estatusCita IN(?) AND ct.evaluacion is NULL AND ct.idPaciente = ?", array(4, $idUsuario));

        return $query;
    }

    public function getDetallePago($folio){
        $query = $this->db->query("SELECT * FROM detallePagos WHERE folio = ?", array($folio));

        return $query;
    }

    public function getEventReasons($idCita){
        $query = $this->db->query("SELECT oxc.idOpcion, oxc.nombre FROM motivosPorCita AS mpc
        INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion = mpc.idMotivo
        INNER JOIN citas AS c ON c.idCita = mpc.idCita
        INNER JOIN usuarios AS u ON u.idUsuario = c.idEspecialista
        INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto WHERE c.idCita = ?
        AND idCatalogo = 
            CASE P.idPuesto
                WHEN 537 THEN 8
                WHEN 585 THEN 7
                WHEN 802 THEN 7
                WHEN 859 THEN 7
                WHEN 686 THEN 9
                WHEN 158 THEN 6
            END",
            $idCita
        );

        return $query;
    }

    public function getLastAppointment($usuario, $beneficio) {
        $query = $this->db->query("SELECT TOP (1) ct.*, usu.idPuesto, axs.tipoCita FROM citas AS ct
        INNER JOIN usuarios AS usu ON usu.idUsuario = ct.idEspecialista
        INNER JOIN atencionXSede AS axs ON axs.idAtencionXSede = ct.idAtencionXSede
        WHERE ct.idPaciente = ? AND usu.idPuesto = ?
        ORDER BY idCita DESC", array($usuario, $beneficio));
    
        return $query;
    }
    
    public function checkInvoice($idDetalle){
        $query = $this->db->query("SELECT idDetalle FROM citas WHERE idDetalle = ? GROUP BY idDetalle HAVING COUNT(idDetalle) > ?", array($idDetalle, 2));

        return $query;
    }

    public function checkDetailPacient($user, $column){
        $query = $this->db->query("SELECT $column FROM detallePaciente 
            WHERE idUsuario = ?;", array($user));
   
        return $query;
    }

    public function getCitaById($idCita){
        $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, us.nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
        ct.tipoCita, atc.tipoCita as modalidad, atc.idSede ,usEspe.idPuesto, us.telPersonal, usEspe.telPersonal as telefonoEspecialista, 
        CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina', CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación',
        pue.idArea, sed.sede, atc.idOficina, us.correo, usEspe.correo as correoEspecialista, usEspe.nombre as especialista, usEspe.sexo as sexoEspecialista,
        tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
        'color' = CASE
                WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500'
	            WHEN ct.estatusCita = 2 THEN '#ff0000'
	            WHEN ct.estatusCita = 3 THEN '#808080'
	            WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff'
        END,
        beneficio = CASE 
            WHEN pue.idPuesto = 537 THEN 'nutrición'
            WHEN pue.idPuesto = 585 THEN 'psicología'
            WHEN pue.idPuesto = 686 THEN 'guía espiritual'
            WHEN pue.idPuesto = 158 THEN 'quantum balance'
        END
        FROM citas ct
        INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
        INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
        LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
        INNER join sedes sed ON sed.idSede = atc.idSede
        INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
        LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
                        FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
        WHERE idCita = ? ",
        array( $idCita ));

        return $query;
    }

    public function getSedesDeAtencionEspecialista($idUsuario){
        $query = $this->db->query(
        "SELECT ate.idSede as value, sedes.sede as label
        FROM atencionXSede ate
        LEFT JOIN sedes ON sedes.idSede=ate.idSede
        WHERE ate.idEspecialista=? AND ate.tipoCita=?", array($idUsuario, 1));

        return $query;
    }

    public function getDiasDisponiblesAtencionEspecialista($idUsuario, $idSede){
        $query = $this->db->query(
        "SELECT * FROM presencialXSede
        WHERE idEspecialista=? AND idSede=?
        AND MONTH(presencialDate) >= MONTH(CAST(GETDATE() AS DATE))
        AND MONTH(presencialDate) <= MONTH(DATEADD(MONTH, 1, CAST(GETDATE() AS DATE)));", array($idUsuario, $idSede));
    
        return $query;
    }
    
}