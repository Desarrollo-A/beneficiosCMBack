<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GestorModel extends CI_Model {
	public function __construct()
	{
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}


    public function getOficinasVal($dt)
    {

        $query = $this->ch-> query("SELECT idoficina AS idOficina, noficina AS oficina,
        CONCAT(noficina,' (', IFNULL(direccion, 'SIN DIRECCIÓN'),') ') AS lugar
        FROM  ". $this->schema_ch .".beneficioscm_vista_oficinas WHERE idsede =  $dt OR idsede =  0");
        
        if($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getEspecialistasVal($dt)
    {

        $idSede = $dt["idSd"];
        $idPuesto = $dt["idPs"];

        $query = $this->ch-> query("SELECT us.idUsuario, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre
        FROM usuarios us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        WHERE idRol = 3 AND idPuesto = $idPuesto");

        if($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getEsp($dt)
    {

        $query = $this->ch-> query("SELECT us.idUsuario, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre
        FROM ". $this->schema_cm .".usuarios us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        WHERE us.idRol = 3 AND us2.idpuesto = $dt");
        return $query->result();
    }

    public function getSedeNone($dt)
    {
            $query = $this->ch-> query("SELECT  sedes.idsede AS idSede, sedes.nsede AS sede, sedes.estatus_sede AS estatus
            FROM ". $this->schema_ch .".beneficioscm_vista_sedes AS sedes
            WHERE sedes.idsede NOT IN
                (SELECT axs.idSede
                FROM ". $this->schema_cm .".atencionxsede AS axs
                INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = axs.idEspecialista
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usCh ON usCh.idContrato = us.idContrato
                WHERE usCh.idpuesto = $dt)");
        
        return $query->result();
        
    }

    public function getSedeNoneEsp($dt)
    {

        $query = $this->ch-> query("SELECT  sedes.idsede AS idSede, sedes.nsede AS sede, sedes.estatus_sede AS estatus
        FROM ". $this->schema_ch .".beneficioscm_vista_sedes AS sedes
        WHERE sedes.idsede NOT IN
        (SELECT axs.idSede
        FROM ". $this->schema_cm .".atencionxsede AS axs
        INNER JOIN ". $this->schema_cm .".usuarios AS us ON us.idUsuario = axs.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS usCh ON usCh.idContrato = us.idContrato
        WHERE usCh.idpuesto = $dt)");
        
        return $query->result();
        
    }

    public function getAtencionXsedeEsp($dt)
    {
        
        $query = $this->ch-> query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.nsede AS sede, o.noficina AS oficina, 
        o.direccion AS ubicación, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,
        ps.idpuesto AS idPuesto, ps.nom_puesto As puesto, op.nombre AS modalidad, axs.estatus,
        IFNULL(ar.narea, 'SIN ÁREA') AS nombreArea
        FROM ". $this->schema_cm .".atencionxsede axs
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas o ON o.idoficina = axs.idOficina
        INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
        INNER JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 5
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = axs.idArea 
        WHERE us2.idpuesto = $dt");
        return $query->result();
        
    }

    public function checkAxs($dt, $idArea){
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND idSede = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
        array($dt["especialista"], $dt["sede"], $idArea, $dt["modalidad"]));

        return $query;
    }

    
    public function checkAxsId($dt, $idArea, $idAts){
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND idSede = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ? AND idAtencionXSede != ?",
        array($dt["especialista"], $dt["sede"], $idArea, $dt["modalidad"], $idAts));

        return $query;
    }

    public function checkAxsArea($dt, $idAts){

        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede 
        where idEspecialista = ? AND idSede = ? AND tipoCita = ? AND idAtencionXSede != ? AND idArea IS NOT NULL",
        array($dt["especialista"], $dt["sede"], $dt["modalidad"], $idAts));

        return $query;
    }

    public function checkAxsNull($dt){
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND idSede = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["modalidad"]));

        return $query;
    }

    public function checkAxsMod($dt){
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND idSede = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["modalidad"]));

        return $query;
    }

    public function getAxs($idAts){
        
        $query = $this->ch->query("SELECT * FROM ". $this->schema_cm .".atencionxsede WHERE idAtencionXSede = ?", $idAts);
        return $query;
    }

    public function checkModalidadesNull($dataValue){
        
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["modalidad"])
        );

        return $query;
    }

    public function checkModalidades($dataValue){
        
        $query = $this->ch->query(
            "SELECT *FROM ". $this->schema_cm .".atencionxsede where idEspecialista = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["idArea"], $dataValue["modalidad"])
        );

        return $query;
    }

    public function getAreas(){
        
        $query = $this->ch->query(
                "SELECT idsubarea AS idArea, narea AS area, AR.iddepto, CONCAT(narea, ' ', '(', DE.ndepto, ')') AS nombre 
                FROM ". $this->schema_ch .".beneficioscm_vista_area AR 
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_departamento DE ON DE.iddepto = AR.iddepto  
                UNION ALL
                SELECT 0 AS idsubarea, 'Sin área' AS area, NULL AS iddepto, 'SIN ÁREA' AS nombre
                ORDER BY idArea");

        return $query;
    }
}
