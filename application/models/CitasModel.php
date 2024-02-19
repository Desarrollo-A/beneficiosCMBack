<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CitasModel extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }

    public function cancelFromUser($idUsuario){
        $query = "UPDATE citas
            SET
                estatusCita=2
            WHERE
                idPaciente=$idUsuario";

        return $this->db->query($query);
    }
}