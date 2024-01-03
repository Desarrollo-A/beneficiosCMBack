<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CalendarioModel extends CI_Model
{

    public function getAppointmentsByUser($year, $month, $idUsuario){
        $query = $this->db->query(
            "SELECT CAST(idCita AS VARCHAR(36)) AS id, observaciones AS title, fechaInicio AS 'start', fechaFinal AS 'end', 
            fechaInicio AS occupied, estatusCita 
            FROM citas
            WHERE YEAR(fechaInicio) = ?
            AND MONTH(fechaInicio) = ?
            AND idPaciente = ?
            AND estatusCita = ?",
            array( $year, $month, $idUsuario, 1 )
        );

        return $query;
    }

    public function getOccupied($year, $month, $idUsuario, $dates){
        $query = $this->db->query(
            "SELECT idUnico as id, titulo as title, concat(fechaOcupado, ' ', horaInicio) as 'start', concat(fechaOcupado, ' ', horaFinal) as 'end',
            fechaOcupado AS occupied, 'purple' AS 'color', estatus, 'ocupado' AS 'type'
            FROM horariosOcupados 
            WHERE YEAR(fechaOcupado) in (?, ?)
            AND MONTH(fechaOcupado) in (?, ?, ?)
            AND idEspecialista = ?  
            AND estatus = ?",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
        );
        return $query;
    }

    public function getAppointment($year, $month, $idUsuario, $dates){
        $query = $this->db->query(
            "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
            ct.fechaInicio AS occupied, 'green' AS 'color', 'cita' AS 'type', ct.estatus, us.nombre, ct.idPaciente, 
            'color' = CASE
	            WHEN ct.estatus = 0 THEN 'red'
	            WHEN ct.estatus = 1 THEN 'green'
	            WHEN ct.estatus = 2 THEN 'red'
	            WHEN ct.estatus = 3 THEN 'grey'
	            WHEN ct.estatus = 4 THEN 'green'
                WHEN ct.estatus > 4 THEN 'pink'
	        END
            FROM citas ct
            INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
            WHERE YEAR(fechaInicio) in (?, ?)
            AND MONTH(fechaInicio) in (?, ?, ?)
            AND idEspecialista = ?
            AND ct.estatus IN(?, ?, ?, ?)",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4 )
        );

        return $query;
    }

    public function checkOccupied($dataValue, $hora_inicio_suma, $hora_final_resta){
        $query = $this->db->query(
            "SELECT *FROM horariosOcupados WHERE 
            ((fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
            OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
            OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
            OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal))
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $dataValue["fecha"], $hora_inicio_suma, $hora_final_resta,
                $dataValue["fecha"], $hora_inicio_suma, $hora_final_resta,
                $dataValue["fecha"], $hora_inicio_suma,
                $dataValue["fecha"], $hora_final_resta,
                $dataValue["id_usuario"],
                1
            )
        );

        return $query;
    }

    public function checkOccupiedId($dataValue, $hora_inicio_suma ,$hora_final_resta){
        $query = $this->db->query(
            "SELECT *FROM horariosOcupados WHERE 
                            ((fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                            OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                            OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                            OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal))
                            AND idUnico != ?
                            AND idEspecialista = ?
                            AND estatus = ?",
            array(
                $dataValue["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta,
                $dataValue["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta,
                $dataValue["fecha_ocupado"], $hora_inicio_suma,
                $dataValue["fecha_ocupado"], $hora_final_resta,
                $dataValue["id"],
                $dataValue["id_usuario"],
                1
            )
        );

        return $query;
    }

    public function checkAppointment($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND ((idPaciente = ?
            AND estatus = ?)
            OR (idEspecialista = ? and estatus IN (1)))",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id_paciente"],
                1,
                $dataValue["id_usuario"]
            )
        );
        
        return $query;
    }

    public function checkAppointmentNormal($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id_usuario"],
                1
            )
        );
        
        return $query;
    }

    public function checkAppointmentId($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->db->query(
            "SELECT *FROM citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idCita != ?
            AND ((idPaciente = ?
            AND estatus = ?)
            OR (idEspecialista = ? AND estatus IN(?)))",
        array(
            $fecha_inicio_suma, $fecha_final_resta,
            $fecha_inicio_suma, $fecha_final_resta,
            $fecha_inicio_suma,
            $fecha_final_resta,
            $dataValue["id"],
            $dataValue["id_paciente"],
            1,
            $dataValue["id_usuario"],
            1
        )
    );

    return $query;
    }

    public function getIdAtencion($dataValue){
        $query = $this->db->query(
            "SELECT idAtencionXSede FROM atencionXSede 
            WHERE idEspecialista = ?
            AND idSede = ( SELECT sede FROM usuarios WHERE idUsuario = ? ) AND estatus = ?", 
            array($dataValue["id_usuario"], $dataValue["id_usuario"], 1)
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

    // **********************************************************

    
    public function getBeneficiosPorSede($sede)
	{
        $query = $this->db->query(
            "SELECT DISTINCT u.puesto as id, p.puesto
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.sede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            and axs.idSede = ?", $sede
        );

        return $query;
	}

    public function getEspecialistaPorBeneficioYSede($sede, $beneficio)
    {
        $query = $this->db->query(
            "SELECT DISTINCT u.idUsuario as id, u.nombre AS especialista
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.sede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            AND axs.idSede = ? AND u.puesto = ?", array($sede, $beneficio)
        );

        return $query;
    }

    public function getModalidadesEspecialista($sede, $especialista)
    {
        $query = $this->db->query(
            "SELECT u.idUsuario as id, u.puesto as idPuesto, p.puesto, u.nombre AS especilista,
            axs.idAtencionXSede, axs.idSede AS idSedeAtiende, se.sede as lugarAtiende, axs.idOficina as oficinaAtiende, 
            axs.tipoCita, oxc.nombre AS modalidad, o.ubicación as ubicacionOficina
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.sede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            AND axs.idSede = ? AND u.idUsuario = ?", array($sede, $especialista));

        return $query;
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
}