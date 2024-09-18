<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class UsuariosModel extends CI_Model {
	public function __construct()
	{
		$this->ch = $this->load->database('ch', TRUE);
		$this->schema_cm = $this->config->item('schema_cm');
		$this->schema_ch = $this->config->item('schema_ch');
    	parent::__construct();
	}

    public function usuarios()
	{
		$query = $this->ch-> query("SELECT * FROM ".$this->schema_cm .".usuarios");
		return $query->result();
	}

	public function getUsers()
	{
		$query = $this->ch->get('usuarios'); 
		return $query->result();
	}

	public function getUsersExternos()
	{
		$query = $this->ch->query("SELECT *FROM ".$this->schema_cm .".usuarios AS u
		LEFT JOIN ".$this->schema_cm .".usuariosexternos AS ue ON ue.idContrato = u.idContrato
		WHERE u.externo = 1;");
		return $query;
	}

	public function login($numEmpleado, $password)
    {

		if($numEmpleado == 'admin99'){
			$query = $this->ch->query(
				"SELECT us.idUsuario, us.idContrato, us.idAreaBeneficio,'admin99' AS numEmpleado,
				'SUPER ADMIN' AS nombre, 000000000 AS telPersonal, 'Programador Analista' AS tipoPuesto,
				1 AS idSede, 'QRO' AS sede, 19 AS idPuesto, 'Programador Analista' AS puesto, 
				24 AS idArea, 'Desarrollo' AS area, 7 AS idDepto, 'TI' AS departamento,
				us.estatus, us.creadoPor, us.fechaCreacion, us.modificadoPor, us.fechaModificacion,
				c.correo AS correo, us.idRol, us.idAreaBeneficio, 
				us.estatus, us.creadoPor, us.fechaCreacion, us.modificadoPor, us.fechaModificacion,
				'1' AS activo, us.password, us.permisos
				FROM ". $this->schema_cm .".usuarios AS us
				INNER JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
				WHERE us.idUsuario = 1;");
		}else{
			$query = $this->ch->query(
				"SELECT us.idUsuario, us.idContrato, us2.num_empleado AS 'numEmpleado',
				CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
				us2.telefono_personal AS telPersonal, us.password, us2.sexo, us.externo, us.idRol, 
				us2.fingreso AS 'fechaIngreso', us2.tipo_puesto AS 'tipoPuesto',
				us2.idsede AS 'idSede', us2.nsede AS 'sede', us2.idpuesto AS 'idPuesto', us2.npuesto AS 'puesto', us2.idarea AS 'idArea', 
				us2.narea as 'area', us2.iddepto AS 'idDepto', us2.ndepto as 'departamento', us.idAreaBeneficio, 
				us.estatus, us.creadoPor, us.fechaCreacion, us.modificadoPor, us.fechaModificacion, c.correo AS correo,
				us2.activo, us.password, us.permisos
				FROM ". $this->schema_cm .".usuarios AS us 
				INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
				LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
				WHERE us2.num_empleado = ?", array( $numEmpleado ));
		}
        
        return $query;
    }

    public function loginAPI($username, $password){
        $query = $this->ch->query(
			"SELECT * FROM ".$this->schema_cm . ".usuarios_api
        	WHERE username = ? AND password = ? ", array( $username, $password ));

        return $query;
    }

	public function getAreas()
	{
		$query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".usuarios AS us
		LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato");

        return $query;
	}

	public function getNameUser($idEspecialista)
	{
		$query = $this->ch->query(
			"SELECT US.idUsuario, US.idContrato, US.password, US.idRol, US.externo, US.idAreaBeneficio,
 			US.estatus, US.creadoPor, US.fechaCreacion, US.modificadoPor, US.fechaModificacion,
			us2.idsede AS idSede, us2.idArea, us2.tipo_puesto AS tipoPuesto, us2.fingreso AS fechaIngreso,
			TRIM(CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), 
         	' ', IFNULL(us2.sec_apellido, ''), ' (', IFNULL(us2.nsede, ''), ')')) AS nombreCompleto,
			us2.npuesto as nombrePuesto, us2.tipo_puesto, c.correo AS correo 
			FROM ". $this->schema_cm .".usuarios US 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = US.idContrato 
			LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
			WHERE US.idRol IN (?, ?, ?) AND US.estatus = ? AND US.idUsuario <> (?)
				AND us2.idsede IN 
					(SELECT axs.idSede 
					FROM ". $this->schema_cm .".atencionxsede as axs 
					INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON axs.idSede = s.idsede
					WHERE idEspecialista = ? AND estatus = 1)", array( 2, 3, 4, 1, $idEspecialista, $idEspecialista )
		);
		
		return $query;
	}

	public function checkUser($idPaciente, $year, $month){ // función para checar si el beneficiario lleva 2 beneficios usados, sin importar mes
		
		$query = $this->ch->query(
			"SELECT idPaciente FROM (SELECT * FROM ". $this->schema_cm .".citas ct WHERE YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ?) AS citas 
			WHERE estatusCita IN(?) AND idPaciente = ? AND tipoCita != ? GROUP BY idPaciente HAVING COUNT(idPaciente) > ?",
			array( $year, $month, 4, $idPaciente, 3, 1 ));

		return $query;
	}

	public function decodePass($dt)
	{
		if(!empty($dt))
		{
			if($dt == 'admin99'){
				$query = $this->ch-> query("SELECT password 
				FROM ". $this->schema_cm .".usuarios 
				WHERE idUsuario = ?", 1);
			}else{
				$query = $this->ch-> query("SELECT password 
				FROM ". $this->schema_ch .".beneficioscm_vista_usuarios us2 
				INNER JOIN ". $this->schema_cm .".usuarios us ON us.idContrato = us2.idcontrato
				WHERE us2.num_empleado = ?", $dt);
			}
			
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

	public function decodePassAdmin(){
		
		$query = $this->ch-> query("SELECT idUsuario, password 
		FROM ". $this->schema_cm .".usuarios");

		$res = array();

		foreach ($query->result() as $row) {

			$desencriptado = desencriptar($row->password);

			$res[] = array(
				'idUsuario' => $row->idUsuario,
				'password' => $desencriptado
			);
		}
		
		return $res;
	
	}

	public function updateRefreshToken($idUsuario, $refresh_token){
		$query = "UPDATE ". $this->schema_cm .".usuarios SET refreshToken=$refresh_token WHERE idUsuario=$idUsuario";

		return $this->ch->query($query);
	}
  
	public function getUserByIdContrato($idContrato){
		$query = $this->ch->query(
			"SELECT idContrato 
			FROM ". $this->schema_cm .".usuarios
			WHERE idContrato=?", $idContrato);

		return $query;
	}

	public function getUserByNumEmpleado($numEmpleado){
		$query = $this->ch->query(
			"SELECT * FROM ". $this->schema_cm .".usuarios AS us
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usCH ON usCH.idcontrato = us.idContrato
			WHERE usCH.num_empleado = ?;", $numEmpleado);

		return $query;
	}

	public function getToken($dt)
    {
		$dt_array = json_decode($dt, true);

		$correo = $dt_array["correo"];
        $token = $dt_array["token"];

        $query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".tokenregistro WHERE correo = ? AND token = ?", array($correo, $token));

		if ($query->num_rows() > 0) {
			echo json_encode(array("estatus" => true, "msj" => "Código correcto" ), JSON_NUMERIC_CHECK); 
			$this->ch->query("DELETE FROM ". $this->schema_cm .".tokenregistro WHERE correo = ?", array($correo));
		}else{
			echo json_encode(array("estatus" => false, "msj" => "El código insertado no es correcto"), JSON_NUMERIC_CHECK);
		}
    }

	public function getUserByNumEmp($numEmpleado)
    {
        $query = $this->ch->query(
        "SELECT *, c.correo AS correo
			FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
			LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
			WHERE us.num_empleado = ?
			ORDER BY us.activo DESC
			LIMIT 1;", array( $numEmpleado ));

        return $query;
	}

	public function insertTempMail($correo, $idContrato)
    {
		$this->ch->query("INSERT INTO ". $this->schema_cm .".correostemporales (correo, idContrato)
		VALUES (?, ?);", array($correo, $idContrato));
    }

	public function getCorreoEmpleado($noEmp){
		$query = $this->ch->query("SELECT idUsuario, us1.idContrato, us1.idRol, us1.estatus, c.correo AS mail_emp, num_empleado, 
		CONCAT(nombre_persona, ' ', pri_apellido, ' ', sec_apellido) AS nombreUsuario
			FROM ". $this->schema_cm .".usuarios us1 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us1.idContrato = us2.idContrato
			INNER JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us2.idcontrato 
			WHERE num_empleado = ? AND us1.estatus = ?", array($noEmp, 1));

		return $query;
	}

	public function deleteToken($mailUsuario){
		$query = $this->ch->query("DELETE t1 FROM ". $this->schema_cm .".tokenregistro t1
			JOIN ". $this->schema_cm .".tokenregistro t2 ON t1.idTokenRegistro = t2.idTokenRegistro
			WHERE t2.correo = ?", $mailUsuario);

		return $query;
	}

	public function checkToken($code){
		$query = $this->ch->query("SELECT *FROM ". $this->schema_cm .".tokenregistro WHERE token = ?", $code);

		return $query;
	}

	public function checkTokenByMail($mailEmp){
		$query = $this->ch->query("SELECT *FROM ". $this->schema_cm .".tokenregistro WHERE correo = ?", $mailEmp);

		return $query;
	}

	public function deleteOldToken($mailEmp){
		$query = $this->ch->query("DELETE t1 FROM ". $this->schema_cm .".tokenregistro t1
			JOIN ". $this->schema_cm .".tokenregistro t2 ON t1.idTokenRegistro = t2.idTokenRegistro
			WHERE t2.correo = ? AND t2.fechaCreacion < DATE_SUB(NOW(), INTERVAL 5 MINUTE)", $mailEmp);

			return $query;
	}

	public function saveToken($mailUsuario, $token){
		$query = $this->ch->query("INSERT INTO ". $this->schema_cm .".tokenregistro (correo, token, fechaCreacion) VALUES(?, ?, NOW())", array($mailUsuario, $token));

		return $query;
	}

	public function getSedesActivo($idUsuario){
		$query = $this->ch->query("SELECT idSede FROM ". $this->schema_cm .".atencionxsede WHERE idEspecialista = 8 AND estatus = 1");

		return $query;
	}
}
