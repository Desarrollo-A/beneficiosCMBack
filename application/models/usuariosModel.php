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

	public function login($numEmpleado, $password)
	{
		$query = $this->db->query("	SELECT u.*, p.idPuesto, p.puesto, p.idArea FROM USUARIOS as u
			INNER JOIN puestos AS p ON u.puesto = P.idPuesto
			WHERE numEmpleado = ? AND password = ?;", array( $numEmpleado, $password ));
		return $query;
	}

	public function getAreas()
	{
		$query = $this->db->distinct()->select('area')->get('usuarios');
        return $query->result();
	}

	public function getNameUser($idEspecialista)
	{
		$query = $this->db->query(
			"SELECT US.*, PS.puesto as nombrePuesto FROM usuarios US
			 INNER JOIN puestos PS ON
			 US.puesto = PS.idPuesto
			 WHERE US.idRol = ?
			 AND US.estatus = ?
			 AND US.idUsuario
			 NOT IN( SELECT idPaciente FROM citas WHERE estatusCita = ? GROUP BY idPaciente HAVING COUNT(idPaciente) > ?)
			 AND US.idSede
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