<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AyudaModel extends CI_Model{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getFaqs(){
        $query = $this->ch->query(
            "SELECT *, idFaqs AS id FROM ". $this->schema_cm .".faqs"
        );

        return $query->result();
    }

    public function getManuales(){
        $query = $this->ch->query(
            "SELECT * FROM ". $this->schema_cm .".manuales"
        );

        return $query->result();
    }
}