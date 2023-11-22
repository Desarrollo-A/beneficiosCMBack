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

    public function usr_count()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [usuarios] FROM usuarios");
		return $query->result();
	}

    public function citas_count()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM citas");
		return $query->result();
	}

    public function especialistas()
	{
		$query = $this->db-> query("SELECT * FROM opcionesporcatalogo WHERE idCatalogo = 1");
		return $query->result();
	}

    public function insertBatch($table, $data)
    {
        try {
            $this->db->trans_begin();
            $this->db->insert_batch($table, $data);
    
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Error en la transacción de la base de datos.");
            } else {
                $this->db->trans_commit();
                $response['result'] = true;
                $response['msg'] = "¡Listado insertado exitosamente!";
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['result'] = false;
            $response['msg'] = "Error en la inserción de datos: " . $e->getMessage();
        }
    
        return $response['result'];
    }
}