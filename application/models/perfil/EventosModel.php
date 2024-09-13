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
            asis.idAsistenciaEv, asis.idContrato, asis.confirmacion, asis.asistencia, 
            (SELECT COUNT(*) FROM PRUEBA_beneficiosCM.asistenciasEventos WHERE idEvento = ev.idEvento AND confirmacion = 1) AS confirmados,
            (SELECT COUNT(*) FROM PRUEBA_beneficiosCM.asistenciasEventos WHERE idEvento = ev.idEvento AND asistencia = 1) AS asistidos,
            ev.estatus, ev.creadoPor, ev.fechaCreacion, ev.modificadoPor, ev.fechaModificacion
            FROM PRUEBA_beneficiosCM.eventos AS ev
            LEFT JOIN PRUEBA_beneficiosCM.asistenciasEventos AS asis ON asis.idEvento = ev.idEvento AND asis.idContrato = ?
            LEFT JOIN PRUEBA_beneficiosCM.alcanceEvento AS alc ON alc.idEvento = ev.idEvento
            WHERE  ev.estatus = 1 
            AND (asis.idContrato IS NULL OR asis.estatus = 1)
            AND (NOW() > ev.inicioPublicacion AND NOW() < ev.finPublicacion);", array($idContrato));
       	return $query;
    }
}