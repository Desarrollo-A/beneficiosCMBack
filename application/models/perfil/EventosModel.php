<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class EventosModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

	public function getEventos($idContrato, $idSede, $idDepto){
        $query = $this->ch->query(
			"SELECT ev.idEvento, ev.titulo, ev.descripcion, ev.fechaEvento, ev.inicioPublicacion, ev.finPublicacion, ev.imagen, ev.horaEvento, ev.limiteRecepcion, ev.ubicacion,
            ev.estatusEvento, opc1.nombre AS nombreEstatusEvento,
            asis.idAsistenciaEv, asis.idContrato, asis.estatusAsistencia, opc2.nombre AS nombreEstatusAsistencia,  
            ev.estatus, ev.creadoPor, ev.fechaCreacion, ev.modificadoPor, ev.fechaModificacion,
           -- SEDES
            (SELECT CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('idsede', s.idSede, 'nsede', s.nsede, 'estatus_sede', s.estatus_sede) ORDER BY s.idSede ASC SEPARATOR ','), ']')
             FROM ". $this->schema_cm .".alcanceEvento AS a
             JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS s ON a.idSede = s.idsede
             WHERE a.idEvento = ev.idEvento) AS sedes,
            -- DEPARTAMENTOS
            (SELECT CONCAT('[', GROUP_CONCAT(DISTINCT JSON_OBJECT('iddepto', d.iddepto, 'ndepto', d.ndepto, 'estatus_depto', d.estatus_depto) ORDER BY d.iddepto ASC SEPARATOR ','), ']')
             FROM ". $this->schema_cm .".alcanceEvento AS a
             JOIN ". $this->schema_ch .".beneficioscm_vista_departamento AS d ON a.idDepartamento = d.iddepto
             WHERE a.idEvento = ev.idEvento) AS departamentos,
            (SELECT COUNT(*) FROM PRUEBA_beneficiosCM.asistenciasEventos WHERE idEvento = ev.idEvento AND estatusAsistencia = 1 AND estatusEvento = 1) AS confirmados,
            (SELECT COUNT(*) FROM PRUEBA_beneficiosCM.asistenciasEventos WHERE idEvento = ev.idEvento AND estatusAsistencia = 3 AND estatusEvento = 1) AS asistidos
            FROM ". $this->schema_cm .".eventos AS ev
            LEFT JOIN ". $this->schema_cm .".asistenciasEventos AS asis ON asis.idEvento = ev.idEvento AND asis.idContrato = ?
            LEFT JOIN ". $this->schema_cm .".alcanceEvento AS alc ON alc.idEvento = ev.idEvento
            LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo AS opc1 ON opc1.idOpcion = ev.estatusEvento AND opc1.idCatalogo = 41
            LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo AS opc2 ON opc2.idOpcion = asis.estatusAsistencia AND opc2.idCatalogo = 42
            WHERE  ev.estatus = 1 
                AND alc.estatus = 1
                AND alc.idSede = ?
                AND alc.idDepartamento = ? 
                AND (asis.idContrato IS NULL OR asis.estatus IN (1,2))
                AND (NOW() > ev.inicioPublicacion AND NOW() < ev.finPublicacion);", array($idContrato, $idSede, $idDepto));
       	return $query;
    }

    public function getasistenciaEventoUsers()
    {
        $query = $this->ch->query("SELECT ae.idEvento, opc.nombre AS estatusAsistentes,ev.titulo, ev.fechaEvento, ev.horaEvento, 
            ev.limiteRecepcion,us2.num_empleado,
            CONCAT(us2.nombre_persona, ' ', us2.pri_apellido, ' ', us2.sec_apellido) AS nombreCompleto,us2.nsede, us2.ndepto 
            FROM " . $this->schema_cm . ".asistenciasEventos AS ae 
            INNER JOIN " . $this->schema_cm . ".eventos AS ev  ON ae.idEvento = ev.idEvento
            INNER JOIN " . $this->schema_cm . ".usuarios AS us ON ae.idContrato = us.idContrato
            INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios_dos AS us2  ON us2.idContrato = us.idContrato
            INNER JOIN " . $this->schema_cm . ".opcionesporcatalogo AS opc  ON ae.estatusAsistencia = opc.idOpcion 
            AND opc.idCatalogo = 42  WHERE ae.idContrato IS NOT NULL");
             
              return $query->result();
    }

    public function getasistenciaEventoUser($idUsuario)
    {
       $query = $this->ch->query("SELECT ae.idEvento, opc.nombre AS estatusAsistentes,ev.titulo, ev.fechaEvento, ev.horaEvento, 
            ev.limiteRecepcion,us2.num_empleado,
            CONCAT(us2.nombre_persona, ' ', us2.pri_apellido, ' ', us2.sec_apellido) AS nombreCompleto,us2.nsede, us2.ndepto 
            FROM " . $this->schema_cm . ".asistenciasEventos AS ae 
            INNER JOIN " . $this->schema_cm . ".eventos AS ev  ON ae.idEvento = ev.idEvento
            INNER JOIN " . $this->schema_cm . ".usuarios AS us ON ae.idContrato = us.idContrato
            INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios_dos AS us2  ON us2.idContrato = us.idContrato
            INNER JOIN " . $this->schema_cm . ".opcionesporcatalogo AS opc  ON ae.estatusAsistencia = opc.idOpcion 
            AND opc.idCatalogo = 42  WHERE us.idUsuario = ?", $idUsuario);

              return $query->result();
             
    }
    public function getEventoUser($idContrato, $idEvento)
    {
        $query = $this->ch->query("SELECT ae.idEvento,us.idContrato, opc.nombre AS estatusAsistentes,ev.titulo, ev.fechaEvento, ev.horaEvento, ev.limiteRecepcion,ev.ubicacion, us2.num_empleado,
        CONCAT(us2.nombre_persona, ' ', us2.pri_apellido, ' ', us2.sec_apellido) AS nombreCompleto
        FROM " . $this->schema_cm . ".asistenciasEventos AS ae 
        INNER JOIN " . $this->schema_cm . ".eventos AS ev  ON ae.idEvento = ev.idEvento
        INNER JOIN " . $this->schema_cm . ".usuarios AS us ON ae.idContrato = us.idContrato
        INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios_dos AS us2  ON us2.idContrato = us.idContrato
        INNER JOIN " . $this->schema_cm . ".opcionesporcatalogo AS opc  ON ae.estatusAsistencia = opc.idOpcion 
        AND opc.idCatalogo = 42  WHERE us.idContrato = '$idContrato' AND  ae.idEvento = '$idEvento' ");
         
        return $query->result();

    }
    public function getAsistenciaEvento($idContrato, $idEvento){
        $query = $this->ch->query(
			"SELECT ev.idEvento, asis.* FROM PRUEBA_beneficiosCM.eventos AS ev
            LEFT JOIN ". $this->schema_cm .".asistenciasEventos AS asis ON asis.idEvento = ev.idEvento AND asis.idContrato = ?
            WHERE ev.idEvento = ? AND ev.estatus = 1 AND (asis.estatus IS NULL OR asis.estatus=1);", array($idContrato, $idEvento));
       	return $query;
    }

    public function inhabilitaAlanceEvento($idEvento){
        $query = $this->ch->query(
			"UPDATE ". $this->schema_cm .".alcanceEvento 
            SET estatus = 0
            WHERE idEvento = ?;", $idEvento);
       	return $query;
    }

    public function getDatosAsistenciaEvento($idEvento, $idContrato) // AND NOT (axs.tipoCita = 1 and axs.idOficina = 0) en caso de error en las modalidades
    {
        $query = $this->ch->query(
            "SELECT 
            ae.estatusAsistencia, oxc.nombre,
            ev.idEvento, ev.titulo, ev.fechaEvento,
            us2.idcontrato, us2.nombre_persona, us2.pri_apellido, us2.sec_apellido, us2.num_empleado
            FROM PRUEBA_beneficiosCM.asistenciasEventos AS ae
            INNER JOIN PRUEBA_beneficiosCM.eventos AS ev ON ae.idEvento = ev.idEvento
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idContrato = ae.idContrato
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios_dos AS us2 ON us.idContrato = us2.idcontrato
            INNER JOIN PRUEBA_beneficiosCM.opcionesporcatalogo AS oxc ON oxc.idOpcion = ae.estatusAsistencia AND oxc.idCatalogo = 42
            WHERE ae.estatus = 1 AND ev.estatus = 1 AND us.estatus = 1 AND us2.activo = 1 AND 
            ae.idEvento = ? AND ae.idContrato = ?;", 
            array($idEvento, $idContrato));

        return $query;
    }
}