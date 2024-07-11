<?php

class SedesModel extends CI_Model {
    public function __construct(){
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getPresencialXEspecialista($idEspecialista){

        $query = "SELECT
        ate.idSede as value,
        sd.nsede as label
        FROM ". $this->schema_cm .".atencionxsede ate
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sd ON sd.idsede = ate.idSede
        WHERE ate.idEspecialista=$idEspecialista AND ate.tipoCita=1 AND ate.estatus = 1";

        $sedes = $this->ch->query($query)->result_array();

        return $sedes;
    }

    public function addHorarioPresencial($presencialDate, $idSede, $idEspecialista){

        $query = "INSERT IGNORE INTO ". $this->schema_cm .".presencialxsede (presencialDate, idSede, idEspecialista)
        SELECT '$presencialDate', '$idSede', '$idEspecialista'
        FROM dual
        WHERE NOT EXISTS (
            SELECT * 
            FROM ". $this->schema_cm .".presencialxsede 
            WHERE presencialDate = '$presencialDate' AND idEspecialista = '$idEspecialista'
        );";

        return $this->ch->query($query);
    }

    public function checkExist($presencialDate, $idSede, $idEspecialista){
        $query = $this->ch->query("SELECT *FROM ". $this->schema_cm .".presencialxsede 
            WHERE presencialDate = '$presencialDate' AND idEspecialista = '$idEspecialista'");

        return $query;
    }

    public function deleteHorarioPresencial($presencialDate, $idSede, $idEspecialista){

        $query = "DELETE FROM ". $this->schema_cm .".presencialxsede
        WHERE presencialDate = '$presencialDate' AND idEspecialista = '$idEspecialista'";

        return $this->ch->query($query);
    }

    public function getHorariosEspecialista($idEspecialista){

        $query = "SELECT
        pxs.idEvento AS id_horario,
        pxs.presencialDate AS 'start',
        pxs.presencialDate AS 'end',
        pxs.idSede AS sede,
        pxs.idEspecialista AS especialista,
        sd.nsede AS title,
        'background' AS display,
        ds.colorBack AS backgroundColor
        FROM ". $this->schema_cm .".presencialxsede pxs
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sd ON sd.idsede = pxs.idSede
        LEFT JOIN ". $this->schema_cm .".datosede AS ds ON ds.idSede = sd.idsede
        WHERE pxs.idEspecialista='$idEspecialista'";

        $horaios = $this->ch->query($query)->result_array();

        return $horaios;
    }

    public function getDiasPresencialXEspe($idSede, $idEspecialista){

        $query = "SELECT * FROM ". $this->schema_cm .".presencialxsede
        WHERE idEspecialista='$idEspecialista' AND idSede='$idSede'";

        $dias = $this->ch->query($query)->result_array();

        return $dias;
    }
}