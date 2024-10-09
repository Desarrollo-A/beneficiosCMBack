<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CatalogosModel extends CI_Model
{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }
    
    public function getCatalogos()
    {
        $query = $this->ch->query("SELECT idCatalogo,nombre, 
        CASE WHEN estatus = 1 THEN 'Activo'  WHEN estatus = 0 THEN 'Inactivo' ELSE 'ERR-Check Database' END AS estatus
        FROM " . $this->schema_cm . ".catalogos");
        return $query->result();
    }
 
    public function getCatalogosOp($idCatalogo) 
    {
        $query = $this->ch->query(" SELECT idOpcion,idCatalogo, nombre, 
        CASE WHEN estatus = 1 THEN 'Activo'  WHEN estatus = 0 THEN 'Inactivo' ELSE 'ERR-Check Database' END AS estatus 
        FROM " . $this->schema_cm . ".opcionesporcatalogo  WHERE idCatalogo = ?", $idCatalogo);
        return $query->result();
    } 
    
    public function updateEstatusOp($idOpcion, $idCatalogo, $estatusOp, $idUsuario)
    {
        $sql = "UPDATE {$this->schema_cm}.opcionesporcatalogo 
                SET estatus = ?, 
                    modificadoPor = ?, 
                    fechaModificacion = NOW() 
                WHERE idCatalogo = ? 
                  AND idOpcion = ?";
    
        $query = $this->ch->query($sql, [$estatusOp, $idUsuario, $idCatalogo, $idOpcion]);
        return $query;
    }

}    