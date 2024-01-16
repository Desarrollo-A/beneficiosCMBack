<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class usuariosModel extends CI_Model {
	public function __construct()
	{
		
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
			 NOT IN( SELECT idPaciente FROM citas WHERE estatusCita = ? GROUP BY idPaciente HAVING COUNT(idPaciente) > ?)
			 AND idSede
			 IN( select distinct idSede from atencionXSede where idEspecialista = ?)",
			 array( 2, 1, 1, 1, $idEspecialista )
		);
		return $query;
	}

	public function checkUser($idPaciente){
		$query = $this->db->query(
			"SELECT idPaciente FROM citas 
			WHERE estatusCita = 1 AND idPaciente = ? 
			GROUP BY idPaciente HAVING COUNT(idPaciente) = ?", 
			array( $idPaciente, 2 ));
		
		return $query;
	}

	public function getSpecialistContact($id)
	{
		$query = $this->db->query("SELECT nombre, telPersonal, correo FROM usuarios WHERE idUsuario = ?", $id);
		return $query;
	}

	public function decodePass($dt)
	{

		if(!empty($dt))
		{
			$query = $this->db-> query("SELECT *
			FROM usuarios us
			WHERE us.idUsuario = $dt");

			$pass = '';
			foreach ($query->result() as $row) {
				$pass = $row->password;
			}

			$res = desencriptar($pass);

			return $res;
		}else{
			return false;
		}
		
	}
}