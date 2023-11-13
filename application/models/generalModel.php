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

}