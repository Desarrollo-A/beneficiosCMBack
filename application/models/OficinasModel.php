<?php

class OficinasModel extends CI_Model {
    public function __construct(){
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getOficinas($idsede){
         $query = "SELECT * FROM ". $this->schema_ch .".beneficioscm_vista_oficinas WHERE idsede = $idsede ORDER BY noficina";

        return $this->ch->query($query)->result();
    }
}
?>