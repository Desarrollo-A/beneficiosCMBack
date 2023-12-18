<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CalendarioModel extends CI_Model
{
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

    public function getAppointmentsByUser($year, $month, $id_usuario){
        $query = $this->db->query(
            "SELECT CAST(idCita AS VARCHAR(36)) AS id, observaciones AS title, fechaInicio AS 'start', fechaFinal AS 'end', 
            fechaInicio AS occupied, estatus 
            FROM citas
            WHERE YEAR(fechaInicio) = ?
            AND MONTH(fechaInicio) = ?
            AND idPaciente = ?
            AND estatus = ?",
            array( $year, $month, $id_usuario, 1 )
        );

        return $query;
    }

    public function getOccupied($year, $month, $id_usuario)
    {
        $month_1 = ($month - 1) === 0 ? 12 : ($month - 1);
        $month_2 = ($month + 1) > 12 ? 1 : ($month + 1);
        
        $year_1 =  intval($month) === 1 ? $year - 1 : $year;
        $year_2 =  intval($month) === 12 ? $year + 1 : $year;
        
        $query = $this->db->query(
            "SELECT 
                idUnico as id, 
                titulo as title,
                concat(fechaOcupado, ' ', horaInicio) as 'start',
                concat(fechaOcupado, ' ', horaFinal) as 'end',
                fechaOcupado AS occupied,
                'red' AS 'color'
                    FROM 
                        horariosOcupados
                    WHERE
                        YEAR(fechaOcupado) in(?, ?)
                    AND
                        MONTH(fechaOcupado) in (?, ?, ?)
                    AND
                        idEspecialista = ?
                    AND 
                        estatus = ?",
            array(
                $year_1,
                $year_2,
                $month_1,
                $month,
                $month_2,
                $id_usuario,
                1
            )
        );

        $query_citas = $this->db->query(
            "SELECT
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
                        estatus = ?",
            array(
                $year,
                $month,
                $id_usuario,
                1
            )
        );

        if ($query->num_rows() > 0 || $query_citas->num_rows() > 0) {
            $data["events"] = array_merge($query->result(), $query_citas->result());
        } else {
            $data["events"] = array('');
        }

        return $data;
    }

    public function saveOccupied($data)
    {
        $hora_final_resta = date('H:i:s', strtotime($data["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["hora_inicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($data["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($data["fecha_inicio"] . '+1 minute'));

        try {
            $check_occupied = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE 
                (
                    (fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                    OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                    OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                    OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal)
                )
                    AND idEspecialista = ?
                    AND estatus = ?",
                array(
                    $data["fecha"], $hora_inicio_suma, $hora_final_resta,
                    $data["fecha"], $hora_inicio_suma, $hora_final_resta,
                    $data["fecha"], $hora_inicio_suma,
                    $data["fecha"], $hora_final_resta,
                    $data["id_usuario"],
                    1
                )
            );

            $check_appointment = $this->db->query(
                "SELECT *FROM citas WHERE
                (
                    (fechaInicio BETWEEN ? AND ?)
                    OR (fechaFinal BETWEEN ? AND ?)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                )
                    AND idEspecialista = ?
                    AND estatus = ?",
                array(
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma,
                    $fecha_final_resta,
                    $data["id_especialista"],
                    1
                )
            );

            if ($check_occupied->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } else {
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
            $data["message"] = "Error al guardar eh horario";
        }

        return $data;
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

    public function updateOccupied($data)
    {
        $hora_final_resta = date('H:i:s', strtotime($data["hora_final"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["hora_inicio"] . '+1 minute'));

        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($data["fecha_final"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($data["fecha_inicio"] . '+1 minute'));

        try {
            $check_occupied = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE 
                                ((fechaOcupado = ? AND horaInicio BETWEEN ? AND ?) 
                                OR (fechaOcupado = ? AND horaFinal BETWEEN ? AND ?)
                                OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal) 
                                OR (fechaOcupado = ? AND ? BETWEEN horaInicio AND horaFinal))
                                AND idUnico != ?
                                AND idEspecialista = ?
                                AND estatus = ?",
                array(
                    $data["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta,
                    $data["fecha_ocupado"], $hora_inicio_suma, $hora_final_resta,
                    $data["fecha_ocupado"], $hora_inicio_suma,
                    $data["fecha_ocupado"], $hora_final_resta,
                    $data["id_unico"],
                    $data["id_usuario"],
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
                    AND estatus = ?",
                array(
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma,
                    $fecha_final_resta,
                    $data["id_especialista"],
                    1
                )
            );
            if ($check_occupied->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } else {
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

    public function deleteOccupied($id_unico)
    {
        $this->db->query("UPDATE horariosOcupados SET estatus = ? where idUnico = ?", array(0, $id_unico));

        if ($this->db->affected_rows() > 0) {
            $data["status"] = true;
            $data["message"] = "Se ha eliminado el horario";
        } else {
            $data["status"] = false;
            $data["message"] = "No se puede eliminar el horario";
        }

        return $data;
    }

    public function deleteDate($id)
    {
        $query = $this->db->query("UPDATE citas SET estatus = 0 WHERE idCita = ? ", $id);

        if ($this->db->affected_rows() > 0) {
            $data["status"] = true;
            $data["message"] = "Se ha cancelado la cita";
        } else {
            $data["status"] = false;
            $data["message"] = "No se ha cancelado la cita";
        }

        return $data;
    }
    
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
            "SELECT u.idUsuario as id, u.nombre AS especialista
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

    public function onDropAppointment($data)
    {
        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        $hora_final_resta = date('H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        $fechaOcupado = date('Y/m/d', strtotime($data["fechaInicio"]));

        $fecha_final = date('Y/m/d H:i:s', strtotime($data["fechaFinal"]));
        $fecha_inicio = date('Y/m/d H:i:s', strtotime($data["fechaInicio"]));

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
                    $fechaOcupado, $hora_inicio_suma, $hora_final_resta,
                    $fechaOcupado, $hora_inicio_suma, $hora_final_resta,
                    $fechaOcupado, $hora_inicio_suma,
                    $fechaOcupado, $hora_final_resta,
                    $data["idEspecialista"],
                    1
                )
            );

            $check_appointment = $this->db->query(
                "SELECT *FROM citas WHERE
                (
                    (fechaInicio BETWEEN ? AND ?)
                    OR (fechaFinal BETWEEN ? AND ?)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                )
                    AND idEspecialista = ?
                    AND estatus = 1",
                array(
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma,
                    $fecha_final_resta,
                    $data["idEspecialista"],
                )
            );

            if ($check_appointment->num_rows() > 0 || $check_occupied->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } 
            else {
                $this->db->query(
                    "UPDATE 
                        citas 
                        SET 
                            fechaInicio = ?,
                            fechaFinal = ? 
                        WHERE
                            idCita = ?",
                    array(
                        $fecha_inicio,
                        $fecha_final,
                        $data["id"]
                    )
                );

                if ($this->db->affected_rows() > 0) {
                    $data["status"] = true;
                    $data["message"] = "Se ha guardado el horario";
                } else {
                    $data["status"] = false;
                    $data["message"] = "No se ha guardado el horario";
                }
            }
        }
        catch (Exception $e) {
            $data["status"] = false;
            $data["message"] = "Error al guardar";
        }

        return $data;
    }

    public function onDropOccupied($data)
    {
        $hora_final_resta = date('H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $hora_inicio_suma = date('H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        $hora_final = date('H:i:s', strtotime($data["fechaFinal"]));
        $hora_inicio = date('H:i:s', strtotime($data["fechaInicio"]));
        $fecha = date('Y/m/d ', strtotime($data["fechaInicio"]));
        
        $fecha_final_resta = date('Y/m/d H:i:s', strtotime($data["fechaFinal"] . '-1 minute'));
        $fecha_inicio_suma = date('Y/m/d H:i:s', strtotime($data["fechaInicio"] . '+1 minute'));

        try {
            $check_occupied = $this->db->query(
                "SELECT *FROM horariosOcupados WHERE 
                (
                    (horaInicio BETWEEN ? AND ?)
                    OR (horaFinal BETWEEN ? AND ?)
                    OR (? BETWEEN horaInicio AND horaFinal) 
                    OR (? BETWEEN horaInicio AND horaFinal)
                )
                    AND fechaOcupado = ?
                    AND idEspecialista = ?
                    AND estatus = ?",
                array(
                    $hora_inicio_suma, $hora_final_resta,
                    $hora_inicio_suma, $hora_final_resta,
                    $hora_inicio_suma,
                    $hora_final_resta,
                    $fecha,
                    $data["idEspecialista"],
                    1
                )
            );

            $check_appointment = $this->db->query(
                "SELECT *FROM citas WHERE
                (
                    (fechaInicio BETWEEN ? AND ?)
                    OR (fechaFinal BETWEEN ? AND ?)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                    OR (? BETWEEN fechaInicio AND fechaFinal)
                )
                    AND idEspecialista = ?
                    AND estatus = 1",
                array(
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma, $fecha_final_resta,
                    $fecha_inicio_suma,
                    $fecha_final_resta,
                    $data["idEspecialista"],
                )
            );

            if ($check_occupied->num_rows() > 0 || $check_appointment->num_rows() > 0) {
                $data["status"] = false;
                $data["message"] = "El horario ya ha sido ocupado";
            } else {
                $this->db->query(
                    "UPDATE 
                                    horariosOcupados 
                                        SET 
                                            fechaOcupado = ?,
                                            horaInicio = ?,
                                            horaFinal = ? 
                                        WHERE
                                            idUnico = ?
                                        ",
                    array(
                        $fecha,
                        $hora_inicio,
                        $hora_final,
                        $data["id"]
                    )
                );

                if ($this->db->affected_rows() > 0) {
                    $data["status"] = true;
                    $data["message"] = "Se ha guardado el horario";
                } else {
                    $data["status"] = false;
                    $data["message"] = "No se ha guardado el horario";
                }
            }
        } catch (Exception $e) {
            $data["status"] = false;
            $data["message"] = "Error al guardar";
        }

        return $data;
    }
}