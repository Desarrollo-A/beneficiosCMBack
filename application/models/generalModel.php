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

	public function updateRecord($table, $data, $key, $value)
    {
        if ($data != '' && $data != null) {
            $response = $this->db->update($table, $data, "$key = '$value'");
            if ($response)
                return true;
            else
                return false;
        } else{
            return false;
        }
    }

}