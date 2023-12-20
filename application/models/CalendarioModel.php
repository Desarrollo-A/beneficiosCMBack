<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CalendarioModel extends CI_Model
{

    public function getAppointmentsByUser($year, $month, $idUsuario){
        $query = $this->db->query(
            "SELECT CAST(idCita AS VARCHAR(36)) AS id, observaciones AS title, fechaInicio AS 'start', fechaFinal AS 'end', 
            fechaInicio AS occupied, estatus 
            FROM citas
            WHERE YEAR(fechaInicio) = ?
            AND MONTH(fechaInicio) = ?
            AND idPaciente = ?
            AND estatus = ?",
            array( $year, $month, $idUsuario, 1 )
        );

        return $query;
    }

    public function getOccupied($year, $month, $idUsuario, $dates){
        $query = $this->db->query(
            "SELECT idUnico as id, titulo as title, concat(fechaOcupado, ' ', horaInicio) as 'start', concat(fechaOcupado, ' ', horaFinal) as 'end',
            fechaOcupado AS occupied, 'red' AS 'color'
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
            "SELECT CAST(idCita AS VARCHAR(36))  AS id,  observaciones AS title, fechaInicio AS 'start', fechaFinal AS 'end', 
            fechaInicio AS occupied, 'green' AS 'color', 'cita' AS 'type'
            FROM citas
            WHERE YEAR(fechaInicio) in (?, ?)
            AND MONTH(fechaInicio) in (?, ?, ?)
            AND idEspecialista = ?
            AND estatus = ?",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
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

    public function checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->db->query(
                "SELECT *FROM citas WHERE
                ((fechaInicio BETWEEN ? AND ?)
                OR (fechaFinal BETWEEN ? AND ?)
                OR (? BETWEEN fechaInicio AND fechaFinal)
                OR (? BETWEEN fechaInicio AND fechaFinal))
                AND idEspecialista = ?
                AND idPaciente = ?
                AND estatus = ?",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id_usuario"],
                $dataValue["id_paciente"],
                1
            )
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

    function revisaCitas()
    {
        print_r($this->session->userdata('id_usuario'));
        exit;
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

    public function createAppointment($data)
    {
        $hora_final_resta = date('H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        try {
            $check_occupied = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE 
                                ((fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                                OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                                OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                                OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal))
                        AND idEspecialista = ?
                        AND estatus = ?",
                array(
                    $data["fechaOcupado"], $hora_inicio_suma, $hora_final_resta,
                    $data["fechaOcupado"], $hora_inicio_suma, $hora_final_resta,
                    $data["fechaOcupado"], $hora_inicio_suma,
                    $data["fechaOcupado"], $hora_final_resta,
                    $data["idEspecialista"],
                    1
                )
            );

            $check_appointment = $this->db->query(
                "SELECT *FROM citas WHERE
                                ((fechaInicio BETWEEN ? AND ?)
                                OR (fechaFinal BETWEEN ? AND ?)
                                OR (? BETWEEN fechaInicio AND fechaFinal)
                                OR (? BETWEEN fechaInicio AND fechaFinal))
                        AND idEspecialista = ?
                        AND idPaciente = ?
                        AND estatus = ?",
                array(
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma,
                    $fecha_final_resta,
                    $data["idEspecialista"],
                    $data["idPaciente"],
                    1
                )
            );

            if ($check_appointment->num_rows() > 0 || $check_occupied->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } else {
                $this->db->query(
                    "INSERT INTO citas VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    array(
                        $data["idEspecialista"],
                        $data["idPaciente"],
                        1,
                        $data["fechaInicio"],
                        $data["fechaFinal"],
                        $data["creadoPor"],
                        date("Y-m-d H:i:s"),
                        $data["observaciones"],
                        $data["modificadoPor"]
                    )
                );

                if ($this->db->affected_rows() > 0) {
                    $data["status"] = true;
                    $data["message"] = "Se ha agendado a cita";
                } else {
                    $data["status"] = false;
                    $data["message"] = "No se ha guardado la cita";
                }
            }
        } catch (Exception $e) {
            $data["status"] = false;
            $data["message"] = "Error al guardar la cita";
        }

        return $data;
    }
}