<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class usuariosModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function usuarios()
	{
		$query = $this->db-> query("SELECT *FROM usuarios");
		return $query->result();
	}

	public function getUsers()
	{
		$query = $this->db->get('usuarios'); 
		return $query->result();
	}

	public function login($numEmpleado,$password)
	{
		$query = $this->db->query("SELECT *  FROM usuarios WHERE numEmpleado='$numEmpleado' AND password='$password'");
		return $query->result();
	}

	public function getAreas()
	{
		$query = $this->db->distinct()->select('area')->get('usuarios');
        return $query->result();
	}

	public function getNameUser()
	{
		$query = $this->db->query("SELECT idUsuario, nombre FROM usuarios");
		return $query->result();
	}
}