<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class calendarioModel extends CI_Model{

    public function getOccupied($year, $month){
        if($year != null){
            $query = $this->db->query("SELECT 
                                    idUnico as id, 
                                    titulo as title,
                                    concat(fechaOcupado, ' ', horaInicio) as 'start',
                                    concat(fechaOcupado, ' ', horaFinal) as 'end',
                                    fechaOcupado as occupied
                                        FROM 
                                            horariosOcupados
                                        WHERE
                                            YEAR(fechaOcupado) = ?
                                        AND
                                            MONTH(fechaOcupado) = ?", array($year, $month));
        }
        else{
            $query = $this->db->query("SELECT 
                                    idUnico as id, 
                                    titulo as title,
                                    concat(fechaOcupado, ' ', horaInicio) as 'start',
                                    concat(fechaOcupado, ' ', horaFinal) as 'end',
                                    fechaOcupado as occupied
                                        FROM 
                                            horariosOcupados");
        }
        
        
        if($query-> num_rows() > 0){
            $data["events"] = $query->result();
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

}