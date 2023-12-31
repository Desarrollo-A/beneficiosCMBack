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

	public function getNameUser($idEspecialista)
	{
		$query = $this->db->query(
			"SELECT idUsuario, nombre FROM usuarios
			 WHERE idRol = ?
			 AND estatus = ?
			 AND idUsuario 
			 NOT IN( SELECT idPaciente FROM citas WHERE estatus = ? GROUP BY idPaciente HAVING COUNT(idPaciente) > ?)
			 AND sede
			 IN( select distinct idSede from atencionXSede where idEspecialista = ?)",
			 array( 2, 1, 1, 1, $idEspecialista )
		);
		return $query->result();
	}

	// public function checkUser($idPaciente){
	// 	$query = $this->db->query("SELECT idPaciente FROM citas 
	// 	WHERE estatus != 4 AND idPaciente = 62 GROUP BY idPaciente HAVING COUNT(idPaciente) = ?", 
	// 	$idPaciente);
		
	// 	return $query;
	// }
}