<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class encuestasModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function insertBatch($table, $data)
    {
        $response = array();  // Initialize $response as an array

        try {
            $this->db->trans_begin();
            $this->db->insert($table, $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Error en la transacción de la base de datos.");
            } else {
                $this->db->trans_commit();
                $response['result'] = true;
                $response['msg'] = "¡Registro insertado exitosamente!";
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['result'] = false;
            $response['msg'] = "Error en la inserción de datos: " . $e->getMessage();
        }

        return $response;
    }

    public function getRespuestas()
    {
        $query = $this->db-> query("SELECT idOpcion, nombre FROM opcionesPorCatalogo WHERE idCatalogo = 4");
		return $query->result();
    }

    public function encuestaMinima()
    {
        $query = $this->db-> query("SELECT COALESCE(MAX(idEncuesta), 0) AS minIdEncuesta FROM encuestasCreadas;");
		return $query->result();
    }

    public function getEncuesta($dt)
    {
        $query = $this->db-> query("SELECT * FROM encuestasCreadas WHERE idEncuesta =1");
		return $query->result();
    }

    public function getResp1()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 1");
		return $query->result();
    }

    public function getResp2()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 2");
		return $query->result();
    }

    public function getResp3()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 3");
		return $query->result();
    }

    public function getResp4()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 4");
		return $query->result();
    }
}