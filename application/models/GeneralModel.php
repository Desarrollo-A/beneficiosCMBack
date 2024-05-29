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
	}

    public function usuarioExiste($idContrato){
        
        $query = $this->ch-> query("SELECT * FROM ". $this->schema_cm .".usuarios WHERE idContrato = ?", $idContrato);
		return $query;
    }

    public function getInfoPuesto($contrato){
        
        $query = $this->ch-> query("SELECT ps.idpuesto AS idPuesto, ps.nom_puesto AS puesto, ps.tipo_puesto AS tipoPuesto,  
        ps.idarea AS idArea, ps.estatus_puesto AS estatus, dp.canRegister 
        FROM ". $this->schema_ch .".beneficioscm_vista_usuarios AS us
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us.idpuesto
        LEFT JOIN ". $this->schema_cm .".datopuesto dp ON dp.idPuesto = ps.idpuesto 
        WHERE us.idcontrato = ?", $contrato);
        return $query;
    }

    public function especialistas()
	{
		$query = $this->ch-> query("SELECT idpuesto AS idPuesto, nom_puesto AS nombre FROM ". $this->schema_ch .".beneficioscm_vista_puestos WHERE idpuesto = 537 OR idpuesto = 686 OR idpuesto = 158 OR idpuesto = 585");
        return $query->result();
	}

    // MJ: AGREGA UN REGISTRO A UNA TABLA EN PARTICULAR, RECIBE 2 PARÁMETROS. LA TABLA Y LA DATA A INSERTAR
    public function addRecord($table, $data) { 
        return $this->ch->insert($table, $data);
    }

    public function addRecordReturnId($table, $data) { 
        $this->ch->insert($table, $data);
        return $this->ch->insert_id();
    }

    // MJ: ACTUALIZA LA INFORMACIÓN DE UN REGISTRO EN PARTICULAR, RECIBE 4 PARÁMETROS. TABLA, DATA A ACTUALIZAR, LLAVE (WHERE) Y EL VALOR DE LA LLAVE
    public function updateRecord($table, $data, $key, $value) { 
        return $this->ch->update($table, $data, "$key = '$value'");
    }

    public function insertBatch($table, $data)
    {
        $this->ch->trans_begin();
        $this->ch->insert_batch($table, $data);
        if (!$this->ch->trans_status())  { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->ch->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->ch->trans_commit();
        }
    }

    public function insertBatchAndGetIds($table, $data) {
        $this->ch->trans_begin();
        $this->ch->insert_batch($table, $data);
        $insert_id = $this->ch->insert_id();
        $affected_rows = $this->ch->affected_rows();
        if (!$this->ch->trans_status())  { // Hubo errores en la consulta, entonces se cancela la transacción.
            $this->ch->trans_rollback();
            return false;
        } else { // Todas las consultas se hicieron correctamente.
            $this->ch->trans_commit();
            return range($insert_id, $insert_id + $affected_rows - 1);
        }
    }


    public function updateBatch($table, $data, $key)
    {
        $this->ch->trans_begin();
        $this->ch->update_batch($table, $data, $key);
        if (!$this->ch->trans_status()) { // Hubo errores en la consulta, entonces se cancela la transacción.
            return $this->ch->trans_rollback();
        } else { // Todas las consultas se hicieron correctamente.
            return $this->ch->trans_commit();
        }
    }

    public function getEstatusPaciente(){
        
        $query = $this->ch->query("SELECT idOpcion, nombre FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 13");
        return $query;

    }

    public function getAtencionXsede(){
        
        $query = $this->ch->query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.nsede AS sede, axs.idArea, axs.idEspecialista, axs.tipoCita, 
        o.idoficina AS idOficina,
        CASE 
        WHEN axs.idOficina = 0 THEN 'VIRTUAL' 
        WHEN axs.idOficina <> 0 THEN o.noficina 
        END AS oficina, 
         o.direccion AS ubicación, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
        ps.idpuesto AS idPuesto, ps.nom_puesto AS puesto, op.nombre AS modalidad, axs.estatus,
        CASE
        WHEN axs.idArea IS NULL THEN 'SIN ÁREA'
        WHEN axs.idArea IS NOT NULL THEN CONCAT(ar.narea, ' ', '(', dt.ndepto, ')')
        END AS nombreArea
        FROM ". $this->schema_cm .".atencionxsede axs
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_sedes sd ON sd.idsede = axs.idSede
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_oficinas o ON o.idoficina = axs.idOficina
        INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
        INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = us2.idpuesto 
        INNER JOIN ". $this->schema_cm .".catalogos ct ON ct.idCatalogo = 5
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = axs.idArea
        LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dt ON dt.iddepto = ar.iddepto  
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita");
        return $query;

    }

    public function getSedes(){

        $query = $this->ch->query("SELECT idsede AS idSede, nsede AS sede 
        FROM ". $this->schema_ch .".beneficioscm_vista_sedes 
        WHERE idsede NOT IN(7) AND estatus_sede = 1
        ORDER BY nsede ASC");
        return $query;

    }

    public function getOficinas(){

        $query = $this->ch->query("SELECT idoficina AS idOficina, noficina AS oficina, direccion AS ubicación, 
        CONCAT(noficina,' (', IFNULL(direccion, 'Sin dirección'),') ') AS lugar
        FROM ". $this->schema_ch .".beneficioscm_vista_oficinas");
        return $query;

    }

    public function getModalidades(){
        
        $query = $this->ch->query("SELECT idOpcion, nombre AS modalidad FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 5");
        return $query;

    }

    public function getSinAsigSede(){

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

        $query = $this->ch-> query("SELECT ct.idCita AS id, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista, us2.idpuesto AS beneficio, sd.nsede AS sede, op.nombre AS estatus, 
		CONCAT(DATE_FORMAT(ct.fechaInicio, '%Y-%m-%d'), ' ', DATE_FORMAT(ct.fechaInicio, '%H:%i'), ' - ', DATE_FORMAT(ct.fechaFinal, '%H:%i')) AS horario,
		ofi.noficina AS oficina, IFNULL(oxc.nombre, 'Pendiente de pago') AS metodoPago, ct.estatusCita, us2.idsede AS idSedeEsp,
		IFNULL(GROUP_CONCAT(ops.nombre SEPARATOR ', '), 'SIN MOTIVOS DE CITA') AS motivoCita,
        CASE
	        WHEN ct.estatusCita = 0 THEN '#ff0000'
	        WHEN ct.estatusCita = 1 AND axs.tipoCita = 1 THEN '#ffa500'
	        WHEN ct.estatusCita = 2 THEN '#ff0000'
	        WHEN ct.estatusCita = 3 THEN '#808080'
	        WHEN ct.estatusCita = 4 THEN '#008000'
            WHEN ct.estatusCita = 5 THEN '#ff4d67'
            WHEN ct.estatusCita = 6 THEN '#00ffff'
            WHEN ct.estatusCita = 7 THEN '#ff0000'
            WHEN ct.estatusCita = 10 THEN '#33105D'
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

        $query = $this->ch->query("SELECT idOpcion, nombre, 
        CASE
        WHEN idOpcion = 1 THEN '#ffa500'
        WHEN idOpcion = 2 THEN '#ff0000'
        WHEN idOpcion = 3 THEN '#808080'
        WHEN idOpcion = 4 THEN '#008000'
        WHEN idOpcion = 5 THEN '#ff4d67'
        WHEN idOpcion = 6 THEN '#00ffff'
        WHEN idOpcion = 7 THEN '#ff0000'
        WHEN idOpcion = 8 THEN '#ffe800'
        WHEN idOpcion = 9 THEN '#0000ff'
        WHEN idOpcion = 10 THEN '#33105D'
        END AS color
        FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 2 AND idOpcion IN (1, 2, 3, 4, 5, 6, 10);");
        return $query;

    }

    public function getAllAreas(){
		$query = $this->ch-> query("SELECT ar.idsubarea AS id,ar.narea AS area 
		FROM ". $this->schema_cm .".datopuesto dt
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = dt.idPuesto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = ps.idarea 
		WHERE dt.canRegister = 1
		GROUP BY ar.idsubarea
		ORDER BY ar.narea ASC");
        return $query;
	}
}
