<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CitasModel extends CI_Model{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function cancelFromUser($idUsuario){

        $query = "UPDATE ". $this->schema_cm .".citas
        SET estatusCita=2
        WHERE idPaciente=$idUsuario";

        return $this->ch->query($query);
    }

    public function getCitasPendientes($idEspecialista, $idSede, $fechaInicio, $fechaFinal){

            $query = "SELECT *
            FROM ". $this->schema_cm .".citas as ct
            LEFT JOIN ". $this->schema_cm .".atencionxsede as axs ON ct.idAtencionXSede = axs.idAtencionXSede
            WHERE ct.idEspecialista='$idEspecialista'
            AND ct.estatusCita IN (1)
            AND NOT axs.idSede = '$idSede'
            AND ct.fechaInicio BETWEEN '$fechaInicio' AND '$fechaFinal'";

        return $this->ch->query($query)->result_array();
    }
}