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
		$query = $this->db-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1");
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
}