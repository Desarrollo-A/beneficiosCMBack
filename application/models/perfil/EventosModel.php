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
}