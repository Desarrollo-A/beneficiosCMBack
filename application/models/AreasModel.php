<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AreasModel extends CI_Model{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getAreas(){
    	$query = $this->ch->query("SELECT
            area.*,
            es.*
        FROM ". $this->schema_cm .".areasbeneficios area
        LEFT JOIN (SELECT 
                    idAreaBeneficio, 
			        COUNT(idAreaBeneficio) AS especialistas 
				    FROM ". $this->schema_cm .".usuarios us 
				    INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios_dos rhus ON us.idContrato = rhus.idContrato AND rhus.activo = 1 GROUP BY idAreaBeneficio) es ON es.idAreaBeneficio = area.idAreaBeneficio 
        WHERE area.estatus = 1");

        return $query->result();
    }
}

?>