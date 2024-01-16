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
            "SELECT * FROM horariosOcupados
            WHERE idEspecialista = ? AND estatus = ?  AND
            (fechaInicio BETWEEN ? AND ?
               OR fechaFinal BETWEEN ? AND ?
               OR (fechaInicio < ? AND fechaFinal > ?));",
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
            ct.fechaInicio AS occupied, 'green' AS 'color', 'date' AS 'type', ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal,
            'color' = CASE
	            WHEN ct.estatusCita = 0 THEN 'red'
	            WHEN ct.estatusCita = 1 THEN 'yellow'
	            WHEN ct.estatusCita = 2 THEN 'red'
	            WHEN ct.estatusCita = 3 THEN 'grey'
	            WHEN ct.estatusCita = 4 THEN 'green'
                WHEN ct.estatusCita = 5 THEN 'pink'
                WHEN ct.estatusCita = 6 THEN 'blue'
                WHEN ct.estatusCita = 7 THEN 'red'
	        END
            FROM citas ct
            INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
            WHERE YEAR(fechaInicio) in (?, ?)
            AND MONTH(fechaInicio) in (?, ?, ?)
            AND idEspecialista = ?
            AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4, 5, 6, 7 )
        );

        return $query;
    }

    public function getAppointmentRange($fechaInicio, $fechaFin, $idUsuario){
        $query = $this->db->query(
            "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio, ct.fechaFinal, 
            ct.fechaInicio AS occupied, 'green' AS 'color', 'date' AS 'type', ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal,
            'color' = CASE
	            WHEN ct.estatusCita = 0 THEN 'red'
	            WHEN ct.estatusCita = 1 THEN 'green'
	            WHEN ct.estatusCita = 2 THEN 'red'
	            WHEN ct.estatusCita = 3 THEN 'grey'
	            WHEN ct.estatusCita = 4 THEN 'green'
                WHEN ct.estatusCita > 4 THEN 'pink'
	        END
            FROM citas ct
            INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
            WHERE idEspecialista = ? AND ct.estatusCita IN (?) 
            AND (fechaInicio BETWEEN ? AND ?
               OR fechaFinal BETWEEN ? AND ?
               OR (fechaInicio < ? AND fechaFinal > ?))",
            array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
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
            AND estatusCita = ?)
            OR (idEspecialista = ? and estatusCita IN (?, ?)))",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idPaciente"],
                1,
                $dataValue["idUsuario"],
                1,
                6
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

    // **********************************************************

    
    public function getBeneficiosPorSede($sede)
	{
        $query = $this->db->query(
            "SELECT DISTINCT u.puesto as id, p.puesto
            FROM usuarios AS u 
            RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
            INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
            INNER JOIN sedes AS S ON S.idSede = U.idSede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
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
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) AND u.puesto = ?;", array($sede, $area, $beneficio)
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
            INNER JOIN sedes AS S ON S.idSede = U.idSede
            LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
            INNER JOIN puestos AS p ON p.idPuesto = u.puesto
            FULL JOIN sedes AS se ON se.idSede = o.idSede
            WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
            AND axs.idSede = ? AND u.idUsuario = ?", array($sede, $especialista));

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

    public function getCitasSinFinalizarUsuario($usuario)
    {
        $query = $this->db->query(
            "SELECT *FROM citas
            WHERE idPaciente = ? AND estatusCita NOT IN (2, 5);", array($usuario)
        );

        return $query;
    }

    public function getCitasFinalizadasUsuario($usuario, $mes, $año)
    {
        $query = $this->db->query(
            "SELECT *FROM citas
            WHERE idPaciente = ? AND MONTH(fechaInicio) = ?
            AND YEAR(fechaInicio) = ? AND estatusCita IN (4);", array($usuario, $mes, $año)
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
        $query = $this->db->query("SELECT *FROM citas WHERE estatusCita = ? AND idEspecialista = ? AND fechaInicio < GETDATE()", array(1, $idUsuario));

        return $query->result();
    }

    

}