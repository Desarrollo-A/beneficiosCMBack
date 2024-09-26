<?php

class DepartamentosModel extends CI_Model {
    public function __construct(){
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getDepartamentos(){
        $query = "SELECT * FROM ". $this->schema_ch .".beneficioscm_vista_departamento WHERE estatus_depto = 1;";

        return $this->ch->query($query)->result();
    }

}