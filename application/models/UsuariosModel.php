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
        $query = $this->ch->query(
        "SELECT us.idUsuario, us.idContrato, us2.num_empleado AS 'numEmpleado',
        CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
        us2.telefono_personal AS telPersonal, us.password, us2.sexo, us.externo, us.idRol, 
        us2.fingreso AS 'fechaIngreso', us2.tipo_puesto AS 'tipoPuesto',
        us2.idsede AS 'idSede', us2.nsede AS 'sede', us2.idpuesto AS 'idPuesto', us2.npuesto AS 'puesto', us2.idarea AS 'idArea', 
        us2.narea as 'area', us2.iddepto AS 'idDepto', us2.ndepto as 'departamento', us.idAreaBeneficio, 
        us.estatus, us.creadoPor, us.fechaCreacion, us.modificadoPor, us.fechaModificacion,
		CASE
			WHEN c.correo = '' THEN us2.mail_emp
			WHEN c.correo IS NULL THEN us2.mail_emp
			WHEN c.estatus = 0 THEN us2.mail_emp
			ELSE c.correo
		END AS correo 
        FROM ". $this->schema_cm .".usuarios AS us 
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
		LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
        WHERE us2.num_empleado = ? AND us.password = ?;", array( $numEmpleado, $password ));
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
			"SELECT US.*, us2.idsede AS idSede, us2.idArea, us2.tipo_puesto AS tipoPuesto, us2.fingreso AS fechaIngreso,
			CONCAT(CONCAT (us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido),' ', '(', us2.nsede, ')') AS nombreCompleto, us2.npuesto as nombrePuesto, us2.tipo_puesto, 
			CASE
				WHEN c.correo = '' THEN us2.mail_emp
				WHEN c.correo IS NULL  THEN us2.mail_emp
				ELSE c.correo
			END AS correo 
			FROM ". $this->schema_cm .".usuarios US 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = US.idContrato 
			LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us2.idcontrato 
			WHERE US.idRol = ? AND US.estatus = ? AND us2.idsede IN ( SELECT DISTINCT idSede FROM ". $this->schema_cm .".atencionxsede WHERE idEspecialista = ? ) 
			UNION ( SELECT u.idUsuario AS idUsuario, u.idContrato, u.password, us2.idRol, u.externo, u.idAreaBeneficio, us2.estatus, us2.creadoPor, us2.fechaCreacion,
			 us2.modificadoPor, us2.fechaModificacion, 1 AS idSede, 0 AS idarea, 0 tipoPuesto, 0 AS fechaIngreso, CONCAT('(Lamat)', ' ', CONCAT(IFNULL(us2.nombre, ''))) AS nombreCompleto,
			0 AS nombrePuesto, 0 AS tipo_puesto, us2.correo
			FROM ". $this->schema_cm .".usuarios as u 
			INNER JOIN ". $this->schema_cm .".usuariosexternos AS us2 ON us2.idContrato = u.idContrato 
			WHERE u.externo = ? )",
			 array( 2, 1, $idEspecialista, 1 )
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
			$query = $this->ch-> query("SELECT password 
			FROM ". $this->schema_ch .".beneficioscm_vista_usuarios us2 
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idContrato = us2.idcontrato
			WHERE us2.num_empleado = ?", $dt);

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
        $token = $dt_array["token"]["codigo"];

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
		$TempMail = $this->ch->query(
			"SELECT * from ". $this->schema_ch .".beneficioscm_vista_usuarios us
			INNER JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
			WHERE us.num_empleado = ?", array( $numEmpleado ));

		if ($TempMail->num_rows() > 0) {

		$query = $this->ch->query(
			"SELECT *, c.correo AS correo
					FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
					LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
					WHERE us.num_empleado = ?", array( $numEmpleado ));

		}else{

        $query = $this->ch->query(
        "SELECT *,
			CASE
				WHEN mail_emp = '' THEN c.correo
				ELSE mail_emp
			END AS correo
			FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
			LEFT JOIN ". $this->schema_cm .".correostemporales c ON c.idContrato = us.idcontrato 
			WHERE us.num_empleado = ?", array( $numEmpleado ));
		}
        return $query;
	}

	public function insertTempMail($correo, $idContrato)
    {
		$this->ch->query("INSERT INTO ". $this->schema_cm .".correostemporales (correo, idContrato)
		VALUES (?, ?);", array($correo, $idContrato));
    }

	public function getCorreoEmpleado($noEmp){
		$query = $this->ch->query("SELECT idUsuario, us1.idContrato, idRol, estatus, mail_emp, num_empleado, 
		CONCAT(nombre_persona, ' ', pri_apellido, ' ', sec_apellido) AS nombreUsuario
			FROM ". $this->schema_cm .".usuarios us1 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us1.idContrato = us2.idContrato
			WHERE num_empleado = ? AND estatus = ?", array($noEmp, 1));

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
}
