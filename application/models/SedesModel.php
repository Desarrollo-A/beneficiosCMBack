<?php

class SedesModel extends CI_Model {
    public function __construct(){
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function getPresencialXEspecialista($idEspecialista){
        /* $query = "SELECT
            ate.idSede as value,
            sedes.sede as label
        FROM atencionXSede ate
        LEFT JOIN sedes ON sedes.idSede=ate.idSede
        WHERE
            ate.idEspecialista=$idEspecialista AND
            ate.tipoCita=1"; */

        $query = "SELECT
        ate.idSede as value,
        sd.nsede as label
        FROM ". $this->schema_cm .".atencionxsede ate
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes AS sd ON sd.idsede = ate.idSede
        WHERE
        ate.idEspecialista=$idEspecialista AND
        ate.tipoCita=1";

        $sedes = $this->ch->query($query)->result_array();

        return $sedes;
    }

    public function addHorarioPresencial($presencialDate, $idSede, $idEspecialista){
        /* $query = "BEGIN
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
        END"; */

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

    public function deleteHorarioPresencial($presencialDate, $idSede, $idEspecialista){
        /* $query = "DELETE FROM presencialXSede
            WHERE
                presencialDate = '$presencialDate'
            AND idEspecialista = '$idEspecialista'"; */

        $query = "DELETE FROM ". $this->schema_cm .".presencialxsede
        WHERE presencialDate = '$presencialDate' AND idEspecialista = '$idEspecialista'";

        return $this->ch->query($query);
    }

    public function getHorariosEspecialista($idEspecialista){
        /* $query = "SELECT
        presencialXSede.idEvento AS id_horario,
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
            presencialXSede.idEspecialista='$idEspecialista'"; */

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
        /* $query = "SELECT * FROM presencialXSede
            WHERE
                idEspecialista='$idEspecialista'
            AND idSede='$idSede'"; */

        $query = "SELECT * FROM ". $this->schema_cm .".presencialxsede
        WHERE idEspecialista='$idEspecialista' AND idSede='$idSede'";

        $dias = $this->ch->query($query)->result_array();

        return $dias;
    }
}