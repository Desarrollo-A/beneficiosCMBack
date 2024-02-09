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
            ((startDate BETWEEN '$startDate' AND '$endDate') 
            OR (endDate BETWEEN '$startDate' AND '$endDate')
            OR ('$startDate' BETWEEN startDate AND endDate) 
            OR ('$endDate' BETWEEN startDate AND endDate)) AND
            idEspecialista='$idEspecialista'";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }

    public function checkIfPresencialExcept($idEvento, $startDate, $endDate, $idSede, $idEspecialista){
        $query = "SELECT *
        FROM presencialXSede
        WHERE
            ((startDate BETWEEN '$startDate' AND '$endDate') 
            OR (endDate BETWEEN '$startDate' AND '$endDate')
            OR ('$startDate' BETWEEN startDate AND endDate) 
            OR ('$endDate' BETWEEN startDate AND endDate)) AND
            idEspecialista='$idEspecialista' AND
            idEvento != $idEvento";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }

    public function addHorarioPresencial($startDate, $endDate, $idSede, $idEspecialista){
        $query = "INSERT INTO presencialXSede
            (startDate, endDate, idSede, idEspecialista)
            VALUES ('$startDate', '$endDate', $idSede, $idEspecialista)";

        return $this->db->query($query);
    }

    public function updateHorarioPresencial($idEvento, $startDate, $endDate, $idSede, $idEspecialista){
        $query = "UPDATE presencialXSede
            SET
                startDate = '$startDate',
                endDate = '$endDate',
                idSede = $idSede,
                idEspecialista = $idEspecialista
            WHERE idEvento = $idEvento";

        return $this->db->query($query);
    }

    public function getHorariosEspecialista($idEspecialista){
        $query = "SELECT
        presencialXSede.idEvento AS id,
        presencialXSede.startDate AS 'start',
        presencialXSede.endDate AS 'end',
        presencialXSede.idSede AS sede,
        presencialXSede.idEspecialista AS especialista,
        sedes.sede AS title
        FROM presencialXSede
        LEFT JOIN sedes ON sedes.idSede=presencialXSede.idSede
        WHERE
            presencialXSede.idEspecialista='$idEspecialista'";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }
}