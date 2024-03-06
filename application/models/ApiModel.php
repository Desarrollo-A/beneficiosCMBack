<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiModel extends CI_Model
{
    public function confirmarPago($fechaInicio, $fechaFin, $idUsuario){
        $query = $this->db->query(
            "SELECT idOcupado as id, titulo as title, fechaInicio as occupied, fechaInicio, fechaFinal FROM horariosOcupados
            WHERE idEspecialista = ? AND estatus = ?  AND
            ((fechaInicio BETWEEN ? AND ?) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?));",
            array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );
        return $query;
    }
}