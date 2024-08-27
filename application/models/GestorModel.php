<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GestorModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
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
        FROM ". $this->schema_cm .".usuarios us
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
        CASE 
        WHEN axs.idOficina = 0 THEN 'VIRTUAL' 
        WHEN axs.idOficina <> 0 THEN o.noficina 
        END AS oficina,o.direccion AS ubicación, CONCAT(us2.nombre_persona,' ',us2.pri_apellido,' ',us2.sec_apellido) AS nombre,
        ps.idpuesto AS idPuesto, ps.nom_puesto As puesto, op.nombre AS modalidad, axs.estatus,
        IFNULL(ar.narea, 'SIN ÁREA') AS nombreArea
        FROM ". $this->schema_cm .".atencionxsede axs
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas o ON o.idoficina = axs.idOficina
        LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = axs.idEspecialista
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto
        LEFT JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 5
        LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = axs.idArea 
        WHERE us2.idpuesto = $dt");
        return $query->result();
        
    }

    public function checkAxs($dt, $idArea){
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede WHERE estatus = 1 AND idEspecialista = ? AND idSede = ? AND (idArea = ? OR idArea IS NULL) AND tipoCita = ?",
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
        
        $query = $this->ch->query("SELECT *from ". $this->schema_cm .".atencionxsede where estatus = 1 AND idEspecialista = ? AND idSede = ? AND (idArea IS NULL OR idArea IS NOT NULL) AND tipoCita = ?", 
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

    public function getHorariosEspecificos(){
        
        $query = $this->ch->query(
                "SELECT idHorario, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista,
                us2.npuesto AS beneficio, CONCAT(TIME_FORMAT(he.horaInicio, '%H:%i'), ' - ', TIME_FORMAT(he.horaFin, '%H:%i')) AS horario,
                IFNULL (CONCAT(TIME_FORMAT(he.horaInicioSabado, '%H:%i'), ' - ', TIME_FORMAT(he.horaFinSabado, '%H:%i')), 'SIN HORARIO') AS horarioSabado,
                he.estatus, he.horaInicio, he.horaFin, he.sabados, he.horaInicioSabado, he.horaFinSabado
                FROM ". $this->schema_cm .".horariosespecificos he
                INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = he.idEspecialista 
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato");

        return $query;
    }

    public function especialistas(){
        $query = $this->ch-> query("SELECT us.idUsuario, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, ''), ' (', IFNULL(us2.npuesto , ''), ')') AS especialista 
        FROM ". $this->schema_cm .".usuarios us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
        WHERE us.idRol  = 3
        AND NOT EXISTS (
            SELECT 1 
            FROM ". $this->schema_cm .".horariosespecificos he 
            WHERE he.idEspecialista = us.idUsuario
        )
        ORDER BY us2.nombre_persona ASC");
        return $query->result();
    }

    public function insertHorario($dt)
    {
        $data = json_decode($dt, true);

        if (isset($data)) {

                $espe = $data["espe"];
                $horaInicio = $data["horaInicio"];
                $horaFin = $data["horaFin"];
                $sabado = $data["sabado"];
                $horaInicioSabado = $data["horaInicioSabado"];
                $horaFinSabado = $data["horaFinSabado"];
                $creadoPor = $data["creadoPor"];

				if (empty($espe) ||
                    empty($horaInicio) ||
                    empty($horaFin) ||
                    empty($creadoPor) ||
                    ($sabado == 1 && 
                    ($horaInicioSabado == null || 
                    $horaFinSabado == null))) {

					echo json_encode(array("estatus" => false, "msj" => "Faltan datos!" ));

				}else if(($horaInicio > $horaFin) || ($sabado == 1 && ($horaInicioSabado >  $horaFinSabado)))
                {
                    echo json_encode(array("estatus" => false, "msj" => "La fecha de inicio no puede ser mayor a la final" ));
                }
                else{

                    $this->db->query("INSERT INTO ". $this->schema_cm .".horariosespecificos (idEspecialista, horaInicio, horaFin, sabados, horaInicioSabado, horaFinSabado, creadoPor ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)", 
                    array($espe, $horaInicio, $horaFin, $sabado, $horaInicioSabado, $horaFinSabado, $creadoPor));
                    
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        echo "Error al realizar el registro";
                    } else {
                        echo json_encode(array("estatus" => true, "msj" => "Registro realizado exitosamente" ));
                    }
                
                }

        } else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function getDepartamentos(){
        $query = $this->ch-> query("SELECT dep.iddepto AS id, dep.ndepto AS departamento,
        CASE 
            WHEN SUM(CASE WHEN dp.canRegister = 1 THEN 1 ELSE 0 END) > 0 THEN 1
            ELSE 0
        END AS estatus
        FROM ". $this->schema_ch .".beneficioscm_vista_departamento dep
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.iddepto = dep.iddepto 
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
        LEFT JOIN ". $this->schema_cm .".datopuesto dp ON dp.idPuesto = ps.idpuesto
        GROUP BY dep.iddepto ,dep.ndepto
        ORDER BY dep.ndepto ASC");
        return $query->result();
    }

    public function getAreasPs($idDpto){
        $query = $this->ch-> query("SELECT ar.idsubarea AS id, ar.narea AS area,
        CASE 
            WHEN SUM(CASE WHEN dp.canRegister = 1 THEN 1 ELSE 0 END) > 0 THEN 1
            ELSE 0
        END AS estatus
        FROM ". $this->schema_ch .".beneficioscm_vista_area ar 
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idarea = ar.idsubarea 
        LEFT JOIN ". $this->schema_cm .".datopuesto dp ON dp.idPuesto = ps.idpuesto 
        WHERE ar.iddepto = $idDpto
        GROUP BY ar.idsubarea, ar.narea
        ORDER BY ar.narea ASC");
        return $query->result();
    }

    public function getPuestos($idArea){
        $query = $this->ch-> query("SELECT ps.idpuesto AS id, ps.nom_puesto AS puesto,
        CASE 
            WHEN dp.canRegister IS NULL THEN 0
            ELSE dp.canRegister 
        END AS estatus
        FROM ". $this->schema_ch .".beneficioscm_vista_puestos ps
        LEFT JOIN ". $this->schema_cm .".datopuesto dp ON dp.idPuesto = ps.idpuesto 
        WHERE ps.idarea = $idArea 
        ORDER BY ps.nom_puesto ASC");
        return $query->result();
    }

    public function getUsuarios(){
        $query = $this->ch-> query("SELECT
        us.idUsuario AS id,
        IFNULL(us2.num_empleado, 'NO APLICA') AS numEmpleado,
        CONCAT(IFNULL(us2.nombre_persona, us3.nombre), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
        IFNULL(c.correo, us3.correo) AS correo,
        us.estatus,
        us.idcontrato AS contrato,
        us.password,
        IFNULL(us2.nsede, 'NO APLICA') AS sede,
        IFNULL(us2.ndepto, 'NO APLICA') AS departamento,
        IFNULL(us2.narea, 'NO APLICA') AS area,
        IFNULL(us2.npuesto, 'NO APLICA') AS puesto,
        CASE
            WHEN us.idRol = 2 AND us.externo = 0 THEN 'Beneficiario'
            WHEN us.idRol = 2 AND us.externo = 1 THEN 'Externo'
            WHEN us.idRol = 3 THEN 'Especialista'
            WHEN us.idRol = 4 THEN 'Administrador'
        END AS rol,
        TRIM(BOTH ', ' FROM CONCAT_WS(', ', 
            CASE WHEN dt.estatusNut = 1 THEN 'Nutrición' END,
            CASE WHEN dt.estatusPsi = 1 THEN 'Psicología' END,
            CASE WHEN dt.estatusGE = 1 THEN 'Guía espiritual' END
        )) AS servicios,
        DATE_FORMAT(us.fechaCreacion, '%Y-%m-%d') AS fechaCreacion,
        permisos.idOpcion as permisos_id,
        permisos.nombre as permisos_name
        FROM ". $this->schema_cm .".usuarios us
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        LEFT JOIN ". $this->schema_cm .".correostemporales AS c ON c.idContrato = us.idContrato
        LEFT JOIN ". $this->schema_cm .".usuariosexternos us3 ON us3.idcontrato = us.idContrato
        LEFT JOIN ". $this->schema_cm .".detallepaciente dt ON dt.idUsuario = us.idUsuario
        LEFT JOIN opcionesporcatalogo permisos ON permisos.idCatalogo = 3 AND permisos.idOpcion = us.permisos
        WHERE us.idUsuario != 1
        GROUP BY
            us.idUsuario,
            us2.num_empleado,
            us2.nombre_persona,
            us3.nombre,
            us2.pri_apellido,
            us2.sec_apellido,
            c.correo,
            us.estatus,
            us.idcontrato,
            us.password,
            us2.nsede,
            us2.iddepto,
            us2.ndepto,
            us2.idarea,
            us2.narea,
            us2.idpuesto,
            us2.npuesto,
            us.idRol,
            us.externo
        ORDER BY IFNULL(us2.nombre_persona, us3.nombre) ASC");

        return $query->result();
    }

    public function getAllAreas(){
		$query = $this->ch-> query("SELECT ar.idsubarea AS id, ar.narea AS area 
		FROM ". $this->schema_ch .".beneficioscm_vista_puestos ps 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = ps.idarea 
		GROUP BY ar.idsubarea
		ORDER BY ar.narea ASC");
        return $query;
	}

    public function updatePermisosUsuarios($idUsuario, $permisos){
        $query = "UPDATE usuarios
        SET
            permisos = $permisos
        WHERE
            idUsuario=$idUsuario";

        return $this->ch->query($query);
    }
}
