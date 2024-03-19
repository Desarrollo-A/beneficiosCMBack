<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GestorModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}


    public function getOficinasVal($dt)
    {
        /* $query = $this->db-> query("SELECT idOficina, oficina 
        FROM oficinas WHERE idSede =  $dt OR idSede =  0"); */

        $query = $this->ch-> query("SELECT idoficina AS idOficina, noficina AS oficina 
        FROM  PRUEBA_CH.beneficioscm_vista_oficinas WHERE idsede =  $dt OR idsede =  0");
        
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

        /* $query = $this->db-> query("SELECT idUsuario, nombre FROM usuarios WHERE idRol = 3 AND idPuesto = $idPuesto"); */
        
        $query = $this->ch-> query("SELECT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre
        FROM usuarios us
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
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

        /* $query = $this->db-> query("SELECT idUsuario, nombre 
        FROM usuarios WHERE idRol = 3 AND idPuesto = $dt"); */
        
        $query = $this->ch-> query("SELECT us.idUsuario, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',
        us2.sec_apellido) AS nombre
        FROM PRUEBA_beneficiosCM.usuarios us
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        WHERE us.idRol = 3 AND us2.idpuesto = $dt");
        return $query->result();
    }

    public function getSedeNone($dt)
    {
        /* $query = $this->db-> query("SELECT  *
        FROM sedes
        WHERE idSede NOT IN 
            (SELECT axs.idSede 
            FROM atencionXSede axs
            INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
            INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
            WHERE ps.idPuesto = $dt)"); */

            $query = $this->ch-> query("SELECT  sedes.idsede AS idSede, sedes.nsede AS sede, sedes.estatus_sede AS estatus
            FROM PRUEBA_CH.beneficioscm_vista_sedes AS sedes
            WHERE sedes.idsede NOT IN
                (SELECT axs.idSede
                FROM PRUEBA_beneficiosCM.atencionxsede AS axs
                INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = axs.idEspecialista
                INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usCh ON usCh.idContrato = us.idContrato
                WHERE usCh.idpuesto = $dt)");
        
        return $query->result();
        
    }

    public function getSedeNoneEsp($dt)
    {
        /* $query = $this->db-> query("SELECT  *
        FROM sedes
        WHERE idSede NOT IN 
            (SELECT axs.idSede 
            FROM atencionXSede axs
            INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
            INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
            WHERE ps.idPuesto = $dt)"); */

        $query = $this->ch-> query("SELECT  sedes.idsede AS idSede, sedes.nsede AS sede, sedes.estatus_sede AS estatus
        FROM PRUEBA_CH.beneficioscm_vista_sedes AS sedes
        WHERE sedes.idsede NOT IN
        (SELECT axs.idSede
        FROM PRUEBA_beneficiosCM.atencionxsede AS axs
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = axs.idEspecialista
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usCh ON usCh.idContrato = us.idContrato
        WHERE usCh.idpuesto = $dt)");
        
        return $query->result();
        
    }

    public function getAtencionXsedeEsp($dt)
    {
        /* $query = $this->db-> query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.sede, o.oficina, 
        o.ubicación, us.nombre, ps.idPuesto, ps.puesto, op.nombre AS modalidad, axs.estatus
        FROM atencionXSede axs
        INNER JOIN sedes sd ON sd.idSede = axs.idSede
        INNER JOIN oficinas o ON o.idOficina = axs.idOficina
        INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
        INNER JOIN catalogos ct ON ct.idCatalogo = 5
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita
		WHERE us.idPuesto = $dt"); */
        
        $query = $this->ch-> query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.nsede AS sede, o.noficina AS oficina, 
        o.direccion AS ubicación, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,
        ps.idpuesto AS idPuesto, ps.nom_puesto As puesto, op.nombre AS modalidad, axs.estatus
        FROM PRUEBA_beneficiosCM.atencionxsede axs
        INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
        INNER JOIN PRUEBA_CH.beneficioscm_vista_oficinas o ON o.idoficina = axs.idOficina
        INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        INNER JOIN PRUEBA_CH.beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
        INNER JOIN PRUEBA_beneficiosCM.catalogos ct ON ct.idCatalogo = 5
        INNER JOIN PRUEBA_beneficiosCM.opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita
        WHERE us2.idpuesto = $dt");
        return $query->result();
        
    }

    public function checkAxs($dt, $idArea){
        /* $query = $this->db->query("SELECT *from atencionXSede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $idArea, $dt["modalidad"])); */

        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $idArea, $dt["modalidad"]));

        return $query;
    }

    
    public function checkAxsId($dt, $idArea, $idAts){
        /* $query = $this->db->query("SELECT *from atencionXSede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ? AND idAtencionXSede != ?",
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $idArea, $dt["modalidad"], $idAts)); */

        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ? AND idAtencionXSede != ?",
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $idArea, $dt["modalidad"], $idAts));

        return $query;
    }

    public function checkAxsArea($dt, $idAts){

        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.atencionxsede 
        where idEspecialista = ? AND idSede = ? AND idOficina = ? AND tipoCita = ? AND idAtencionXSede != ? AND idArea IS NOT NULL",
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"], $idAts));

        return $query;
    }

    public function checkAxsNull($dt){
        /* $query = $this->db->query("SELECT *from atencionXSede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"])); */

        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"]));

        return $query;
    }

    public function checkAxsMod($dt){
        /* $query = $this->db->query("SELECT *from atencionXSede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"])); */

        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"]));

        return $query;
    }

    public function getAxs($idAts){
        /* $query = $this->db->query("SELECT *from atencionXSede WHERE idAtencionXSede = ?", $idAts); */

        $query = $this->ch->query("SELECT * FROM PRUEBA_beneficiosCM.atencionxsede WHERE idAtencionXSede = ?", $idAts);
        return $query;
    }

    public function checkModalidadesNull($dataValue){
        /* $query = $this->db->query(
            "SELECT *FROM atencionXSede where idEspecialista = ? AND idOficina = ? AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["idOficina"], $dataValue["modalidad"])
        ); */

        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idOficina = ? AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["idOficina"], $dataValue["modalidad"])
        );

        return $query;
    }

    public function checkModalidades($dataValue){
        /* $query = $this->db->query(
            "SELECT *FROM atencionXSede where idEspecialista = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["idOficina"], $dataValue["idArea"], $dataValue["modalidad"])
        ); */

        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.atencionxsede where idEspecialista = ? AND idOficina = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
            array($dataValue["idEspecialista"], $dataValue["idOficina"], $dataValue["idArea"], $dataValue["modalidad"])
        );

        return $query;
    }

    public function getAreas(){
        /* $query = $this->db->query(
            "SELECT idArea, area, AR.idDepto, CONCAT(area, ' ', '(', DE.depto, ')') as nombre 
            FROM areas AR 
            INNER JOIN departamentos DE ON DE.idDepto = AR.idDepto 
            UNION ALL
            SELECT 0 as idArea, 'Sin área' as area, NULL as idDepto, 'SIN ÁREA' as nombre
            ORDER BY idArea"); */

            $query = $this->ch->query(
                "SELECT idsubarea AS idArea, narea AS area, AR.iddepto, CONCAT(narea, ' ', '(', DE.ndepto, ')') AS nombre 
                FROM PRUEBA_CH.beneficioscm_vista_area AR 
                INNER JOIN PRUEBA_CH.beneficioscm_vista_departamento DE ON DE.iddepto = AR.iddepto  
                UNION ALL
                SELECT 0 AS idsubarea, 'Sin área' AS area, NULL AS iddepto, 'SIN ÁREA' AS nombre
                ORDER BY idArea");

        return $query;
    }
}