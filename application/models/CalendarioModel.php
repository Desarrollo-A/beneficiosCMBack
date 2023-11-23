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

	public function getBeneficiosDisponibles($id_usuario)
	{
		$query = $this->db-> query("
		SELECT * FROM opcionesPorCatalogo opc WHERE opc.idOpcion NOT IN(
			SELECT opc.idOpcion FROM opcionesPorCatalogo opc
			INNER JOIN usuarios u ON u.idArea = opc.idOpcion
			JOIN citas ct ON u.idUsuario = ct.idEspecialista
			WHERE opc.idCatalogo=1 AND ct.idPaciente=$id_usuario ) 
			AND idCatalogo=1");
		return $query->result_array();
	}

	function revisaCitas(){
		$id_usuario = $this->session->userdata('id_usuario');
		$fecha_actual_inicio = date('Y/m/01 00:00:00');
		$fecha_actual_final = date('Y/m/t 23:59:59');
		$query = $this->db->query("SELECT * FROM citas WHERE (fechaInicio >= '".$fecha_actual_inicio."' AND fechaInicio <= '".$fecha_actual_final ."')
		AND (fechaFinal >= '".$fecha_actual_inicio."' AND fechaFinal <= '".$fecha_actual_final ."') AND idPaciente=$id_usuario AND estatus IN(1,4)");
		return $query->result_array();
	}


}
