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

	public function agregarRegistro($table, $data) { 
        if ($data != '' && $data != null) {
			$this->db->db_debug = false;
            $response = $this->db->insert($table, $data);
            if (!$response){
				$error = $this->db->error();
                return $error;
			}
            else{
				return true ;
			}
        } else
            return false;
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

    public function addRecord($table, $data) 
    { // MJ: AGREGA UN REGISTRO A UNA TABLA EN PARTICULAR, RECIBE 2 PARÁMETROS. LA TABLA Y LA DATA A INSERTAR
        $response = $this->db->insert($table, $data);
        
        return $response;
    } 

    public function updateRecord($table, $data, $key, $value) 
    { // MJ: ACTUALIZA LA INFORMACIÓN DE UN REGISTRO EN PARTICULAR, RECIBE 4 PARÁMETROS. TABLA, DATA A ACTUALIZAR, LLAVE (WHERE) Y EL VALOR DE LA LLAVE
        $response = $this->db->update($table, $data, "$key = '$value'");
        
        return $response;
    }
}