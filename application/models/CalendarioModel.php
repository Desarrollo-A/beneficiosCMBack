<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CalendarioModel extends CI_Model
{
    public function getOccupied($year, $month, $id_usuario, $dates){
        $query = $this->db->query(
            "SELECT idUnico as id, titulo as title, concat(fechaOcupado, ' ', horaInicio) as 'start', concat(fechaOcupado, ' ', horaFinal) as 'end',
            fechaOcupado AS occupied, 'red' AS 'color'
            FROM horariosOcupados 
            WHERE YEAR(fechaOcupado) in(?, ?)
            AND MONTH(fechaOcupado) in (?, ?, ?)
            AND idEspecialista = ?  
            AND estatus = ?",
            array( $dates["year_1"], $dates["year_2"], $dates["month_1"], $month, $dates["month_2"], $id_usuario, 1 )
        );
        return $query;
    }

    public function getAppointment($year, $month, $id_usuario, $dates){
        $query = $this->db->query(
            "SELECT CAST(idCita AS VARCHAR(36))  AS id,  observaciones AS title, fechaInicio AS 'start', fechaFinal AS 'end', 
            fechaInicio AS occupied, 'green' AS 'color', 'cita' AS 'type'
            FROM citas
            WHERE YEAR(fechaInicio) = ?
            AND MONTH(fechaInicio) = ?
            AND idEspecialista = ?
            AND estatus = ?",
            array( $year, $month, $id_usuario, 1 )
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
}
