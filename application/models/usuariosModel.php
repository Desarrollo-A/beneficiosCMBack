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

	public function getUsersExternos()
	{
		$query = $this->db->query("SELECT *FROM usuarios WHERE externo = 1"); 
		return $query;
	}

	public function login($numEmpleado, $password)
	{
		$query = $this->db->query("SELECT u.*, p.idPuesto, p.puesto, p.idArea, p.tipoPuesto, a.idDepto FROM USUARIOS as u
		FULL JOIN puestos AS p ON u.idPuesto = P.idPuesto
		FULL JOIN areas AS a ON a.idArea = p.idArea
		WHERE numEmpleado = ? AND password = ?;", array( $numEmpleado, $password ));
		return $query;
	}

	public function getAreas()
	{
		$query = $this->db->query("SELECT *from usuarios");
        return $query;
	}

	public function getNameUser($idEspecialista)
	{
		$query = $this->db->query(
			"SELECT US.*, PS.idArea, CONCAT(US.nombre, ' ', '(', SE.sede, ')') AS nombreCompleto, PS.puesto as nombrePuesto, PS.tipoPuesto FROM usuarios US
			 INNER JOIN puestos PS ON
			 US.idPuesto = PS.idPuesto
			 INNER JOIN sedes SE ON SE.idSede = US.idSede
			 WHERE US.idRol = ?
			 AND US.estatus = ?
			 AND US.idSede
			 IN ( select distinct idSede from atencionXSede where idEspecialista = ? )
			 UNION
			 SELECT US2.*, NULL AS idArea, CONCAT('(Lamat)', ' ', US2.nombre) AS nombreCompleto, 'nombrePuesto' = 'na', 'tipoPuesto' = 'na' FROM usuarios US2 where externo = 1",
			 array( 2, 1, $idEspecialista )
		);
		
		return $query;
	}

	public function checkUser($idPaciente, $year, $month){ // función para checar si el beneficiario lleva 2 beneficios usados, sin importar mes
		$query = $this->db->query(
			"SELECT idPaciente from (select *from citas ct where YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ?) as citas 
			WHERE estatusCita IN(?) AND idPaciente = ? AND tipoCita != ? GROUP BY idPaciente HAVING COUNT(idPaciente) > ?",
			array( $year, $month, 4, $idPaciente, 3, 1 )); // version de query por mes
		
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

	public function getUserByNumEmpleado($idContrato){
		$query = $this->db->query(
			"SELECT idContrato 
			FROM usuarios
			WHERE idContrato=?", $idContrato);

		return $query;
	}
}