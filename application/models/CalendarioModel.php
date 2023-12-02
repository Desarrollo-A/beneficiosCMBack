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

    public function saveOccupied($data) {
        $hora_final_resta = date('H:i:s', strtotime($data["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["hora_inicio"] . '+1 minute'));
    
        try {
            $check = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE (fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                                                  OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                                                  OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                                                  OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal)",
                array(
                    $data["fecha"], $hora_inicio_suma, $hora_final_resta, 
                    $data["fecha"], $hora_inicio_suma, $hora_final_resta, 
                    $data["fecha"], $hora_inicio_suma, 
                    $data["fecha"], $hora_final_resta
                    )
                );
            
            if ($check->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } 
            else {
                $query = $this->db->query(
                    "INSERT 
                        INTO 
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
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    array(
                        $data["fecha"], 
                        $data["hora_inicio"], 
                        $data["hora_final"], 
                        $data["id_usuario"], 
                        $data["id_usuario"], 
                        date("Y-m-d H:i:s"), 
                        date("Y-m-d H:i:s"), 
                        $data["titulo"], 
                        $data["id_unico"]
                        )
                );
    
                if ($this->db->affected_rows() > 0) {
                    $data["status"] = true;
                    $data["message"] = "Se ha guardado el horario";
                } else {
                    $data["status"] = false;
                    $data["message"] = "Error al guardar el horario";
                }
            }
        } catch (Exception $e) {
            $data["status"] = false;
            $data["message"] = "Error en la consulta: " . $e->getMessage();
        }
    
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


    public function updateOccupied($data){
        $hora_final_resta = date('H:i:s', strtotime($data["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["hora_inicio"] . '+1 minute'));

        try{
            $check = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE 
                                                  ((fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                                                  OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                                                  OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                                                  OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal))
                                                  AND idUnico != ?",
                array($data["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta, 
                      $data["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta, 
                      $data["fecha_ocupado"], $hora_inicio_suma, 
                      $data["fecha_ocupado"], $hora_final_resta,
                      $data["id_unico"])
                                    );
            if($check->num_rows() > 0){
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            }
            else{
                $query = $this->db->query(
                    "UPDATE
                        horariosOcupados
                        SET
                            horaInicio = ?, 
                            horaFinal = ?, 
                            fechaModificacion = ?, 
                            titulo = ?,
                            fechaOcupado = ?
                        WHERE
                            idUnico = ?", 
                        array(
                            $data["hora_inicio"], $data["hora_final"], date("Y-m-d H:i:s"), $data["titulo"], $data["fecha_ocupado"], $data["id_unico"]
                        )
                    );
        
                if($this->db->affected_rows() > 0){
                    $data["status"] = true;
                    $data["message"] = "Se ha guardado el horario";
                }
                else{
                    $data["status"] = false;
                    $data["message"] = "Error al guardar el horario";
                }
            }
        }
        catch(Exception $e){
            $data["status"] = false;
            $data["message"] = "Error en la consulta: " . $e->getMessage();
        }

        return $data;
    }

    public function deleteOccupied($id_unico){
        $this->db->query("DELETE FROM horariosOcupados where idUnico = ?", $id_unico);

        if($this->db->affected_rows() > 0){
            $data["status"] = true;
            $data["message"] = "Se ha eliminado el horario";
        }
        else{
            $data["status"] = false;
            $data["message"] = "No se puede eliminar el horario";
        }   

        return $data;
    }

    public function deleteDate($id){
        $query = $this->db->query("UPDATE citas SET estatus = 0 WHERE idCita = ? ", $id);

        if($this->db->affected_rows() > 0){
            $data["status"] = true;
            $data["message"] = "Se ha cancelado la cita";
        }
        else{
            $data["status"] = false;
            $data["message"] = "No se ha cancelado la cita";
        }

        return $data;
    }

    public function createAppointment($data){
        $this->db->query("INSERT INTO citas VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)", 
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
        ));

        if($this->db->affected_rows() > 0){
            $data["status"] = true;
            $data["message"] = "Se ha agendado a cita";
        }   
        else{
            $data["status"] = false;
            $data["message"] = "No se ha guardado el horario";
        }

        return $data;
    }
    
}