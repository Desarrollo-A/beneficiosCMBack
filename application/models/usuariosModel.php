<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class UsuariosModel extends CI_Model {
	public function __construct()
	{
		$this->ch        = $this->load->database('ch', TRUE);
        $this->beneficio = $this->load->database('beneficio', TRUE);
		$this->pp        = $this->load->database('pp', TRUE);
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

	public function login_view_mysql($numEmpleado, $password)
	{
		$query = $this->ch->query("SELECT u.*, p.idPuesto, p.puesto, p.idArea, p.tipoPuesto, a.idDepto FROM v_USUARIOS
		WHERE numEmpleado = ? AND password = ?;", array( $numEmpleado, $password ));
		return $query;
	}

	public function login_view_sql($numEmpleado, $password)
	{
		$query = $this->beneficios->query("SELECT u.*, p.idPuesto, p.puesto, p.idArea, p.tipoPuesto, a.idDepto FROM v_USUARIOS
		WHERE numEmpleado = ? AND password = ?;", array( $numEmpleado, $password ));
		return $query;
	}

	// public function loginFusion2($numEmpleado, $password)
	// {
	// 	$usuarios_ch = $this->ch->query("CALL sp_usuarios()")->result();
		
	// 	$this->beneficio->query("
	// 	CREATE TABLE #tmpUsuarios (
	// 	idContrato VARCHAR(100),
	// 	numEmpleado VARCHAR(100),
	// 	idSede INT,
	// 	sexo VARCHAR(3),
	// 	idPuesto INT,
	// 	nombrePersona VARCHAR(100),
	// 	priApellido VARCHAR(100),
	// 	secApellido VARCHAR(100),
	// 	fIngreso DATE,
	// 	nSede VARCHAR(50),
	// 	activo INT,
	// 	mailEmp VARCHAR(255),
	// 	nPuesto VARCHAR(255),
	// 	telefonoPersonal VARCHAR(15)
	// 	)"
	// 	)->result();

	// 	// Insertar los resultados del procedimiento almacenado en la tabla temporal
	// 	foreach ($usuarios_ch as $user) {
	// 		$this->beneficios->insert('#tmpUsuarios', $user);
	// 	}

	// 	$query = $this->beneficios->query("SELECT * FROM #tmpUsuarios
	// 	WHERE numEmpleado = ? AND password = ?;", array( $numEmpleado, $password ));
	// 	return $query;
	// }

	// ----------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------

	// 1: SQLSRV CON SP
	public function loginFusion($numEmpleado, $password)
	{	
    	$usuarios_ch = $this->ch->query("CALL sp_usuarios()")->result();

    	$this->beneficio->query("
    	    CREATE TABLE #tmpUsuarios (
    	        idContrato VARCHAR(100),
    	        numEmpleado VARCHAR(100),
    	        idSede INT,
    	        sexo VARCHAR(3),
    	        idPuesto INT,
    	        nombrePersona VARCHAR(100),
    	        priApellido VARCHAR(100),
    	        secApellido VARCHAR(100),
    	        fIngreso DATE,
    	        nSede VARCHAR(50),
    	        activo INT,
    	        mailEmp VARCHAR(255),
    	        nPuesto VARCHAR(255),
    	        telefonoPersonal VARCHAR(15)
    	    )
    	");

    	// Insertar los resultados del procedimiento almacenado en la tabla temporal
    	foreach ($usuarios_ch as $user) {
    	    $this->beneficio->query("
    	        INSERT INTO #tmpUsuarios (idContrato, numEmpleado, idSede, sexo, idPuesto, nombrePersona, priApellido, secApellido, fIngreso, nSede, activo, mailEmp, nPuesto, telefonoPersonal)
    	        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    	        array($user->idContrato, $user->numEmpleado, $user->idSede, $user->sexo, $user->idPuesto, $user->nombrePersona, $user->priApellido, $user->secApellido, $user->fIngreso, $user->nSede, $user->activo, $user->mailEmp, $user->nPuesto, $user->telefonoPersonal)
    	    );
    	}

    	// Aquí necesitarías ajustar la consulta para buscar la contraseña en el lugar correcto
    	$query = $this->beneficio->query("
		SELECT * FROM usuarios as u
		INNER JOIN #tmpUsuarios as tmpu ON u.idContrato = tmpu.idContrato
		WHERE numEmpleado = ? AND password = ?;", array($numEmpleado, $password));

    	return $query;
	}

	// SQLSRV CON VISTA
	public function loginFusion2($numEmpleado, $password)
	{	
    	$usuarios_ch = $this->ch->query("SELECT *FROM v_usuarios")->result();

    	$this->beneficio->query("
    	    CREATE TABLE #tmpUsuarios (
    	        idContrato VARCHAR(100),
    	        numEmpleado VARCHAR(100),
    	        idSede INT,
    	        sexo VARCHAR(3),
    	        idPuesto INT,
    	        nombrePersona VARCHAR(100),
    	        priApellido VARCHAR(100),
    	        secApellido VARCHAR(100),
    	        fIngreso DATE,
    	        nSede VARCHAR(50),
    	        activo INT,
    	        mailEmp VARCHAR(255),
    	        nPuesto VARCHAR(255),
    	        telefonoPersonal VARCHAR(15)
    	    )
    	");

    	// Insertar los resultados del procedimiento almacenado en la tabla temporal
    	foreach ($usuarios_ch as $user) {
    	    $this->beneficio->query("
    	        INSERT INTO #tmpUsuarios (idContrato, numEmpleado, idSede, sexo, idPuesto, nombrePersona, priApellido, secApellido, fIngreso, nSede, activo, mailEmp, nPuesto, telefonoPersonal)
    	        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    	        array($user->idContrato, $user->numEmpleado, $user->idSede, $user->sexo, $user->idPuesto, $user->nombrePersona, $user->priApellido, $user->secApellido, $user->fIngreso, $user->nSede, $user->activo, $user->mailEmp, $user->nPuesto, $user->telefonoPersonal)
    	    );
    	}

    	// Aquí necesitarías ajustar la consulta para buscar la contraseña en el lugar correcto
    	$query = $this->beneficio->query("
		SELECT * FROM usuarios as u
		INNER JOIN #tmpUsuarios as tmpu ON u.idContrato = tmpu.idContrato
		WHERE numEmpleado = ? AND password = ?;", array($numEmpleado, $password));

    	return $query;
	}

	// MYSQL CON VIEW
	public function loginFusion3($numEmpleado, $password)
	{	
    	// Aquí necesitarías ajustar la consulta para buscar la contraseña en el lugar correcto
    	$query = $this->ch->query("
		SELECT * FROM usuarios_bnfcs as u
		INNER JOIN v_usuarios as tmpu ON u.idContrato = tmpu.idContrato
		WHERE numEmpleado = ? AND password = ?;", array($numEmpleado, $password));

    	return $query;
	}

	// MYSQL CON SP
	public function loginFusion4($numEmpleado, $password)
	{	
		$query =  $this->ch->query("CALL sp_usuarios()");
		$usuarios_ch = $query->result();
	
		// Liberar los resultados
		$query->free_result();
		$this->ch->close();
	
		// Reabrir la conexión
		$this->ch->reconnect();
	
		// Crear una tabla temporal
		$this->ch->query("CREATE TEMPORARY TABLE tmpUsuarios (idContrato VARCHAR(100),numEmpleado VARCHAR(100),idSede INT,sexo VARCHAR(3),idPuesto INT,nombrePersona VARCHAR(100),priApellido VARCHAR(100),secApellido VARCHAR(100),fIngreso DATE,nSede INT,activo INT,mailEmp VARCHAR(255),nPuesto VARCHAR(255),telefonoPersonal VARCHAR(15))");
	
		// Insertar los resultados del procedimiento almacenado en la tabla temporal
		foreach ($usuarios_ch as $usuario) {
			$this->ch->insert('tmpUsuarios', $usuario);
		}
	
		$query = $this->ch->query("
		SELECT * FROM usuarios_bnfcs as u
		INNER JOIN tmpUsuarios as tmpu ON u.idContrato = tmpu.idContrato
		WHERE numEmpleado = ? AND password = ?;", array($numEmpleado, $password));

    	return $query;
	}

// ----------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------

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

	public function usuariosPrueba()
	{	
    	// Aquí necesitarías ajustar la consulta para buscar la contraseña en el lugar correcto
    	$query = $this->pp->query("
		SELECT * FROM PRUEBA_beneficiosCM.usuarios AS us
		INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 
		ON us.idContrato = us2.num_empleado;");

    	return $query;
	}

}