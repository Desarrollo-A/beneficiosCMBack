<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class generalModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function usuarios()
	{
		$query = $this->db-> query("SELECT *  FROM usuarios");
		return $query->result();
	}

    public function usrCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [usuarios] FROM usuarios");
		return $query->result();
	}

    public function citasCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM citas");
		return $query->result();
	}

    public function especialistas()
	{
		$query = $this->db-> query("SELECT idPuesto, puesto AS nombre FROM puestos WHERE idPuesto = 537 OR idPuesto = 686 OR idPuesto = 158 OR idPuesto = 585");
		return $query->result();
	}

    // MJ: AGREGA UN REGISTRO A UNA TABLA EN PARTICULAR, RECIBE 2 PARÁMETROS. LA TABLA Y LA DATA A INSERTAR
    public function addRecord($table, $data) { 
        return $this->db->insert($table, $data);
    }

    // MJ: ACTUALIZA LA INFORMACIÓN DE UN REGISTRO EN PARTICULAR, RECIBE 4 PARÁMETROS. TABLA, DATA A ACTUALIZAR, LLAVE (WHERE) Y EL VALOR DE LA LLAVE
    public function updateRecord($table, $data, $key, $value) { 
        return $this->db->update($table, $data, "$key = '$value'");
    }

    public function insertBatch($table, $data)
    {
        $this->db->trans_begin();
        $this->db->insert_batch($table, $data);
        if (!$this->db->trans_status())  { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->db->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->db->trans_commit();
        }
    }

    public function updateBatch($table, $data, $key)
    {
        $this->db->trans_begin();
        $this->db->update_batch($table, $data, $key);
        if (!$this->db->trans_status()) { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->db->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->db->trans_commit();
        }
    }

    public function getPuesto($dt)
    {
        $query = $this->db-> query("SELECT pu.puesto
        FROM usuarios us
        INNER JOIN puestos pu ON pu.idPuesto = us.puesto
        WHERE idUsuario = $dt");

		return $query;
    }

    public function getSede($dt)
    {
        $query = $this->db-> query("SELECT se.sede
        FROM usuarios us
        INNER JOIN sedes se ON se.idSede = us.idSede
        WHERE idUsuario = $dt");
		return $query;
    }

    public function getPacientes($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];

        if($idRol == 1){
            $query = $this->db-> query("SELECT COUNT(*) AS [pacientes] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.puesto = $idData");
        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [pacientes] FROM citas WHERE idPaciente = $idData");
        }
        else{
            $query = $this->db-> query("SELECT COUNT(*) AS [pacientes] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.puesto = $idData");
        }

        return $query;
    }

    public function getCtAsistidas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];

        if($idRol == 1){
            $query = $this->db-> query("SELECT COUNT(*) AS [asistencia] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.puesto = $idData AND ct.estatusCita = 4");
        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [asistencia] FROM citas WHERE idPaciente = $idData AND estatusCita = 4");
        }
        else{
            $query = $this->db-> query("SELECT COUNT(*) AS [asistencia] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idUsuario = $idData AND ct.estatusCita = 4");
        }

        return $query;
    }

    public function getCtCanceladas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];

        if($idRol == 1){
            $query = $this->db-> query("SELECT COUNT(*) AS [cancelada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.puesto = $idData AND ct.estatusCita = 2");
        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [cancelada] FROM citas WHERE idPaciente = $idData AND estatusCita = 2");
        }
        else{
            $query = $this->db-> query("SELECT COUNT(*) AS [cancelada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idUsuario = $idData AND ct.estatusCita = 2");
        }

        return $query;
    }

    public function getCtPenalizadas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];

        if($idRol == 1){
            $query = $this->db-> query("SELECT COUNT(*) AS [penalizada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.puesto = $idData AND ct.estatusCita = 3");
        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [penalizada] FROM citas WHERE idPaciente = $idData AND estatusCita = 3");
        }
        else{
            $query = $this->db-> query("SELECT COUNT(*) AS [penalizada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idUsuario = $idData AND ct.estatusCita = 3");
        }

        return $query;
    }

    public function getAppointmentHistory($dt){

        $query = $this->db->query("SELECT us.nombre, ct.idPaciente, ct.titulo, oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
		CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario
		FROM citas ct 
		INNER JOIN catalogos ca ON ca.idCatalogo = 2
		INNER JOIN opcionesPorCatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
		INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
		WHERE ct.idPaciente = $dt AND oc.idCatalogo = 2
		GROUP BY us.nombre, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita,
		ct.fechaInicio, ct.fechaFinal
		ORDER BY ct.fechaInicio, ct.fechaFinal DESC ");

        return $query;

    }

    public function getEstatusPaciente(){
        
        $query = $this->db->query("SELECT idOpcion, nombre FROM opcionesPorCatalogo WHERE idCatalogo = 13");
        return $query;

    }

}