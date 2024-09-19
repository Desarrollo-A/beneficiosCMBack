<?php
defined('BASEPATH') or exit('No direct script access allowed');

class asistenciaEvModel extends CI_Model
{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }
    public function getasistenciaEvento()
    {
        $query = $this->ch->query("SELECT ae.idEvento, opc.nombre AS estatusAsistentes,ev.titulo, ev.fechaEvento, ev.horaEvento, 
            ev.limiteRecepcion,us2.num_empleado,
            CONCAT(us2.nombre_persona, ' ', us2.pri_apellido, ' ', us2.sec_apellido) AS nombreCompleto,us2.nsede, us2.ndepto 
            FROM " . $this->schema_cm . ".asistenciasEventos AS ae 
            INNER JOIN " . $this->schema_cm . ".eventos AS ev  ON ae.idEvento = ev.idEvento
            INNER JOIN " . $this->schema_cm . ".usuarios AS us ON ae.idContrato = us.idContrato
            INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios AS us2  ON us2.idContrato = us.idContrato
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
            INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios AS us2  ON us2.idContrato = us.idContrato
            INNER JOIN " . $this->schema_cm . ".opcionesporcatalogo AS opc  ON ae.estatusAsistencia = opc.idOpcion 
            AND opc.idCatalogo = 42  WHERE us.idUsuario = ?", $idUsuario);
             
              return $query->result();
    }
}    