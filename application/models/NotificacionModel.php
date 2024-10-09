<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NotificacionModel extends CI_Model{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getNotificacion($idUsuario){

        $usr = isset($idUsuario) ? $idUsuario : 0;

        $query = $this->ch->query(
            "SELECT
            nt.idNotificacion AS id,
            nt.tipo,
            oxc.nombre AS mensaje,
            nt.fechaCreacion AS fecha,
            nt.mensaje AS tipoMensaje,
            nt.icono,
            CASE 
            WHEN us2.idpuesto = 537 THEN 'nutrición'
            WHEN us2.idpuesto = 585 THEN 'psicología'
            WHEN us2.idpuesto = 686 THEN 'guía espiritual'
            END AS beneficio,
            CONCAT( 
                DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', 
                    CASE 
                        WHEN us4.idSede = 9 THEN DATE_FORMAT(DATE_ADD(ct.fechaInicio, INTERVAL 1 HOUR), '%H:%i') 
                        WHEN us4.idSede = 11 THEN DATE_FORMAT(DATE_SUB(ct.fechaInicio, INTERVAL 1 HOUR), '%H:%i') 
                    ELSE 
                        DATE_FORMAT(ct.fechaInicio, '%H:%i') END, ' - ', 
                    CASE 
                        WHEN us4.idSede = 9 THEN DATE_FORMAT(DATE_ADD(ct.fechaFinal, INTERVAL 1 HOUR), '%H:%i') 
                        WHEN us4.idSede = 11 THEN DATE_FORMAT(DATE_SUB(ct.fechaFinal, INTERVAL 1 HOUR), '%H:%i') 
                    ELSE DATE_FORMAT(ct.fechaFinal, '%H:%i') END ) AS horario,
            dp.estatusPago AS pago,
            ct.idCita
            FROM ". $this->schema_cm .".notificaciones nt
            INNER JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = nt.mensaje AND oxc.idCatalogo = 18
            LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idCita = nt.idCita
            LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios_dos us2 ON us2.idcontrato = us.idContrato
            LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle AND dp.estatusPago = 1
            LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = nt.idUsuario
            LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios_dos us4 ON us4.idcontrato = us3.idContrato 
            WHERE nt.idUsuario = $usr");

        return $query;
    }

    public function deleteNotificacion($id){
        $query = $this->ch->query(
            "DELETE FROM  ". $this->schema_cm .".notificaciones WHERE idNotificacion = $id"
        );

        return $query;
    }
}