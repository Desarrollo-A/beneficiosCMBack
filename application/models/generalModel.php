<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GeneralModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function usuarios()
	{
		$query = $this->db-> query("SELECT * FROM usuarios");
		return $query->result();
	}

    public function usuarioExiste($idContrato){
        $query = $this->db-> query("SELECT *FROM usuarios WHERE idContrato = ?", $idContrato);
		return $query;
    }

    public function getInfoPuesto($puesto){
        $query = $this->db-> query("SELECT *FROM puestos WHERE idPuesto = ?", $puesto);
		return $query;
    }

    public function usrCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [usuarios] FROM usuarios");
		return $query->result();
	}

    public function citasCount()
	{
		$query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM citas");
		return $query->result();
	}

    public function especialistas()
	{
		$query = $this->db-> query("SELECT idPuesto, puesto AS nombre FROM puestos WHERE idPuesto = 537 OR idPuesto = 686 OR idPuesto = 158 OR idPuesto = 585");
		return $query->result();
	}

    // MJ: AGREGA UN REGISTRO A UNA TABLA EN PARTICULAR, RECIBE 2 PARÁMETROS. LA TABLA Y LA DATA A INSERTAR
    public function addRecord($table, $data) { 
        return $this->db->insert($table, $data);
    }

    // MJ: ACTUALIZA LA INFORMACIÓN DE UN REGISTRO EN PARTICULAR, RECIBE 4 PARÁMETROS. TABLA, DATA A ACTUALIZAR, LLAVE (WHERE) Y EL VALOR DE LA LLAVE
    public function updateRecord($table, $data, $key, $value) { 
        return $this->db->update($table, $data, "$key = '$value'");
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

    public function getPuesto($dt)
    {
        $query = $this->db-> query("SELECT pu.puesto
        FROM usuarios us
        INNER JOIN puestos pu ON pu.idPuesto = us.idPuesto
        WHERE idUsuario = $dt");

		return $query;
    }

    public function getSede($dt)
    {
        $query = $this->db-> query("SELECT se.sede
        FROM usuarios us
        INNER JOIN sedes se ON se.idSede = us.idSede
        WHERE idUsuario = $dt");
		return $query;
    }

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
                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [pacientes] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
            }else if( $slEs != 0 ){

                $query = $this->db-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS [pacientes] FROM usuarios us
                INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND ct.idEspecialista = $slEs AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
            }

        }else if($idRol == 2){
            $query = $this->db-> query("SELECT COUNT(*) AS [pacientes] FROM citas WHERE idPaciente = $idUser AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }else if($idRol == 3){
            $query = $this->db-> query("SELECT COUNT(DISTINCT idPaciente) AS [pacientes] FROM citas WHERE idEspecialista = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }
        
        return $query;
    }

    public function getCtAsistidas($dt)
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
    }

    public function getCtVirtuales($dt)
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
    }

    public function getAppointmentHistory($dt){

        $idUsuario = $dt["idUser"];
        $idRol = $dt["idRol"];
        $idEspe = $dt["idEspe"];
        $espe = $dt["espe"];

        if($idRol == 1  || $idRol == 4){

        $query = $this->db->query("SELECT us.nombre, es.nombre AS especialista, ct.idPaciente, ct.titulo, oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
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
		ORDER BY ct.fechaInicio, ct.fechaFinal DESC ");

        return $query;

        }else if($idRol == 3){

            $query = $this->db->query("SELECT us.nombre, es.nombre AS especialista, ct.idPaciente, ct.titulo, oc.nombre AS estatus, ct.estatusCita, ct.idDetalle AS pago, ct.tipoCita,
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
            ORDER BY ct.fechaInicio, ct.fechaFinal DESC ");
    
            return $query;

        }

    }

    public function getEstatusPaciente(){
        
        $query = $this->db->query("SELECT idOpcion, nombre FROM opcionesPorCatalogo WHERE idCatalogo = 13");
        return $query;

    }

    public function getAtencionXsede(){
        
        $query = $this->db->query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.sede, axs.idArea, axs.idEspecialista, axs.tipoCita, o.idOficina, o.oficina, o.ubicación, us.nombre, ps.idPuesto, ps.puesto, op.nombre AS modalidad, axs.estatus
        FROM atencionXSede axs
        INNER JOIN sedes sd ON sd.idSede = axs.idSede
        INNER JOIN oficinas o ON o.idOficina = axs.idOficina
        INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
        INNER JOIN catalogos ct ON ct.idCatalogo = 5
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita");
        return $query;

    }

    public function getSedes(){
        
        $query = $this->db->query("SELECT * FROM sedes");
        return $query;

    }

    public function getOficinas(){
        
        $query = $this->db->query("SELECT * FROM oficinas");
        return $query;

    }

    public function getModalidades(){
        
        $query = $this->db->query("SELECT idOpcion, nombre AS modalidad FROM opcionesPorCatalogo WHERE idCatalogo = 5");
        return $query;

    }

    public function getSinAsigSede(){
        
        $query = $this->db->query("SELECT sd.idSede, sd.sede, sd.fechaCreacion AS fecha
        FROM sedes sd
        LEFT JOIN atencionXSede ON sd.idSede = atencionXSede.idSede
        WHERE atencionXSede.idSede IS NULL;
        ");

        if ($query->num_rows() > 0) {

            return $query->result();

        }else{

            return false;

        }
    }

    public function getCitas($dt)
    {
        $query = $this->db-> query("SELECT ct.idCita AS id, us.nombre especialista, ps.puesto AS beneficio, sd.sede, op.nombre AS estatus, 
		CONCAT (CONVERT(DATE,ct.fechaInicio), ' ', FORMAT(ct.fechaInicio, 'HH:mm'), ' - ', FORMAT(ct.fechaFinal, 'HH:mm')) AS horario,
		ofi.oficina, oxc.nombre AS metodoPago, ct.estatusCita,
		ISNULL(string_agg(ops.nombre, ', '), 'Sin motivos de cita') AS motivoCita,
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
			dep.depto");
		return $query;
    }

}