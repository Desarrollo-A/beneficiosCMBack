<?php

class ModalidadesModel extends CI_Model {
    public function __construct(){
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getModalidades(){
         $query = "SELECT * FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 5";

        return $this->ch->query($query)->result();
    }
}
?>