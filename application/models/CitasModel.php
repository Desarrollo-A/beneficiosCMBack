<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CitasModel extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }

    public function cancelFromUser($idUsuario){
        /* $query = "UPDATE citas
            SET
                estatusCita=2
            WHERE
                idPaciente=$idUsuario"; */

        $query = "UPDATE PRUEBA_beneficiosCM.citas
        SET estatusCita=2
        WHERE idPaciente=$idUsuario";

        return $this->ch->query($query);
    }

    public function getCitasPendientes($idEspecialista, $idSede, $fechaInicio, $fechaFinal){
        /* $query = "SELECT *
            FROM citas
            LEFT JOIN atencionXSede ON citas.idAtencionXSede = atencionXSede.idAtencionXSede
            WHERE
                citas.idEspecialista='$idEspecialista'
            AND citas.estatusCita IN (1)
            AND NOT atencionXSede.idSede = '$idSede'
            AND citas.fechaInicio BETWEEN '$fechaInicio' AND '$fechaFinal'"; */

            $query = "SELECT *
            FROM PRUEBA_beneficiosCM.citas as ct
            LEFT JOIN atencionxsede as axs ON ct.idAtencionXSede = axs.idAtencionXSede
            WHERE ct.idEspecialista='$idEspecialista'
            AND ct.estatusCita IN (1)
            AND NOT axs.idSede = '$idSede'
            AND ct.fechaInicio BETWEEN '$fechaInicio' AND '$fechaFinal'";

        return $this->ch->query($query)->result_array();
    }
}