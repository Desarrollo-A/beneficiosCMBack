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

    public function addHorarioPresencial($presencialDate, $idSede, $idEspecialista){
        $query = "BEGIN
            IF NOT EXISTS (
                SELECT * FROM presencialXSede 
                WHERE
                    presencialDate = '$presencialDate'
                    AND idEspecialista = '$idEspecialista'
            )
                BEGIN
                    INSERT INTO presencialXSede (presencialDate, idSede, idEspecialista)
                    VALUES ('$presencialDate', '$idSede', '$idEspecialista')
                END
            ELSE
                BEGIN
                    UPDATE presencialXSede SET idSede='$idSede'
                    WHERE
                        presencialDate = '$presencialDate'
                    AND idEspecialista = '$idEspecialista'
                END
        END";

        return $this->db->query($query);
    }

    public function deleteHorarioPresencial($presencialDate, $idSede, $idEspecialista){
        $query = "DELETE FROM presencialXSede
            WHERE
                presencialDate = '$presencialDate'
            AND idEspecialista = '$idEspecialista'";

        return $this->db->query($query);
    }

    public function getHorariosEspecialista($idEspecialista){
        $query = "SELECT
        presencialXSede.idEvento AS id,
        presencialXSede.presencialDate AS 'start',
        presencialXSede.presencialDate AS 'end',
        presencialXSede.idSede AS sede,
        presencialXSede.idEspecialista AS especialista,
        sedes.sede AS title,
        'background' AS display,
        sedes.colorBack AS backgroundColor
        FROM presencialXSede
        LEFT JOIN sedes ON sedes.idSede=presencialXSede.idSede
        WHERE
            presencialXSede.idEspecialista='$idEspecialista'";

        $horaios = $this->db->query($query)->result_array();

        return $horaios;
    }

    public function getDiasPresencialXEspe($idSede, $idEspecialista){
        $query = "SELECT * FROM presencialXSede
            WHERE
                idEspecialista='$idEspecialista'
            AND idSede='$idSede'";

        $dias = $this->db->query($query)->result_array();

        return $dias;
    }
}