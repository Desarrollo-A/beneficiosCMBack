<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GeneralModel extends CI_Model {
	public function __construct()
	{
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
	}

   /*  public function usuarios()
	{
		$query = $this->db-> query("SELECT * FROM usuarios");
		return $query->result();
	} */

    public function usuarioExiste($idContrato){
        /* $query = $this->db-> query("SELECT * FROM usuarios WHERE idContrato = ?", $idContrato); */

        $query = $this->ch-> query("SELECT * FROM ". $this->schema_cm .".usuarios WHERE idContrato = ?", $idContrato);
		return $query;
    }

    public function getInfoPuesto($contrato){
        /* $query = $this->db-> query("SELECT *FROM puestos WHERE idPuesto = ?", $puesto); */

        $query = $this->ch-> query("SELECT ps.idpuesto AS idPuesto, ps.nom_puesto AS puesto, ps.tipo_puesto AS tipoPuesto,  
        ps.idarea AS idArea, ps.estatus_puesto AS estatus, dp.canRegister 
        FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us.idpuesto
        LEFT JOIN datopuesto dp ON dp.idPuesto = ps.idpuesto 
        WHERE us.idcontrato = ?", $contrato);
        return $query;
    }

    /* public function usrCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [usuarios] FROM usuarios");
		return $query->result();
	} */

    /* public function citasCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM citas");
		return $query->result();
	} */

    public function especialistas()
	{
		/* $query = $this->db-> query("SELECT idPuesto, puesto AS nombre FROM puestos WHERE idPuesto = 537 OR idPuesto = 686 OR idPuesto = 158 OR idPuesto = 585"); */
		$query = $this->ch-> query("SELECT idpuesto AS idPuesto, nom_puesto AS nombre FROM ". $this->schema_ch .".beneficioscm_vista_puestos WHERE idpuesto = 537 OR idpuesto = 686 OR idpuesto = 158 OR idpuesto = 585");
        return $query->result();
	}

    // MJ: AGREGA UN REGISTRO A UNA TABLA EN PARTICULAR, RECIBE 2 PARÁMETROS. LA TABLA Y LA DATA A INSERTAR
    public function addRecord($table, $data) { 
        /* return $this->db->insert($table, $data); */
        return $this->ch->insert($table, $data);
    }

    // MJ: ACTUALIZA LA INFORMACIÓN DE UN REGISTRO EN PARTICULAR, RECIBE 4 PARÁMETROS. TABLA, DATA A ACTUALIZAR, LLAVE (WHERE) Y EL VALOR DE LA LLAVE
    public function updateRecord($table, $data, $key, $value) { 
        return $this->ch->update($table, $data, "$key = '$value'");
    }

    public function insertBatch($table, $data)
    {
        $this->db->trans_begin();
        $this->db->insert_batch($table, $data);
        if (!$this->db->trans_status())  { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->db->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->db->trans_commit();
        }
    }

    public function updateBatch($table, $data, $key)
    {
        $this->db->trans_begin();
        $this->db->update_batch($table, $data, $key);
        if (!$this->db->trans_status()) { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->db->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->db->trans_commit();
        }
    }

    /* public function getPuesto($dt)
    {
        $query = $this->db-> query("SELECT pu.puesto
        FROM usuarios us
        INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
        WHERE idUsuario = $dt");

		return $query;
    } */

    /* public function getSede($dt)
    {
        $query = $this->db-> query("SELECT se.sede
        FROM usuarios us
        INNER JOIN sedes se ON se.idSede = us.idSede
        WHERE idUsuario = $dt");
		return $query;
    } */

    public function getPacientes($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 1 || $idRol == 4){
            if( $slEs == 0){
                /* $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [pacientes] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')"); */

                $query = $this->ch->query("SELECT COUNT(DISTINCT ct.idPaciente) AS `pacientes` FROM ". $this->schema_cm .".usuarios us
                INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
                WHERE us2.idpuesto = $idData AND ct.estatusCita = 4 AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");


            }else if( $slEs != 0 ){

                /* $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [pacientes] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND ct.idEspecialista = $slEs AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')"); */

                $query = $this->ch-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS `pacientes` FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
                WHERE us2.idpuesto = 158 AND ct.estatusCita = 4 AND ct.idEspecialista = $slEs AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
            }

        }else if($idRol == 2){
            /* $query = $this->db-> query("SELECT COUNT(*) AS [pacientes] FROM citas WHERE idPaciente = $idUser AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')"); */

            $query = $this->ch-> query("SELECT COUNT(*) AS `pacientes` FROM citas WHERE idPaciente = $idUser AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }else if($idRol == 3){
            /* $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS [pacientes] FROM citas WHERE idEspecialista = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')"); */

            $query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS `pacientes` FROM citas WHERE idEspecialista = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }
        
        return $query;
    }

    /* public function getCtAsistidas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 1 || $idRol == 4){

            if( $slEs == 0){

            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [asistencia] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }else if( $slEs != 0 ){
            
            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [asistencia] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND ct.idEspecialista = $slEs AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }

        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [asistencia] FROM citas WHERE idPaciente = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS [asistencia] FROM citas WHERE idEspecialista = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }

        return $query;
    }

    public function getCtCanceladas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 1 || $idRol == 4){

            if( $slEs == 0){

            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [cancelada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $idData AND ct.estatusCita = 2 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }else if( $slEs != 0 ){
                        
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [cancelada] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 2 AND ct.idEspecialista = $slEs AND 
			    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }

        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [cancelada] FROM citas WHERE idPaciente = $idUser AND estatusCita = 2
            AND (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS [cancelada] FROM citas WHERE idEspecialista = $idUser AND estatusCita = 2
            AND (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }

        return $query;
    }

    public function getCtPenalizadas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 1 || $idRol == 4){

            if( $slEs == 0){

            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [penalizada] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $idData AND ct.estatusCita = 3 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }else if( $slEs != 0 ){
                        
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [penalizada] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 3 AND ct.idEspecialista = $slEs
                AND 
			    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }

        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [penalizada] FROM citas WHERE idPaciente = $idUser AND estatusCita = 3
            AND (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS [penalizada] FROM citas WHERE idEspecialista = $idUser AND estatusCita = 3
            AND (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");
        }

        return $query;
    } */

    /* public function getCtVirtuales($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 4){

            if( $slEs == 0){

            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [virtual] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE us.idPuesto = $idData AND axs.tipoCita = 2 AND 
		    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }else if( $slEs != 0 ){
                        
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [virtual] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
                INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
                WHERE us.idPuesto = $idData AND axs.tipoCita = 2 AND ct.idEspecialista = $slEs AND 
			    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }

        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [virtual] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE ct.idPaciente = $idUser AND axs.tipoCita = 2 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [virtual] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE ct.idEspecialista = $idUser AND axs.tipoCita = 2 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
        }

        return $query;
    }

    public function getCtPresenciales($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $slEs = $dt["slEs"];
        $idUser = $dt["idUser"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($idRol == 4){

            if( $slEs == 0){
                
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [presencial] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
                INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
                WHERE us.idPuesto = $idData AND axs.tipoCita = 1 AND 
			    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

            }else if( $slEs != 0 ){
                        
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [presencial] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
                INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
                WHERE us.idPuesto = $idData AND axs.tipoCita = 1 AND ct.idEspecialista = $slEs AND 
			    (ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");

                }
        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [presencial] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE ct.idPaciente = $idUser AND axs.tipoCita = 1 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [presencial] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE ct.idEspecialista = $idUser AND axs.tipoCita = 1 AND 
			(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
        }

        return $query;
    } */

    public function getAppointmentHistory($dt){

        $idUsuario = $dt["idUser"];
        $idRol = $dt["idRol"];
        $idEspe = $dt["idEspe"];
        $espe = $dt["espe"];

        if($idRol == 1  || $idRol == 4){

        /* $query = $this->db->query("SELECT us.nombre, es.nombre AS especialista, ct.idPaciente, ct.titulo, oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
		CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario,
        ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita
		FROM citas ct 
		INNER JOIN catalogos ca ON ca.idCatalogo = 2
		INNER JOIN opcionesPorCatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
		INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
		INNER JOIN usuarios es ON es.idUsuario = ct.idEspecialista
        LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
		LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
		LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
		LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN es.idPuesto = 537 THEN 8
			WHEN es.idPuesto = 585 THEN 7
			WHEN es.idPuesto = 686 THEN 9
			WHEN es.idPuesto = 158 THEN 6
			ELSE es.idPuesto END 
  		LEFT JOIN opcionesPorCatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo
		WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND es.idPuesto = $espe
		GROUP BY us.nombre, es.nombre, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita,
		ct.fechaInicio, ct.fechaFinal
		ORDER BY ct.fechaInicio, ct.fechaFinal DESC "); */

        $query = $this->ch->query("SELECT CONCAT(IFNULL(us3.nombre_persona, ''), ' ', IFNULL(us3.pri_apellido, ''), ' ', IFNULL(us3.sec_apellido, '')) AS nombre, 
        CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, ct.idPaciente, ct.titulo,
        oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
        CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
        IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita
        FROM ". $this->schema_cm .".citas ct 
        INNER JOIN ". $this->schema_cm .".catalogos ca ON ca.idCatalogo = 2
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
        INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_cm .".usuarios es ON es.idUsuario = ct.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = es.idContrato
        LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
        LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
        LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
        LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
            WHEN us2.idpuesto = 537 THEN 8
            WHEN us2.idpuesto = 585 THEN 7
            WHEN us2.idpuesto = 686 THEN 9
            WHEN us2.idpuesto = 158 THEN 6
            ELSE us2.idpuesto END 
          LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo
        WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND us2.idpuesto = $espe
        GROUP BY us2.nombre_persona, us2.pri_apellido,us2.sec_apellido,us3.nombre_persona, us3.pri_apellido,    
          us3.sec_apellido, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita, ct.fechaInicio, ct.fechaFinal
        ORDER BY ct.fechaInicio, ct.fechaFinal DESC");

        return $query;

        }else if($idRol == 3){

            /* $query = $this->db->query("SELECT us.nombre, es.nombre AS especialista, ct.idPaciente, ct.titulo, oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
            CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario, 
            ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita
            FROM citas ct 
            INNER JOIN catalogos ca ON ca.idCatalogo = 2
            INNER JOIN opcionesPorCatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
            INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN usuarios es ON es.idUsuario = ct.idEspecialista
            LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
            LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
            LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
			WHEN es.idPuesto = 537 THEN 8
			WHEN es.idPuesto = 585 THEN 7
			WHEN es.idPuesto = 686 THEN 9
			WHEN es.idPuesto = 158 THEN 6
			ELSE es.idPuesto END 
            LEFT JOIN opcionesPorCatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo	
            WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND es.idPuesto = $espe AND es.idUsuario = $idEspe
            GROUP BY us.nombre, es.nombre, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita,
            ct.fechaInicio, ct.fechaFinal
            ORDER BY ct.fechaInicio, ct.fechaFinal DESC "); */
    
            $query = $this->ch->query("SELECT CONCAT(IFNULL(us3.nombre_persona, ''), ' ', IFNULL(us3.pri_apellido, ''), ' ', IFNULL(us3.sec_apellido, '')) AS nombre, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, ct.idPaciente, ct.titulo, 
            oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
            CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
            IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita
            FROM ". $this->schema_cm .".citas ct 
            INNER JOIN ". $this->schema_cm .".catalogos ca ON ca.idCatalogo = 2
            INNER JOIN ". $this->schema_cm .".opcionesporcatalogo oc ON oc.idCatalogo = ca.idCatalogo AND oc.idOpcion = ct.estatusCita
            INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".usuarios es ON es.idUsuario = ct.idEspecialista
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = es.idContrato
            LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
            LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
            LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
            LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
                WHEN us2.idpuesto = 537 THEN 8
                WHEN us2.idpuesto = 585 THEN 7
                WHEN us2.idpuesto = 686 THEN 9
                WHEN us2.idpuesto = 158 THEN 6
                ELSE us2.idpuesto END 
              LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idOpcion = mpc.idMotivo AND ops.idCatalogo = cat.idCatalogo
            WHERE ct.idPaciente = $idUsuario AND oc.idCatalogo = 2 AND us2.idpuesto = $espe AND es.idUsuario = $idEspe
            GROUP BY us2.nombre_persona, us2.pri_apellido,us2.sec_apellido,us3.nombre_persona, us3.pri_apellido,    
              us3.sec_apellido, ct.idPaciente, ct.titulo, oc.nombre, ct.estatusCita, ct.idDetalle, ct.tipoCita, ct.fechaInicio, ct.fechaFinal
            ORDER BY ct.fechaInicio, ct.fechaFinal DESC");
            return $query;

        }

    }

    public function getEstatusPaciente(){
        
        $query = $this->ch->query("SELECT idOpcion, nombre FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 13");
        return $query;

    }

    public function getAtencionXsede(){
        
        /* $query = $this->db->query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.sede, axs.idArea, axs.idEspecialista, axs.tipoCita, o.idOficina, o.oficina, o.ubicación, us.nombre, ps.idPuesto, ps.puesto, op.nombre AS modalidad, axs.estatus,
        'nombreArea' = CASE
            WHEN axs.idArea IS NULL THEN 'SIN ÁREA'
            WHEN axs.idArea IS NOT NULL THEN CONCAT(ar.area, ' ', '(', dt.depto, ')')
        END
        FROM atencionXSede axs
        INNER JOIN sedes sd ON sd.idSede = axs.idSede
        INNER JOIN oficinas o ON o.idOficina = axs.idOficina
        INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
        INNER JOIN catalogos ct ON ct.idCatalogo = 5
        LEFT JOIN areas ar ON ar.idArea = axs.idArea
        LEFT JOIN departamentos dt ON dt.idDepto = ar.idDepto 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita"); */
        
        $query = $this->ch->query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.nsede AS sede, axs.idArea, axs.idEspecialista, axs.tipoCita, 
        o.idoficina AS idOficina, o.noficina AS oficina, o.direccion AS ubicación, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
        ps.idpuesto AS idPuesto, ps.nom_puesto AS puesto, op.nombre AS modalidad, axs.estatus,
        CASE
        WHEN axs.idArea IS NULL THEN 'SIN ÁREA'
        WHEN axs.idArea IS NOT NULL THEN CONCAT(ar.narea, ' ', '(', dt.ndepto, ')')
        END AS nombreArea
        FROM ". $this->schema_cm .".atencionxsede axs
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas o ON o.idoficina = axs.idOficina
        INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto 
        INNER JOIN catalogos ct ON ct.idCatalogo = 5
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = axs.idArea
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dt ON dt.iddepto = ar.iddepto  
        INNER JOIN opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita");
        return $query;

    }

    public function getSedes(){
        
        /* $query = $this->db->query("SELECT * FROM sedes"); */

        $query = $this->ch->query("SELECT idsede AS idSede, nsede AS sede 
        FROM ". $this->schema_ch .".beneficioscm_vista_sedes");
        return $query;

    }

    public function getOficinas(){
        
        /* $query = $this->db->query("SELECT * FROM oficinas"); */

        $query = $this->ch->query("SELECT idoficina AS idOficina, noficina AS oficina, direccion AS ubicación 
        FROM ". $this->schema_ch .".beneficioscm_vista_oficinas");
        return $query;

    }

    public function getModalidades(){
        
        /* $query = $this->db->query("SELECT idOpcion, nombre AS modalidad FROM opcionesPorCatalogo WHERE idCatalogo = 5"); */
        $query = $this->ch->query("SELECT idOpcion, nombre AS modalidad FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 5");
        return $query;

    }

    public function getSinAsigSede(){
        
        /* $query = $this->db->query("SELECT sd.idSede, sd.sede, sd.fechaCreacion AS fecha
        FROM sedes sd
        LEFT JOIN atencionXSede ON sd.idSede = atencionXSede.idSede
        WHERE atencionXSede.idSede IS NULL;
        "); */

        $query = $this->ch->query("SELECT sd.idsede AS idSede, sd.nsede AS sede
        FROM ". $this->schema_ch .".beneficioscm_vista_sedes sd
        LEFT JOIN ". $this->schema_cm .".atencionxsede ON sd.idsede = atencionxsede.idSede
        WHERE atencionxsede.idSede IS NULL");

        if ($query->num_rows() > 0) {

            return $query->result();

        }else{

            return false;

        }
    }

    public function getCitas($dt)
    {
        /* $query = $this->db-> query("SELECT ct.idCita AS id, us.nombre especialista, ps.puesto AS beneficio, sd.sede, op.nombre AS estatus, 
		CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario,
		ofi.oficina, oxc.nombre AS metodoPago, ct.estatusCita,
		ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita,
        'color' = CASE
	        WHEN ct.estatusCita = 0 THEN '#ff0000'
	        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	        WHEN ct.estatusCita = 2 THEN '#ff0000'
	        WHEN ct.estatusCita = 3 THEN '#808080'
	        WHEN ct.estatusCita = 4 THEN '#008000'
            WHEN ct.estatusCita = 5 THEN '#ff4d67'
            WHEN ct.estatusCita = 6 THEN '#00ffff'
            WHEN ct.estatusCita = 7 THEN '#ff0000'
            WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
	    END,
		CASE 
		WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
		ELSE 'Exitoso'
		END AS pagoGenerado
		FROM citas ct
		LEFT JOIN usuarios us ON us.idUsuario = ct.idEspecialista
		LEFT JOIN usuarios pa ON pa.idUsuario = ct.idPaciente
		LEFT JOIN puestos ps ON ps.idPuesto = us.idPuesto
		LEFT JOIN opcionesPorCatalogo op ON op.idOpcion = ct.estatusCita
		LEFT JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
		LEFT JOIN sedes sd ON sd.idSede = axs.idSede
		LEFT JOIN oficinas ofi ON ofi.idOficina = axs.idOficina
		LEFT JOIN puestos ps2 ON ps2.idPuesto = pa.idPuesto
		LEFT JOIN areas ar ON ar.idArea = ps2.idArea
		LEFT JOIN departamentos dep ON dep.idDepto = ar.idDepto
		LEFT JOIN catalogos cat ON cat.idCatalogo = CASE 
		WHEN ps.idPuesto = 537 THEN 8
		WHEN ps.idPuesto = 585 THEN 7
		WHEN ps.idPuesto = 686 THEN 9
		WHEN ps.idPuesto = 158 THEN 6
		ELSE ps.idPuesto END 
		LEFT JOIN detallePagos dp ON dp.idDetalle = ct.idDetalle
		LEFT JOIN opcionesPorCatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
		LEFT JOIN motivosPorCita mpc ON mpc.idCita = ct.idCita
		  LEFT JOIN opcionesPorCatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
		WHERE op.idCatalogo = 2 AND ct.idPaciente = $dt
		GROUP BY 
			  ct.idCita, 
			  pa.idUsuario, 
			  us.nombre, 
			  ps.puesto, 
			  sd.sede, 
			  ct.titulo, 
			  op.nombre, 
			  ct.fechaInicio, 
			  ct.fechaFinal, 
			  ofi.oficina, 
			  oxc.nombre, 
			  ct.estatusCita, 
			  ct.fechaModificacion,
			  dep.depto,
			  axs.tipoCita"); */

        $query = $this->ch-> query("SELECT ct.idCita AS id, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, us2.idpuesto AS beneficio, sd.nsede AS sede, op.nombre AS estatus, 
		CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
		ofi.noficina AS oficina, oxc.nombre AS metodoPago, ct.estatusCita,
		IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'Sin motivos de cita') AS motivoCita,
        CASE
	        WHEN ct.estatusCita = 0 THEN '#ff0000'
	        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	        WHEN ct.estatusCita = 2 THEN '#ff0000'
	        WHEN ct.estatusCita = 3 THEN '#808080'
	        WHEN ct.estatusCita = 4 THEN '#008000'
            WHEN ct.estatusCita = 5 THEN '#ff4d67'
            WHEN ct.estatusCita = 6 THEN '#00ffff'
            WHEN ct.estatusCita = 7 THEN '#ff0000'
            WHEN ct.estatusCita = 1 AND axs.tipoCita = 2 THEN '#0000ff'
	    END AS color,
		CASE 
		WHEN ct.estatusCita IN (2, 7, 8) THEN 'Cancelado'
		ELSE 'Exitoso'
		END AS pagoGenerado
		FROM ". $this->schema_cm .".citas ct
		LEFT JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idEspecialista
		LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
		LEFT JOIN ". $this->schema_cm .".usuarios pa ON pa.idUsuario = ct.idPaciente
		LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = pa.idContrato
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idOpcion = ct.estatusCita
		LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
		LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
		LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas ofi ON ofi.idoficina = axs.idOficina
		LEFT JOIN ". $this->schema_cm .".catalogos cat ON cat.idCatalogo = CASE 
		WHEN us2.idpuesto = 537 THEN 8
		WHEN us2.idpuesto = 585 THEN 7
		WHEN us2.idpuesto = 686 THEN 9
		WHEN us2.idpuesto = 158 THEN 6
		ELSE us2.idpuesto END 
		LEFT JOIN ". $this->schema_cm .".detallepagos dp ON dp.idDetalle = ct.idDetalle
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo oxc ON oxc.idOpcion = dp.metodoPago AND oxc.idCatalogo = 11
		LEFT JOIN ". $this->schema_cm .".motivosporcita mpc ON mpc.idCita = ct.idCita
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo ops ON ops.idCatalogo = cat.idCatalogo AND ops.idOpcion = mpc.idMotivo	
		WHERE op.idCatalogo = 2 AND ct.idPaciente = $dt
		GROUP BY 
			  ct.idCita, 
			  pa.idUsuario, 
			  us2.nombre_persona,
			  us2.pri_apellido,
			  us2.sec_apellido,
			  us2.idpuesto, 
			  sd.nsede, 
			  ct.titulo, 
			  op.nombre, 
			  ct.fechaInicio, 
			  ct.fechaFinal, 
			  oficina, 
			  oxc.nombre, 
			  ct.estatusCita, 
			  ct.fechaModificacion,
			  axs.tipoCita");
		return $query;
    }

    public function getEstatusCitas(){
        
        /* $query = $this->db->query("SELECT idOpcion, nombre, 'color' = CASE
        WHEN idOpcion = 1 THEN '#ffa500'
        WHEN idOpcion = 2 THEN '#ff0000'
        WHEN idOpcion = 3 THEN '#808080'
        WHEN idOpcion = 4 THEN '#008000'
        WHEN idOpcion = 5 THEN '#ff4d67'
        WHEN idOpcion = 6 THEN '#00ffff'
        END
        FROM opcionesPorCatalogo WHERE idCatalogo = 2 AND idOpcion NOT IN (7, 8, 9);"); */

        $query = $this->ch->query("SELECT idOpcion, nombre, 'color' = CASE
        WHEN idOpcion = 1 THEN '#ffa500'
        WHEN idOpcion = 2 THEN '#ff0000'
        WHEN idOpcion = 3 THEN '#808080'
        WHEN idOpcion = 4 THEN '#008000'
        WHEN idOpcion = 5 THEN '#ff4d67'
        WHEN idOpcion = 6 THEN '#00ffff'
        END
        FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 2 AND idOpcion NOT IN (7, 8, 9);");
        return $query;

    }

    public function getCountEstatusCitas($dt){

        $area = $dt["area"];
        $especialidad = $dt["espe"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($especialidad == 0){

            /* $query = $this->db->query("SELECT 
            COUNT(CASE WHEN ct.estatusCita = 1 THEN ct.idPaciente END) AS asistir,
            COUNT(CASE WHEN ct.estatusCita = 2 OR ct.estatusCita = 7 THEN ct.idPaciente END) AS cancelada,
            COUNT(CASE WHEN ct.estatusCita = 3 THEN ct.idPaciente END) AS penalizada,
            COUNT(CASE WHEN ct.estatusCita = 4 THEN ct.idPaciente END) AS asistencia,
            COUNT(CASE WHEN ct.estatusCita = 5 THEN ct.idPaciente END) AS justificada,
            COUNT(CASE WHEN ct.estatusCita = 6 THEN ct.idPaciente END) AS pendiente,
            COUNT(ct.idCita) AS citas
            FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $area AND
            (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')"); */

            $query = $this->ch->query("SELECT 
            COUNT(CASE WHEN ct.estatusCita = 1 THEN ct.idPaciente END) AS asistir,
            COUNT(CASE WHEN ct.estatusCita = 2 OR ct.estatusCita = 7 THEN ct.idPaciente END) AS cancelada,
            COUNT(CASE WHEN ct.estatusCita = 3 THEN ct.idPaciente END) AS penalizada,
            COUNT(CASE WHEN ct.estatusCita = 4 THEN ct.idPaciente END) AS asistencia,
            COUNT(CASE WHEN ct.estatusCita = 5 THEN ct.idPaciente END) AS justificada,
            COUNT(CASE WHEN ct.estatusCita = 6 THEN ct.idPaciente END) AS pendiente,
            COUNT(ct.idCita) AS citas
            FROM ". $this->schema_cm .".usuarios us
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us2.idpuesto = $area AND
            (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')");

        }else{

            /* $query = $this->db->query("SELECT 
            COUNT(CASE WHEN ct.estatusCita = 1 THEN ct.idPaciente END) AS asistir,
            COUNT(CASE WHEN ct.estatusCita = 2 OR ct.estatusCita = 7 THEN ct.idPaciente END) AS cancelada,
            COUNT(CASE WHEN ct.estatusCita = 3 THEN ct.idPaciente END) AS penalizada,
            COUNT(CASE WHEN ct.estatusCita = 4 THEN ct.idPaciente END) AS asistencia,
            COUNT(CASE WHEN ct.estatusCita = 5 THEN ct.idPaciente END) AS justificada,
            COUNT(CASE WHEN ct.estatusCita = 6 THEN ct.idPaciente END) AS pendiente,
            COUNT(ct.idCita) AS citas
            FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $area AND
            (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')
            AND us.idUsuario = $especialidad"); */

            $query = $this->ch->query("SELECT 
            COUNT(CASE WHEN ct.estatusCita = 1 THEN ct.idPaciente END) AS asistir,
            COUNT(CASE WHEN ct.estatusCita = 2 OR ct.estatusCita = 7 THEN ct.idPaciente END) AS cancelada,
            COUNT(CASE WHEN ct.estatusCita = 3 THEN ct.idPaciente END) AS penalizada,
            COUNT(CASE WHEN ct.estatusCita = 4 THEN ct.idPaciente END) AS asistencia,
            COUNT(CASE WHEN ct.estatusCita = 5 THEN ct.idPaciente END) AS justificada,
            COUNT(CASE WHEN ct.estatusCita = 6 THEN ct.idPaciente END) AS pendiente,
            COUNT(ct.idCita) AS citas
            FROM ". $this->schema_cm .".usuarios us
            INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us2.idpuesto = $area AND
            (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')
            AND us.idUsuario = $especialidad");

        }
        
        return $query;

    }

    public function getCountModalidades($dt){

        $area = $dt["area"];
        $especialidad = $dt["espe"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

        if($especialidad == 0){

            /* $query = $this->db->query("SELECT 
			COUNT(CASE WHEN axs.tipoCita = 2 THEN ct.idPaciente END) AS virtual,
			COUNT(CASE WHEN axs.tipoCita = 1 THEN ct.idPaciente END) AS presencial
			FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE us.idPuesto = $area AND
		    (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')"); */

            $query = $this->ch->query("SELECT 
			COUNT(CASE WHEN axs.tipoCita = 2 THEN ct.idPaciente END) AS `virtual`,
			COUNT(CASE WHEN axs.tipoCita = 1 THEN ct.idPaciente END) AS presencial
			FROM ". $this->schema_cm .".usuarios us
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE us2.idpuesto = $area AND
		    (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')");

        }else{

            /* $query = $this->db->query("SELECT 
			COUNT(CASE WHEN axs.tipoCita = 2 THEN ct.idPaciente END) AS virtual,
			COUNT(CASE WHEN axs.tipoCita = 1 THEN ct.idPaciente END) AS presencial
			FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN atencionXSede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN opcionesPorCatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE us.idPuesto = $area AND
		    (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')
            AND us.idUsuario = $especialidad"); */

            $query = $this->ch->query("SELECT 
			COUNT(CASE WHEN axs.tipoCita = 2 THEN ct.idPaciente END) AS `virtual`,
			COUNT(CASE WHEN axs.tipoCita = 1 THEN ct.idPaciente END) AS presencial
			FROM ". $this->schema_cm .".usuarios us
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
            INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
			INNER JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
			INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita
            WHERE us2.idpuesto = $area AND
		    (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')
            AND us.idUsuario = $especialidad");

        }
        
        return $query;

    }

    public function getCountPacientes($dt){

        $especialidad = $dt["espe"];
        $rol = $dt["idRol"];

        if($rol == 4){

            /* $query = $this->db->query("SELECT
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 1 THEN ct.idPaciente END) AS enero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 2 THEN ct.idPaciente END) AS febrero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 3 THEN ct.idPaciente END) AS marzo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 4 THEN ct.idPaciente END) AS abril,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 5 THEN ct.idPaciente END) AS mayo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 6 THEN ct.idPaciente END) AS junio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 7 THEN ct.idPaciente END) AS julio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 8 THEN ct.idPaciente END) AS agosto,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 9 THEN ct.idPaciente END) AS septiembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 10 THEN ct.idPaciente END) AS octubre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 11 THEN ct.idPaciente END) AS noviembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 12 THEN ct.idPaciente END) AS diciembre,
            COUNT(DISTINCT ct.idPaciente) AS total
            FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE ct.estatusCita = 4
            AND YEAR(ct.fechaFinal) = YEAR(GETDATE())"); */

            $query = $this->ch->query("SELECT
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 1 THEN ct.idPaciente END) AS enero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 2 THEN ct.idPaciente END) AS febrero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 3 THEN ct.idPaciente END) AS marzo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 4 THEN ct.idPaciente END) AS abril,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 5 THEN ct.idPaciente END) AS mayo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 6 THEN ct.idPaciente END) AS junio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 7 THEN ct.idPaciente END) AS julio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 8 THEN ct.idPaciente END) AS agosto,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 9 THEN ct.idPaciente END) AS septiembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 10 THEN ct.idPaciente END) AS octubre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 11 THEN ct.idPaciente END) AS noviembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 12 THEN ct.idPaciente END) AS diciembre,
            COUNT(DISTINCT ct.idPaciente) AS total
            FROM ". $this->schema_cm .".usuarios us
            INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
            WHERE ct.estatusCita = 4
            AND YEAR(ct.fechaFinal) =  YEAR(CURDATE());");

        }else{

            /* $query = $this->db->query("SELECT
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 1 THEN ct.idPaciente END) AS enero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 2 THEN ct.idPaciente END) AS febrero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 3 THEN ct.idPaciente END) AS marzo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 4 THEN ct.idPaciente END) AS abril,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 5 THEN ct.idPaciente END) AS mayo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 6 THEN ct.idPaciente END) AS junio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 7 THEN ct.idPaciente END) AS julio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 8 THEN ct.idPaciente END) AS agosto,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 9 THEN ct.idPaciente END) AS septiembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 10 THEN ct.idPaciente END) AS octubre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 11 THEN ct.idPaciente END) AS noviembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 12 THEN ct.idPaciente END) AS diciembre,
            COUNT(DISTINCT ct.idPaciente) AS total
            FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE ct.estatusCita = 4
            AND YEAR(ct.fechaFinal) = YEAR(GETDATE())
            AND us.idUsuario = $especialidad"); */

            $query = $this->ch->query("SELECT
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 1 THEN ct.idPaciente END) AS enero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 2 THEN ct.idPaciente END) AS febrero,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 3 THEN ct.idPaciente END) AS marzo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 4 THEN ct.idPaciente END) AS abril,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 5 THEN ct.idPaciente END) AS mayo,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 6 THEN ct.idPaciente END) AS junio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 7 THEN ct.idPaciente END) AS julio,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 8 THEN ct.idPaciente END) AS agosto,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 9 THEN ct.idPaciente END) AS septiembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 10 THEN ct.idPaciente END) AS octubre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 11 THEN ct.idPaciente END) AS noviembre,
            COUNT(DISTINCT CASE WHEN MONTH(ct.fechaFinal) = 12 THEN ct.idPaciente END) AS diciembre,
            COUNT(DISTINCT ct.idPaciente) AS total
            FROM ". $this->schema_cm .".usuarios us
            INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
            WHERE ct.estatusCita = 4
            AND YEAR(ct.fechaFinal) =  YEAR(CURDATE())
            AND us.idUsuario = $especialidad");

        }
        
        return $query;

    }
}