<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarioModel extends CI_Model{

    public function getOccupied($year, $month, $id_usuario){
            $query = $this->db->query("SELECT 
                                    idUnico as id, 
                                    titulo as title,
                                    concat(fechaOcupado, ' ', horaInicio) as 'start',
                                    concat(fechaOcupado, ' ', horaFinal) as 'end',
                                    fechaOcupado as occupied,
                                    'red' as 'color'
                                        FROM 
                                            horariosOcupados
                                        WHERE
                                            YEAR(fechaOcupado) = ?
                                        AND
                                            MONTH(fechaOcupado) = ?
                                        AND
                                            idEspecialista = ?", 
                                        array(
                                            $year, 
                                            $month,
                                            $id_usuario
                                        )
                                    );

            $query_citas = $this->db->query("SELECT
                                    CAST(idCita AS VARCHAR(36))  AS id, 
                                    observaciones AS title,
                                    fechaInicio AS 'start',
                                    fechaFinal AS 'end',
                                    fechaInicio AS occupied,
                                    'green' AS 'color',
                                    'cita' AS 'type'
                                        FROM 
                                            citas
                                        WHERE
                                            YEAR(fechaInicio) = ?
                                        AND
                                            MONTH(fechaInicio) = ?
                                        AND
                                            idEspecialista = ?
                                        AND 
                                            estatus = 1",
                                        array(
                                            $year, 
                                            $month,
                                            $id_usuario
                                        )
                                    );
                                    
                                         
        
        if($query-> num_rows() > 0 || $query_citas -> num_rows() > 0){
            $data["events"] = array_merge($query->result(), $query_citas->result());
        } 
        else{
            $data["events"] = array('');
        }

        return $data;
    }

    public function saveOccupied($fecha, $hora_inicio, $hora_final, $id_especialista, $creado_por, $fecha_modificacion, $fecha_creacion, $titulo, $id_unico){
        $query = $this->db->query(
            "INSERT INTO 
                horariosOcupados
                (
                    fechaOcupado, 
                    horaInicio, 
                    horaFinal, 
                    idEspecialista, 
                    creadoPor, 
                    fechaModificacion, 
                    fechaCreacion, 
                    titulo, 
                    idUnico
                ) 
                values (?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                array(
                    $fecha, $hora_inicio, $hora_final, $id_especialista, $creado_por, $fecha_modificacion, $fecha_creacion, $titulo, $id_unico
                )
            );

        if($this->db->affected_rows() > 0)
            $data["status"] = true;

        else
            $data["status"] = false;

        return $data;
    }

    public function getBeneficiosDisponibles()
	{
		$query = $this->db-> query("
		SELECT * FROM opcionesPorCatalogo opc WHERE opc.idOpcion NOT IN(
			SELECT opc.idOpcion FROM opcionesPorCatalogo opc
			INNER JOIN usuarios u ON u.idArea = opc.idOpcion
			JOIN citas ct ON u.idUsuario = ct.idEspecialista
			WHERE opc.idCatalogo=1) 
		AND idCatalogo=1");
		return $query->result_array();
	}

	function revisaCitas(){
		print_r($this->session->userdata('id_usuario'));
		exit;
	}


    public function updateOccupied($hora_inicio, $hora_final, $fecha_modificacion, $titulo, $id_unico){
        $query = $this->db->query(
            "UPDATE
                horariosOcupados
                SET
                    horaInicio = ?, 
                    horaFinal = ?, 
                    fechaModificacion = ?, 
                    titulo = ?
                WHERE
                    idUnico = ?", 
                array(
                    $hora_inicio, $hora_final, $fecha_modificacion, $titulo, $id_unico
                )
            );

        if($this->db->affected_rows() > 0)
            $data["status"] = true;

        else
            $data["status"] = false;

        return $data;
    }

    public function deleteOccupied($id_unico){
        $this->db->query("DELETE FROM horariosOcupados where idUnico = ?", $id_unico);

        if($this->db->affected_rows() > 0)
            $data["status"] = true;
        else
            $data["status"] = false;

        return $data;
    }

    public function deleteDate($id){
        $query = $this->db->query("UPDATE citas SET estatus = 0 WHERE idCita = ? ", $id);

        if($this->db->affected_rows() > 0)
            $data["status"] = true;
        else
            $data["status"] = false;

        return $data;
        
    }
}