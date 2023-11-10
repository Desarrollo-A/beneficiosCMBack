<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class calendarioModel extends CI_Model{

    public function getOccupied(){
        $query = $this->db->query('SELECT *, titulo as title FROM horariosOcupados WHERE YEAR(fechaOcupado) = 2023 AND MONTH(fechaOcupado) = 11');

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

	public function getBeneficiosDisponibles()
	{
		$query = $this->db-> query("SELECT *  FROM usuarios");
		return $query->result_array();
	}
}
