<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class UsuariosModel extends CI_Model {
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
		$query = $this->db->query("	SELECT u.*, p.idPuesto, p.puesto, p.idArea, p.tipoPuesto, a.idDepto FROM USUARIOS as u
		INNER JOIN puestos AS p ON u.idPuesto = P.idPuesto
		INNER JOIN areas AS a ON a.idArea = p.idArea
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
			 US.idPuesto = PS.idPuesto
			 WHERE US.idRol = ?
			 AND US.estatus = ?
			 AND US.idSede
			 IN( select distinct idSede from atencionXSede where idEspecialista = ?)",
			 array( 2, 1, $idEspecialista )
		);
		return $query;
	}

	public function checkUser($idPaciente, $year, $month){ // función para checar si el beneficiario lleva 2 beneficios usados, sin importar mes
		// $query = $this->db->query(
		// 	"SELECT idPaciente from (select *from citas ct where YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ?) as citas 
		// 	WHERE estatusCita IN(?, ?, ?) AND idPaciente = ? 
		// 	GROUP BY idPaciente HAVING COUNT(idPaciente) > ?",
		// 	array( $year, $month, 1, 4, 6, $idPaciente, 1 )); // version de query por mes

			$query = $this->db->query(
				"SELECT idPaciente from citas
				WHERE estatusCita IN(?, ?, ?) AND idPaciente = ? AND evaluacion != null
				GROUP BY idPaciente HAVING COUNT(idPaciente) > ?",
				array(1, 4, 6, $idPaciente, 1 )); // version de query por todos los tiempos
		
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

	public function updateRefreshToken($idUsuario, $refresh_token){
		$query = "UPDATE usuarios SET refreshToken=$refresh_token WHERE idUsuario=$idUsuario";

		return $this->db->query($query);
	}
}