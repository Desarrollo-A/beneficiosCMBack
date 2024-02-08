<?php

class SedesModel extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function getPresencialXEspecialista($idEspecialista){
        $query = "SELECT
            ate.idSede as value,
            sedes.sede as label
        FROM atencionXSede ate
        LEFT JOIN sedes ON sedes.idSede=ate.idSede
        WHERE
            ate.idEspecialista=$idEspecialista AND
            ate.tipoCita=1";

        $sedes = $this->db->query($query)->result_array();

        return $sedes;
    }

    public function checkIfPresencial($startDate, $endDate, $idSede, $idEspecialista){
        $query = "SELECT *
        FROM presencialXSede
        WHERE
            (endDate <= '$endDate') AND (startDate >= '$startDate') AND
            idSede='$idSede' AND
            idEspecialista='$idEspecialista'";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }

    public function addHorarioPresencial($startDate, $endDate, $idSede, $idEspecialista){
        $query = "INSERT INTO presencialXSede
            (startDate, endDate, idSede, idEspecialista)
            VALUES ('$startDate', '$endDate', $idSede, $idEspecialista)";

        return $this->db->query($query);
    }

    public function getHorariosEspecialista($idEspecialista){
        $query = "SELECT *
        FROM presencialXSede
        WHERE
            idEspecialista='$idEspecialista'";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }
}