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

	public function disableUser()
	{
		$this->db->set('estatus', 0); // Establece el valor del campo estatus a 0
		$this->db->where('id_usuario', $idUsuario); // Especifica la condición WHERE para el usuario que deseas actualizar
		$this->db->update('usuarios'); // Nombre de tu tabla de usuarios
		$this->db->query();
		
        return $query->result();
	}
}