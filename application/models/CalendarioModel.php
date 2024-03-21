<?php
defined('BASEPATH') or exit('No direct script access allowed');

class calendarioModel extends CI_Model
{
    public function __construct()
	{
		$this->ch = $this->load->database('ch', TRUE);
	}
    
    // Mostrarlos en calendario
    // public function getAppointmentsByUser($year, $month, $idUsuario){
    //     $query = $this->db->query(
    //         "SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
    //         ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, us.nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
    //         ct.tipoCita, atc.tipoCita as modalidad, atc.idSede ,usEspe.idPuesto, us.telPersonal, usEspe.telPersonal as telefonoEspecialista, 
    //         CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina', CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación',
    //         pue.idArea, sed.sede, atc.idOficina, us.correo, usEspe.correo as correoEspecialista, usEspe.nombre as especialista, 
    //         usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
    //         'color' = CASE
    //             WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500'
	//             WHEN ct.estatusCita = 2 THEN '#ff0000'
	//             WHEN ct.estatusCita = 3 THEN '#808080'
	//             WHEN ct.estatusCita = 4 THEN '#008000'
    //             WHEN ct.estatusCita = 5 THEN '#ff4d67'
    //             WHEN ct.estatusCita = 6 THEN '#00ffff'
    //             WHEN ct.estatusCita = 7 THEN '#ff0000'
    //             WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff'
	//         END,
    //         beneficio = CASE 
    //         WHEN pue.idPuesto = 537 THEN 'nutrición'
    //         WHEN pue.idPuesto = 585 THEN 'psicología'
    //         WHEN pue.idPuesto = 686 THEN 'guía espiritual'
    //         WHEN pue.idPuesto = 158 THEN 'quantum balance'
    //         END
    //         FROM citas ct
    //         INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //         INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
    //         INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
    //         LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
    //         INNER join sedes sed ON sed.idSede = atc.idSede
    //         INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
    //         LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
    //                     FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
    //         WHERE YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ? AND ct.idPaciente = ?
    //         AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
    //         array( $year, $month, $idUsuario, 1, 2, 3, 4, 5, 6, 7)
    //     );

    //     return $query;
    // }

    // public function getOccupied($year, $month, $idUsuario, $dates){
    //     $query = $this->db->query(
    //         "SELECT idUnico as id, titulo as title, fechaInicio as 'start', fechaFinal as 'end',
    //         'purple' AS 'color', estatus, 'cancel' AS 'type'
    //         FROM horariosOcupados 
    //         WHERE YEAR(fechaInicio) in (?, ?)
    //         AND MONTH(fechaInicio) in (?, ?, ?)
    //         AND idEspecialista = ?  
    //         AND estatus = ?",
    //         array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
    //     );
    //     return $query;
    // }

    // public function getOccupiedRange($fechaInicio, $fechaFin, $idUsuario){
    //     $query = $this->db->query(
    //         "SELECT idOcupado as id, titulo as title, fechaInicio as occupied, fechaInicio, fechaFinal FROM horariosOcupados
    //         WHERE idEspecialista = ? AND estatus = ?  AND
    //         ((fechaInicio BETWEEN ? AND ?) OR 
    //         (fechaFinal BETWEEN ? AND ?) OR 
    //         (fechaInicio >= ? AND fechaFinal <= ?));",
    //         array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
    //     );
    //     return $query;
    // }

    // public function getHorarioBeneficio($beneficio){
    //     $query = $this->db->query(
    //         "SELECT *FROM horariosPorBeneficio WHERE idBeneficio = ?",
    //         array($beneficio)
    //     );
    //     return $query;
    // }

    // public function getAppointment($year, $month, $idUsuario, $dates){
    //     $query = $this->db->query(
    //         "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
    //         ct.fechaInicio AS occupied, 'date' AS 'type', ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, us.correo,
    //         se.sede, ofi.oficina, ct.idDetalle, ct.idAtencionXSede, us.externo, usEspe.nombre as especialista, ct.fechaCreacion, pue.tipoPuesto,
    //         tf.fechasFolio, idEventoGoogle, ct.tipoCita, aps.tipoCita as modalidad, aps.idSede,
    //         'color' = CASE
	//             WHEN ct.estatusCita = 0 THEN '#ff0000'
	//             WHEN ct.estatusCita = 1 AND aps.tipoCita = 1 THEN '#ffa500'
	//             WHEN ct.estatusCita = 2 THEN '#ff0000'
	//             WHEN ct.estatusCita = 3 THEN '#808080'
	//             WHEN ct.estatusCita = 4 THEN '#008000'
    //             WHEN ct.estatusCita = 5 THEN '#ff4d67'
    //             WHEN ct.estatusCita = 6 THEN '#00ffff'
    //             WHEN ct.estatusCita = 7 THEN '#ff0000'
    //             WHEN ct.estatusCita = 1 AND aps.tipoCita = 2 THEN '#0000ff'
	//         END,
    //         beneficio = CASE 
    //         WHEN pue.idPuesto = 537 THEN 'nutrición'
    //         WHEN pue.idPuesto = 585 THEN 'psicología'
    //         WHEN pue.idPuesto = 686 THEN 'guía espiritual'
    //         WHEN pue.idPuesto = 158 THEN 'quantum balance'
    //         END
    //         FROM citas ct
    //         FULL JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //         FULL JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
    //         FULL JOIN atencionXSede aps ON ct.idAtencionXSede = aps.idAtencionXSede
    //         FULL JOIN sedes se ON se.idSede = aps.idSede
    //         FULL JOIN oficinas ofi ON ofi.idOficina = aps.idOficina
    //         FULL JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
    //         FULL JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio FROM citas WHERE estatusCita IN(?) AND citas.idCita = idCita GROUP BY citas.idDetalle) tf
    //         ON tf.idDetalle = ct.idDetalle
    //         WHERE YEAR(fechaInicio) in (?, ?)
    //         AND MONTH(fechaInicio) in (?, ?, ?)
    //         AND ct.idEspecialista = ?
    //         AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
    //         array( 8, $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4, 5, 6, 7 )
    //     );

    //     return $query;
    // }

    // Función para checar las citas de ambos (Beneficiario y especialista)
    // public function getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario){
    //     $query = $this->db->query(
    //         "SELECT CAST(ct.idCita AS VARCHAR(36))  AS id,  ct.titulo AS title, ct.fechaInicio, ct.fechaFinal, 
    //         ct.estatusCita, ct.idPaciente, ct.idEspecialista d
    //         FROM citass ct
    //         LEFT JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //         WHERE (ct.idEspecialista = ? OR ct.idPaciente = ?) AND ct.estatusCita IN (?, ?)
    //         AND ((fechaInicio BETWEEN ? AND ? ) OR 
    //         (fechaFinal BETWEEN ? AND ?) OR 
    //         (fechaInicio >= ? AND fechaFinal <= ?))",
    //         array( $especialista, $usuario, 1, 6, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
    //     );

    //     return $query;
    // }

    // public function checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta){
    //     $query = $this->db->query(
    //         "SELECT *FROM horariosOcupados WHERE 
    //         ((fechaInicio BETWEEN ? AND ?) 
    //         OR (fechaFinal BETWEEN ? AND ?)
    //         OR (? BETWEEN fechaInicio AND fechaFinal) 
    //         OR (? BETWEEN fechaInicio AND fechaFinal))
    //         AND idEspecialista = ?
    //         AND estatus = ?",
    //         array(
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma,
    //             $fechaFinalResta,
    //             $dataValue["idUsuario"],
    //             1
    //         )
    //     );

    //     return $query;
    // }

    // public function checkPresencial($idSede, $idEspecialista, $modalidad, $fecha){
    //     $query = $this->db->query(
    //         "SELECT *from presencialXSede as pxs
    //         WHERE pxs.idSede = ? AND pxs.idEspecialista = ? AND presencialDate = ?;",
    //         array( $idSede, $idEspecialista, $fecha)
    //     );

    //     return $query;
    // }

    // public function checkOccupiedId($dataValue, $fechaInicioSuma ,$fechaFinalResta){
    //     $query = $this->db->query(
    //         "SELECT *FROM horariosOcupados WHERE 
    //         ((fechaInicio BETWEEN ? AND ?) 
    //         OR (fechaFinal BETWEEN ? AND ?)
    //         OR (? BETWEEN fechaInicio AND fechaFinal) 
    //         OR (? BETWEEN fechaInicio AND fechaFinal))
    //         AND idUnico != ?
    //         AND idEspecialista = ?
    //         AND estatus = ?",
    //         array(
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma,
    //             $fechaFinalResta,
    //             $dataValue["id"],
    //             $dataValue["idUsuario"],
    //             1
    //         )
    //     );

    //     return $query;
    // }

    // public function checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta){
    //     $query = $this->db->query(
    //         "SELECT *FROM citas WHERE
    //         ((fechaInicio BETWEEN ? AND ?)
    //         OR (fechaFinal BETWEEN ? AND ?)
    //         OR (? BETWEEN fechaInicio AND fechaFinal)
    //         OR (? BETWEEN fechaInicio AND fechaFinal))
    //         AND ((idPaciente = ?
    //         AND estatusCita IN (?, ?))
    //         OR (idEspecialista = ? and estatusCita IN (?, ?)))",
    //         array(
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $dataValue["idPaciente"],
    //             1, 6,
    //             $dataValue["idUsuario"],
    //             1, 6
    //         )
    //     );
        
    //     return $query;
    // }

    // public function checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta){
    //     $query = $this->db->query(
    //         "SELECT *FROM citas WHERE
    //         ((fechaInicio BETWEEN ? AND ?)
    //         OR (fechaFinal BETWEEN ? AND ?)
    //         OR (? BETWEEN fechaInicio AND fechaFinal)
    //         OR (? BETWEEN fechaInicio AND fechaFinal))
    //         AND idEspecialista = ? AND estatusCita IN(?, ?)",
    //         array(
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma, $fechaFinalResta,
    //             $fechaInicioSuma,
    //             $fechaFinalResta,
    //             $dataValue["idUsuario"],
    //             1,
    //             6
    //         )
    //     );
        
    //     return $query;
    // }

    // public function checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta){
    //     $query = $this->db->query(
    //         "SELECT *FROM citas WHERE
    //         ((fechaInicio BETWEEN ? AND ?)
    //         OR (fechaFinal BETWEEN ? AND ?)
    //         OR (? BETWEEN fechaInicio AND fechaFinal)
    //         OR (? BETWEEN fechaInicio AND fechaFinal))
    //         AND idCita != ?
    //         AND ((idPaciente = ?
    //         AND estatusCita = ?)
    //         OR (idEspecialista = ? AND estatusCita IN(?, ?)))",
    //         array(
    //             $fecha_inicio_suma, $fecha_final_resta,
    //             $fecha_inicio_suma, $fecha_final_resta,
    //             $fecha_inicio_suma,
    //             $fecha_final_resta,
    //             $dataValue["id"],
    //             $dataValue["idPaciente"],
    //             1,
    //             $dataValue["idUsuario"],
    //             1,
    //             6
    //         )
    //     );

    //     return $query;
    // }

    // public function getIdAtencion($dataValue){
    //     $query = $this->db->query(
    //         "SELECT idAtencionXSede FROM atencionXSede 
    //         WHERE idEspecialista = ?
    //         AND idSede = ( SELECT idSede FROM usuarios WHERE idUsuario = ? ) AND estatus = ?", 
    //         array($dataValue["idUsuario"], $dataValue["idUsuario"], 1)
    //     );
        
    //     return $query;
    // }
    
    // public function getBeneficiosPorSede($sede)
	// {
    //     $query = $this->db->query(
    //         "SELECT DISTINCT u.idPuesto, p.puesto
    //         FROM usuarios AS u 
    //         RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
    //         INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
    //         INNER JOIN sedes AS S ON S.idSede = U.idSede
    //         LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
    //         INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto
    //         FULL JOIN sedes AS se ON se.idSede = o.idSede
    //         WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
    //         and axs.idSede = ?", $sede
    //     );

    //     return $query;
	// }

    // public function getEspecialistaPorBeneficioYSede($sede, $area, $beneficio)
    // {
    //     $query = $this->db->query(
    //         "SELECT DISTINCT u.idUsuario as id, u.nombre AS especialista
    //         FROM usuarios AS u 
    //         RIGHT JOIN atencionXSede AS AXS ON AXS.idEspecialista = U.idUsuario
    //         INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion= axs.tipoCita
    //         INNER JOIN sedes AS S ON S.idSede = U.idSede
    //         LEFT JOIN oficinas as o ON o.idoficina = axs.idOficina
    //         INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto
    //         FULL JOIN sedes AS se ON se.idSede = o.idSede
    //         WHERE u.estatus = 1 AND s.estatus = 1 AND axs.estatus = 1  AND u.idRol = 3 AND oxc.idCatalogo = 5
    //         AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) AND u.idPuesto = ?;", array($sede, $area, $beneficio)
    //     );

    //     return $query;
    // }

    // public function getModalidadesEspecialista($sede, $especialista, $area)
    // {
    //     $query = $this->db->query(
    //         "SELECT modalidad = CASE WHEN tipoCita = 1 then 'PRESENCIAL' WHEN tipoCita = 2 THEN 'EN LíNEA' END,
    //         us.idUsuario as id, us.idPuesto, us.nombre AS especialista, o.ubicación as ubicacionOficina, axs.tipoCita, axs.idAtencionXSede, se.sede as lugarAtiende
    //         FROM atencionXSede axs
    //         INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
    //         LEFT JOIN oficinas o ON o.idoficina = axs.idOficina
    //         INNER JOIN sedes se ON se.idSede = us.idSede
    //         WHERE axs.estatus = ? AND axs.idSede = ? AND ((axs.idEspecialista = ? AND axs.idArea is NULL ) OR (axs.idEspecialista = ? AND axs.idArea = ?))", 
    //         array(1, $sede, $especialista, $especialista, $area));

    //     return $query;
    // }

    // public function getReasons($puesto){
    //     $query = $this->db->query("SELECT *from opcionesPorCatalogo where idCatalogo = ?", $puesto);

    //     return $query->result();
    // }

    // public function getOficinaByAtencion($sede, $beneficio, $especialista, $modalidad)
    // {
    //     $query = $this->db->query(
    //         "SELECT axs.idAtencionXSede, axs.idEspecialista, axs.idSede, axs.tipoCita,  axs.estatus,
    //         ofi.idOficina, ofi.oficina, ofi.ubicación
    //         from atencionXSede AS axs
    //         INNER JOIN oficinas AS ofi ON axs.idOficina = ofi.idOficina
    //         WHERE axs.estatus = 1 AND
    //         axs.idSede = ? AND axs.idEspecialista = ? AND axs.tipoCita = ?", array($sede, $especialista, $modalidad)
    //     );

    //     return $query;
    // }

    // public function isPrimeraCita($usuario, $especialista)
    // {
    //     $query = $this->db->query(
    //         "SELECT *FROM CITAS
    //         WHERE idPaciente = ? AND idEspecialista = ?;",
    //         array($usuario, $especialista)
    //     );

    //     return $query;
    // }

    // public function getCitasSinFinalizarUsuario($usuario, $beneficio)
    // {
    //     $query = $this->db->query(
    //         "SELECT c.*, u.idPuesto FROM citas AS c
    //         INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
    //         WHERE c.idPaciente = ? AND u.idPuesto = ? AND c.estatusCita IN (1, 6);",array($usuario, $beneficio)
    //     );

    //     return $query;
    // }

    // public function getCitasSinEvaluarUsuario($usuario)
    // {
    //     $query = $this->db->query(
    //         "SELECT c.*, u.idPuesto FROM citas AS c
    //         INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
    //         WHERE c.idPaciente = ? AND evaluacion is NULL AND c.estatusCita IN (?)",array($usuario, 4)
    //     );

    //     return $query;
    // }

    // public function getCitasSinPagarUsuario($usuario)
    // {
    //     $query = $this->db->query(
    //         "SELECT c.*, u.idPuesto FROM citas AS c
    //         INNER JOIN usuarios as u ON c.idEspecialista = u.idUsuario
    //         WHERE c.idPaciente = ? AND idDetalle is NULL AND c.estatusCita IN (?);",array($usuario, 6)
    //     );

    //     return $query;
    // }

    // public function getCitasFinalizadasUsuario($usuario, $mes, $año)
    // {
    //     $query = $this->db->query(
    //         "SELECT *FROM citas
    //         WHERE idPaciente = ? AND MONTH(fechaInicio) = ?
    //         AND YEAR(fechaInicio) = ? AND estatusCita IN (4, 1) AND tipoCita IN (1, 2);", array($usuario, $mes, $año)
    //     );

    //     return $query;
    // }

    // public function getAtencionPorSede($especialista, $sede, $modalidad)
    // {
    //     $query = $this->db->query(
    //         "SELECT *FROM atencionXSede 
    //         WHERE estatus = 1 AND idEspecialista = ? 
    //         AND idSede = ? AND tipoCita = ? ;", array($especialista, $sede, $modalidad)
    //     );

    //     return $query;
    // }

    // public function getPending($idUsuario){
    //     $query = $this->db->query("SELECT ct.idCita as id, ct.titulo, ct.fechaInicio as 'start', ct.fechaFinal as 'end', usEsp.nombre as especialista, usBen.correo, sed.sede, ofi.oficina,
    //     beneficio = CASE 
    //     WHEN pue.idPuesto = 537 THEN 'nutrición'
    //     WHEN pue.idPuesto = 585 THEN 'psicología'
    //     WHEN pue.idPuesto = 686 THEN 'guía espiritual'
    //     WHEN pue.idPuesto = 158 THEN 'quantum balance'
    //     END
    //     FROM citas ct
    //     INNER JOIN usuarios usBen ON usBen.idUsuario = ct.idPaciente
    //     INNER JOIN usuarios usEsp ON usEsp.idUsuario = ct.idEspecialista
    //     INNER JOIN puestos pue ON usEsp.idPuesto = pue.idPuesto
    //     INNER JOIN atencionXSede ats ON ats.idAtencionXSede = ct.idAtencionXSede
    //     INNER JOIN sedes sed ON sed.idSede = ats.idSede
    //     LEFT JOIN oficinas ofi ON ofi.idOficina = ats.idOficina
    //     WHERE estatusCita IN(?) AND ct.idEspecialista = ? AND fechaInicio < GETDATE()", array(1, $idUsuario));

    //     return $query;
    // }

    // public function getPendientesPago($idUsuario){
    //     $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
    //     ct.fechaInicio AS occupied, ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina',
    //     CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación', sed.sede , atc.idOficina, us.correo, usEspe.correo as correoEspecialista, 
    //     usEspe.nombre as especialista, ct.idDetalle, usEspe.telPersonal as telefonoEspecialista,
    //     usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
    //     beneficio = CASE 
    //     WHEN pue.idPuesto = 537 THEN 'Nutrición'
    //     WHEN pue.idPuesto = 585 THEN 'Psicología'
    //     WHEN pue.idPuesto = 686 THEN 'Guía espiritual'
    //     WHEN pue.idPuesto = 158 THEN 'Quantum balance'
    //     END
    //     FROM citas ct
    //     INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //     INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
    //     INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
    //     LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
    //     INNER join sedes sed ON sed.idSede = atc.idSede
    //     INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
    //     LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
    //                     FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
    //     WHERE ct.estatusCita IN(?) AND ct.idPaciente = ?", array(6, $idUsuario));

    //     return $query; 
    // }

    // public function getPendientesEvaluacion($idUsuario){
    //     $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
    //     ct.fechaInicio AS occupied, ct.estatusCita AS estatus, us.nombre, ct.idPaciente, us.telPersonal, CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina',
    //     CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación', sed.sede , atc.idOficina, us.correo, usEspe.correo as correoEspecialista, 
    //     usEspe.nombre as especialista, ct.idDetalle, usEspe.telPersonal as telefonoEspecialista,
    //     usEspe.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
    //     beneficio = CASE 
    //     WHEN pue.idPuesto = 537 THEN 'Nutrición'
    //     WHEN pue.idPuesto = 585 THEN 'Psicología'
    //     WHEN pue.idPuesto = 686 THEN 'Guía espiritual'
    //     WHEN pue.idPuesto = 158 THEN 'Quantum balance'
    //     END
    //     FROM citas ct
    //     INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //     INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
    //     INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
    //     LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
    //     INNER join sedes sed ON sed.idSede = atc.idSede
    //     INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
    //     LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
    //                     FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
    //     WHERE ct.estatusCita IN(?) AND ct.evaluacion is NULL AND ct.idPaciente = ?", array(4, $idUsuario));

    //     return $query;
    // }

    // public function getDetallePago($folio){
    //     $query = $this->db->query("SELECT * FROM detallePagos WHERE folio = ?", array($folio));

    //     return $query;
    // }

    // public function getEventReasons($idCita){
    //     $query = $this->db->query("SELECT oxc.idOpcion, oxc.nombre FROM motivosPorCita AS mpc
    //     INNER JOIN opcionesPorCatalogo AS oxc ON oxc.idOpcion = mpc.idMotivo
    //     INNER JOIN citas AS c ON c.idCita = mpc.idCita
    //     INNER JOIN usuarios AS u ON u.idUsuario = c.idEspecialista
    //     INNER JOIN puestos AS p ON p.idPuesto = u.idPuesto WHERE c.idCita = ?
    //     AND idCatalogo = 
    //         CASE P.idPuesto
    //             WHEN 537 THEN 8
    //             WHEN 585 THEN 7
    //             WHEN 802 THEN 7
    //             WHEN 859 THEN 7
    //             WHEN 686 THEN 9
    //             WHEN 158 THEN 6
    //         END",
    //         $idCita
    //     );

    //     return $query;
    // }

    // public function getLastAppointment($usuario, $beneficio) {
    //     $query = $this->db->query("SELECT TOP (1) ct.*, usu.idPuesto, axs.tipoCita FROM citas AS ct
    //     INNER JOIN usuarios AS usu ON usu.idUsuario = ct.idEspecialista
    //     INNER JOIN atencionXSede AS axs ON axs.idAtencionXSede = ct.idAtencionXSede
    //     WHERE ct.idPaciente = ? AND usu.idPuesto = ?
    //     ORDER BY idCita DESC", array($usuario, $beneficio));
    
    //     return $query;
    // }
    
    // public function checkInvoice($idDetalle){
    //     $query = $this->db->query("SELECT idDetalle FROM citas WHERE idDetalle = ? GROUP BY idDetalle HAVING COUNT(idDetalle) > ?", array($idDetalle, 2));

    //     return $query;
    // }

    // public function checkDetailPacient($user, $column){
    //     $query = $this->db->query("SELECT $column FROM detallePaciente 
    //         WHERE idUsuario = ?;", array($user));
   
    //     return $query;
    // }

    // public function getCitaById($idCita){
    //     $query = $this->db->query("SELECT CAST(idCita AS VARCHAR(36)) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
    //     ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, us.nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
    //     ct.tipoCita, atc.tipoCita as modalidad, atc.idSede ,usEspe.idPuesto, us.telPersonal, usEspe.telPersonal as telefonoEspecialista, 
    //     CASE WHEN ofi.oficina IS NULL THEN 'VIRTUAL' ELSE ofi.oficina END as 'oficina', CASE WHEN ofi.ubicación IS NULL THEN 'VIRTUAL' ELSE ofi.ubicación END as 'ubicación',
    //     pue.idArea, sed.sede, atc.idOficina, us.correo, usEspe.correo as correoEspecialista, usEspe.nombre as especialista, usEspe.sexo as sexoEspecialista,
    //     tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
    //     'color' = CASE
    //             WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500'
	//             WHEN ct.estatusCita = 2 THEN '#ff0000'
	//             WHEN ct.estatusCita = 3 THEN '#808080'
	//             WHEN ct.estatusCita = 4 THEN '#008000'
    //             WHEN ct.estatusCita = 5 THEN '#ff4d67'
    //             WHEN ct.estatusCita = 6 THEN '#00ffff'
    //             WHEN ct.estatusCita = 7 THEN '#ff0000'
    //             WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff'
    //     END,
    //     beneficio = CASE 
    //         WHEN pue.idPuesto = 537 THEN 'nutrición'
    //         WHEN pue.idPuesto = 585 THEN 'psicología'
    //         WHEN pue.idPuesto = 686 THEN 'guía espiritual'
    //         WHEN pue.idPuesto = 158 THEN 'quantum balance'
    //     END
    //     FROM citas ct
    //     INNER JOIN usuarios us ON us.idUsuario = ct.idPaciente
    //     INNER JOIN usuarios usEspe ON usEspe.idUsuario = ct.idEspecialista
    //     INNER join atencionXSede atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
    //     LEFT join oficinas ofi ON ofi.idOficina = atc.idOficina
    //     INNER join sedes sed ON sed.idSede = atc.idSede
    //     INNER JOIN puestos pue ON pue.idPuesto = usEspe.idPuesto
    //     LEFT JOIN (SELECT idDetalle, string_agg(FORMAT(fechaInicio, 'HH:mm MMMM d yyyy','es-US'), ' ,') as fechasFolio 
    //                     FROM citas WHERE estatusCita IN(8) GROUP BY citas.idDetalle) tf ON tf.idDetalle = ct.idDetalle
    //     WHERE idCita = ? ",
    //     array( $idCita ));

    //     return $query;
    // }

    // public function getSedesDeAtencionEspecialista($idUsuario){
    //     $query = $this->db->query(
    //     "SELECT ate.idSede as value, sedes.sede as label
    //     FROM atencionXSede ate
    //     LEFT JOIN sedes ON sedes.idSede=ate.idSede
    //     WHERE ate.idEspecialista=? AND ate.tipoCita=?", array($idUsuario, 1));

    //     return $query;
    // }

    // public function getDiasDisponiblesAtencionEspecialista($idUsuario, $idSede){
    //     $query = $this->db->query(
    //     "SELECT * FROM presencialXSede
    //     WHERE idEspecialista=? AND idSede=?
    //     AND MONTH(presencialDate) >= MONTH(CAST(GETDATE() AS DATE))
    //     AND MONTH(presencialDate) <= MONTH(DATEADD(MONTH, 1, CAST(GETDATE() AS DATE)));", array($idUsuario, $idSede));
    
    //     return $query;
    // }


    /* --------------------------------------------- ----- --------------------------------------------------------- */
	/* --------------------------------------------- MYSQL --------------------------------------------------------- */
    /* --------------------------------------------- ----- --------------------------------------------------------- */

    public function getAppointmentsByUser($year, $month, $idUsuario){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end',
            ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre,
            ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, ct.tipoCita, atc.tipoCita as modalidad, atc.idSede , usEspe2.idpuesto AS idPuesto, 
            us2.telefono_personal AS telPersonal, usEspe2.telefono_personal as telefonoEspecialista, usEspe2.idarea AS idArea, sed.nsede AS sede, 
            atc.idOficina, us2.mail_emp AS correo , usEspe2.mail_emp as correoEspecialista, 
            CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) AS especialista,
            usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
            CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
            CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', 
            CASE WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500' WHEN ct.estatusCita = 2 THEN '#ff0000' WHEN ct.estatusCita = 3 THEN '#808080' WHEN ct.estatusCita = 4 THEN '#008000' WHEN ct.estatusCita = 5 THEN '#ff4d67' WHEN ct.estatusCita = 6 THEN '#00ffff' WHEN ct.estatusCita = 7 THEN '#ff0000' WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff' END AS 'color', 
            CASE WHEN usEspe2.idpuesto = 537 THEN 'nutrición' WHEN usEspe2.idpuesto = 585 THEN 'psicología' WHEN usEspe2.idpuesto = 686 THEN 'guía espiritual' WHEN usEspe2.idpuesto = 158 THEN 'quantum balance' END AS 'beneficio'
            FROM PRUEBA_beneficiosCM.citas AS ct 
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idPaciente 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
            INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede 
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS sed ON sed.idsede = atc.idSede 
            LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM PRUEBA_beneficiosCM.citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
            WHERE YEAR(fechaInicio) = ? AND MONTH(fechaInicio) = ? AND ct.idPaciente = ? AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?);",
            array( $year, $month, $idUsuario, 1, 2, 3, 4, 5, 6, 7)
        );

        return $query;
    }

    public function getBeneficiosPorSede($sede)
	{
        $query = $this->ch->query(
            "SELECT DISTINCT us2.idpuesto as 'idPuesto', us2.npuesto as 'puesto'
            FROM PRUEBA_beneficiosCM.usuarios AS us 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            RIGHT JOIN PRUEBA_beneficiosCM.atencionxsede AS axs ON axs.idEspecialista = us.idUsuario
            INNER JOIN PRUEBA_beneficiosCM.opcionesporcatalogo AS opc ON opc.idOpcion= axs.tipoCita
            INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS s ON s.idsede = us2.idsede
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas as ofi ON ofi.idoficina = axs.idOficina
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes AS so ON so.idsede = ofi.idsede
            WHERE us.estatus = 1 AND s.estatus_sede = 1 AND axs.estatus = 1  AND us.idRol = 3 AND opc.idCatalogo = 5
            AND axs.idSede = 1 ;", $sede
        );

        return $query;
	}

    public function getEspecialistaPorBeneficioYSede($sede, $area, $beneficio)
    {
        $query = $this->ch->query(
            "SELECT DISTINCT us.idUsuario as id, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista
            FROM PRUEBA_beneficiosCM.usuarios AS us
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
            RIGHT JOIN PRUEBA_beneficiosCM.atencionxsede AS axs ON axs.idEspecialista = us.idUsuario 
            INNER JOIN PRUEBA_beneficiosCM.opcionesporcatalogo AS opc ON opc.idOpcion= axs.tipoCita 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS s ON s.idsede = us2.idsede 
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas as ofi ON ofi.idoficina = axs.idOficina
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes AS so ON so.idsede = ofi.idsede
            WHERE us.estatus = 1 AND s.estatus_sede = 1 AND axs.estatus = 1 AND us.idRol = 3 AND opc.idCatalogo = 5 
            AND (axs.idSede = ? AND (axs.idArea IS NULL OR axs.idArea = ?)) AND us2.idpuesto = ?;", array($sede, $area, $beneficio)
        );

        return $query;
    }

    public function getModalidadesEspecialista($sede, $especialista, $area)
    {
        $query = $this->ch->query(
            "SELECT CASE WHEN tipoCita = 1 then 'PRESENCIAL' WHEN tipoCita = 2 THEN 'EN LíNEA' END AS 'modalidad', us.idUsuario as id,
            us2.idpuesto,  CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS especialista,
            ofi.direccion as ubicacionOficina, axs.tipoCita, axs.idAtencionXSede, us2.nsede as lugarAtiende 
            FROM PRUEBA_beneficiosCM.atencionxsede AS axs 
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = axs.idEspecialista 
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = axs.idOficina 
            WHERE axs.estatus = ? AND axs.idSede = ? AND ((axs.idEspecialista = ? AND axs.idArea is NULL ) OR (axs.idEspecialista = ? AND axs.idArea = ?));", 
            array(1, $sede, $especialista, $especialista, $area));

        return $query;
    }
    
    public function getDiasDisponiblesAtencionEspecialista($idUsuario, $idSede){
        $query = $this->ch->query(
        "SELECT * FROM PRUEBA_beneficiosCM.presencialxsede 
        WHERE idEspecialista = ? AND idSede= ?
        AND MONTH(presencialDate) >= MONTH(CURDATE()) 
        AND MONTH(presencialDate) <= MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH));", array($idUsuario, $idSede));
    
        return $query;
    }

    public function getOficinaByAtencion($sede, $especialista, $modalidad)
    {
        $query = $this->ch->query(
            "SELECT axs.idAtencionXSede, axs.idEspecialista, axs.idSede, axs.tipoCita,  axs.estatus,
            ofi.idoficina AS 'idOficina', ofi.noficina AS oficina, ofi.direccion AS ubicación
            from PRUEBA_beneficiosCM.atencionxsede AS axs
            INNER JOIN PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON axs.idOficina = ofi.idoficina
            WHERE axs.estatus = 1 AND
            axs.idSede = ? AND axs.idEspecialista = ? AND axs.tipoCita = ?;", array($sede, $especialista, $modalidad)
        );

        return $query;
    }
    
    public function getHorarioBeneficio($beneficio){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.horariosporbeneficio WHERE idBeneficio = ?",
            array($beneficio)
        );
        return $query;
    }

    public function getOccupiedRange($fechaInicio, $fechaFin, $idUsuario){
        $query = $this->ch->query(
            "SELECT idOcupado as id, titulo as title, fechaInicio as occupied, fechaInicio, fechaFinal 
            FROM PRUEBA_beneficiosCM.horariosocupados 
            WHERE idEspecialista = ? AND estatus = ? AND 
            ((fechaInicio BETWEEN ? AND ?) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?));",
            array( $idUsuario, 1, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );
        return $query;
    }

    public function getAppointmentRange($fechaInicio, $fechaFin, $especialista, $usuario){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio, ct.fechaFinal, 
            ct.estatusCita, ct.idPaciente, ct.idEspecialista
            FROM PRUEBA_beneficiosCM.citas ct
            LEFT JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            WHERE (ct.idEspecialista = ? OR ct.idPaciente = ?) AND ct.estatusCita IN (?, ?)
            AND ((fechaInicio BETWEEN ? AND ? ) OR 
            (fechaFinal BETWEEN ? AND ?) OR 
            (fechaInicio >= ? AND fechaFinal <= ?))",
            array( $especialista, $usuario, 1, 6, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin)
        );

        return $query;
    }

    public function getLastAppointment($usuario, $beneficio) {
        $query = $this->ch->query("SELECT ct.*, us2.idPuesto AS 'idPuesto', axs.tipoCita 
        FROM PRUEBA_beneficiosCM.citas AS ct 
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idEspecialista 
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS axs ON axs.idAtencionXSede = ct.idAtencionXSede 
        WHERE ct.idPaciente = ? AND us2.idpuesto = ? ORDER BY idCita DESC LIMIT 1;", array($usuario, $beneficio));
    
        return $query;
    }
    
    public function isPrimeraCita($usuario, $especialista)
    {
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.citas
            WHERE idPaciente = ? AND idEspecialista = ?;",
            array($usuario, $especialista)
        );

        return $query;
    }

    public function getSedesDeAtencionEspecialista($idUsuario){
        $query = $this->ch->query(
        "SELECT axs.idSede as value, s.nsede AS label
        FROM PRUEBA_beneficiosCM.atencionxsede AS axs
        LEFT JOIN PRUEBA_CH.beneficioscm_vista_sedes AS s ON s.idsede = axs.idSede
        WHERE axs.idEspecialista=?  AND axs.tipoCita = ?", array($idUsuario, 1));

        return $query;
    }

    public function checkPresencial($idSede, $idEspecialista, $fecha){
        $query = $this->ch->query(
            "SELECT *from PRUEBA_beneficiosCM.presencialxsede as pxs
            WHERE pxs.idSede = ? AND pxs.idEspecialista = ? AND presencialDate = ?;",
            array( $idSede, $idEspecialista, $fecha)
        );

        return $query;
    }

    public function checkAppointment($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND ((idPaciente = ?
            AND estatusCita IN (?, ?))
            OR (idEspecialista = ? and estatusCita IN (?, ?)))",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $dataValue["idPaciente"],
                1, 6,
                $dataValue["idUsuario"],
                1, 6
            )
        );
        
        return $query;
    }

    public function checkOccupied($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.horariosocupados WHERE 
            ((fechaInicio BETWEEN ? AND ?) 
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal) 
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idUsuario"],
                1
            )
        );

        return $query;
    }

    public function checkOccupiedId($dataValue, $fechaInicioSuma ,$fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.horariosocupados WHERE 
            ((fechaInicio BETWEEN ? AND ?) 
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal) 
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idUnico != ?
            AND idEspecialista = ?
            AND estatus = ?",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["id"],
                $dataValue["idUsuario"],
                1
            )
        );

        return $query;
    }

    public function getReasons($puesto){
        $query = $this->ch->query("SELECT *from PRUEBA_beneficiosCM.opcionesporcatalogo where idCatalogo = ?", $puesto);

        return $query->result();
    }

    public function checkAppointmentId($dataValue, $fecha_inicio_suma, $fecha_final_resta){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.citas WHERE
            ((fechaInicio BETWEEN ? AND ?)
            OR (fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN fechaInicio AND fechaFinal)
            OR (? BETWEEN fechaInicio AND fechaFinal))
            AND idCita != ?
            AND ((idPaciente = ?
            AND estatusCita = ?)
            OR (idEspecialista = ? AND estatusCita IN(?, ?)))",
            array(
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma, $fecha_final_resta,
                $fecha_inicio_suma,
                $fecha_final_resta,
                $dataValue["id"],
                $dataValue["idPaciente"],
                1,
                $dataValue["idUsuario"],
                1,
                6
            )
        );

        return $query;
    }

    public function getCitaById($idCita){
        $query = $this->ch->query("SELECT TRIM(CAST(idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, ct.idDetalle, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) as nombre, ct.idPaciente, ct.idEspecialista, ct.idAtencionXSede, 
        ct.tipoCita, atc.tipoCita as modalidad, atc.idSede, usEspe2.idpuesto, us2.telefono_personal AS 'telPersonal', usEspe2.telefono_personal AS telefonoEspecialista, 
        CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina', CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación',
        usEspe2.idarea AS 'idArea', s.nsede AS 'sede', atc.idOficina, us2.mail_emp AS correo, usEspe2.mail_emp as correoEspecialista, CONCAT(IFNULL(usEspe2.nombre_persona, ''), ' ', IFNULL(usEspe2.pri_apellido, ''), ' ', IFNULL(usEspe2.sec_apellido, '')) as especialista,
        usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
        CASE
        WHEN ct.estatusCita = 1 AND atc.tipoCita = 1 THEN '#ffa500'
        WHEN ct.estatusCita = 2 THEN '#ff0000'
        WHEN ct.estatusCita = 3 THEN '#808080'
        WHEN ct.estatusCita = 4 THEN '#008000'
        WHEN ct.estatusCita = 5 THEN '#ff4d67'
        WHEN ct.estatusCita = 6 THEN '#00ffff'
        WHEN ct.estatusCita = 7 THEN '#ff0000'
        WHEN ct.estatusCita = 1 AND atc.tipoCita = 2 THEN '#0000ff'
        END AS 'color',
        CASE 
        WHEN usEspe2.idpuesto = 537 THEN 'nutrición'
        WHEN usEspe2.idpuesto = 585 THEN 'psicología'
        WHEN usEspe2.idpuesto = 686 THEN 'guía espiritual'
        WHEN usEspe2.idpuesto = 158 THEN 'quantum balance'
        END AS 'beneficio'
        FROM PRUEBA_beneficiosCM.citas ct
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idPaciente
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
        INNER join PRUEBA_beneficiosCM.atencionxsede AS atc  ON atc.idAtencionXSede = ct.idAtencionXSede  
        LEFT join PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
        INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS s ON s.idsede = atc.idSede
                    LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM PRUEBA_beneficiosCM.citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
        WHERE idCita = ?",
        array( $idCita ));

        return $query;
    }

    public function getOccupied($month, $idUsuario, $dates){
        $query = $this->ch->query(
            "SELECT idUnico as id, titulo as title, fechaInicio as 'start', fechaFinal as 'end',
            'purple' AS 'color', estatus, 'cancel' AS 'type'
            FROM PRUEBA_beneficiosCM.horariosocupados
            WHERE YEAR(fechaInicio) IN(?, ?)
            AND MONTH(fechaInicio) IN(?, ?, ?)
            AND idEspecialista = ?  
            AND estatus = ?",
            array( $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1 )
        );
        return $query;
    }

    public function checkInvoice($idDetalle){
        $query = $this->ch->query("SELECT idDetalle FROM PRUEBA_beneficiosCM.citas WHERE idDetalle = ? GROUP BY idDetalle HAVING COUNT(idDetalle) > ?", array($idDetalle, 2));

        return $query;
    }

    public function checkDetailPacient($user, $column){
        $query = $this->ch->query("SELECT $column FROM PRUEBA_beneficiosCM.detallepaciente 
        WHERE idUsuario = ?;", array($user));
   
        return $query;
    }

    public function getEventReasons($idCita){
        $query = $this->ch->query("SELECT oxc.idOpcion, oxc.nombre FROM PRUEBA_beneficiosCM.motivosporcita AS mpc
        INNER JOIN PRUEBA_beneficiosCM.opcionesporcatalogo AS oxc ON oxc.idOpcion = mpc.idMotivo
        INNER JOIN PRUEBA_beneficiosCM.citas AS ct ON ct.idCita = mpc.idCita
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idEspecialista
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        WHERE ct.idCita = ? AND idCatalogo = 
        CASE us2.idpuesto
        WHEN 537 THEN 8
        WHEN 585 THEN 7
        WHEN 802 THEN 7
        WHEN 859 THEN 7
        WHEN 686 THEN 9
        WHEN 158 THEN 6
        END", $idCita );

        return $query;
    }

    public function getDetallePago($folio){
        $query = $this->ch->query("SELECT * FROM PRUEBA_beneficiosCM.detallepagos WHERE folio = ?", array($folio));

        return $query;
    }

    public function getAtencionPorSede($especialista, $sede, $area, $modalidad)
    {
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.atencionxsede 
            WHERE estatus = 1 AND idEspecialista = ?
            AND ((idSede = ? AND idArea is NULL ) OR (idSede = ? AND idArea = ?))
            AND tipoCita = ? ;", array($especialista, $sede, $sede, $area, $modalidad)
        );
        return $query;
    }

    public function getIdAtencion($dataValue){
        $query = $this->ch->query(
            "SELECT idAtencionXSede FROM PRUEBA_beneficiosCM.atencionxsede 
            WHERE idEspecialista = ?
            AND idSede = ( 
                SELECT idSede FROM PRUEBA_beneficiosCM.usuarios AS us 
				INNER JOIN AS PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
				WHERE idUsuario = ? ) AND estatus = ?", 
            array($dataValue["idUsuario"], $dataValue["idUsuario"], 1)
        );
        
        return $query;
    }

    public function getCitasFinalizadasUsuario($usuario, $mes, $año)
    {
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.citas
            WHERE idPaciente = ? AND MONTH(fechaInicio) = ?
            AND YEAR(fechaInicio) = ? AND estatusCita IN (4, 1) AND tipoCita IN (1, 2);", array($usuario, $mes, $año)
        );

        return $query;
    }

    public function getCitasSinPagarUsuario($usuario)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM PRUEBA_beneficiosCM.citas AS ct
            WHERE ct.idPaciente = ? AND ct.idDetalle is NULL AND ct.estatusCita IN (?);",array($usuario, 6)
        );

        return $query;
    }

    public function getCitasSinEvaluarUsuario($usuario)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM PRUEBA_beneficiosCM.citas AS ct
            WHERE ct.idPaciente = ? AND ct.evaluacion is NULL AND ct.estatusCita IN (?)",array($usuario, 4)
        );

        return $query;
    }

    public function getCitasSinFinalizarUsuario($usuario, $beneficio)
    {
        $query = $this->ch->query(
            "SELECT ct.idCita FROM PRUEBA_beneficiosCM.citas AS ct
            INNER JOIN PRUEBA_beneficiosCM.usuarios as us ON ct.idEspecialista = us.idUsuario
            INNER JOIN AS PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato 
            WHERE ct.idPaciente = ? AND us2.idpuesto = ? AND ct.estatusCita IN (1, 6);",array($usuario, $beneficio)
        );

        return $query;
    }

    public function getAppointment($month, $idUsuario, $dates){
        $query = $this->ch->query(
            "SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id,  ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end',
            ct.fechaInicio AS occupied, 'date' AS 'type', ct.estatusCita AS estatus, CONCAT(us2.nombre_persona, us2.pri_apellido, us2.sec_apellido) AS nombre, ct.idPaciente, us2.telefono_personal AS telPersonal, us2.mail_emp AS correo,
            se.nsede AS sede, ofi.noficina as oficina, ct.idDetalle, ct.idAtencionXSede, us.externo, CONCAT(usEspCH.nombre_persona, usEspCH.pri_apellido, usEspCH.sec_apellido) as especialista, ct.fechaCreacion, usEspCH.tipo_puesto AS tipoPuesto,
            tf.fechasFolio, idEventoGoogle, ct.tipoCita, aps.tipoCita as modalidad, aps.idSede,
            CASE
                WHEN ct.estatusCita = 0 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND aps.tipoCita = 1 THEN '#ffa500'
                WHEN ct.estatusCita = 2 THEN '#ff0000'
                WHEN ct.estatusCita = 3 THEN '#808080'
                WHEN ct.estatusCita = 4 THEN '#008000'
                WHEN ct.estatusCita = 5 THEN '#ff4d67'
                WHEN ct.estatusCita = 6 THEN '#00ffff'
                WHEN ct.estatusCita = 7 THEN '#ff0000'
                WHEN ct.estatusCita = 1 AND aps.tipoCita = 2 THEN '#0000ff'
            END AS color,
            CASE
            WHEN usEspCH.idPuesto = 537 THEN 'nutrición'
            WHEN usEspCH.idPuesto= 585 THEN 'psicología'
            WHEN usEspCH.idPuesto = 686 THEN 'guía espiritual'
            WHEN usEspCH.idPuesto = 158 THEN 'quantum balance'
            END AS beneficio
            FROM PRUEBA_beneficiosCM.citas AS ct
            INNER JOIN PRUEBA_beneficiosCM.usuarios us ON us.idUsuario = ct.idPaciente
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEspCH ON usEspCH.idcontrato = usEspe.idContrato
            INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS aps ON ct.idAtencionXSede = aps.idAtencionXSede
            INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS se ON se.idsede = aps.idSede
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = aps.idOficina
            LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM PRUEBA_beneficiosCM.citas 
            WHERE estatusCita IN( ? ) AND citas.idCita = idCita GROUP BY citas.idDetalle) AS tf
            ON tf.idDetalle = ct.idDetalle
            WHERE YEAR(fechaInicio) IN (?, ?)
            AND MONTH(fechaInicio) IN (?, ?, ?)
            AND ct.idEspecialista = ?
            AND ct.estatusCita IN(?, ?, ?, ?, ?, ?, ?)",
            array( 8, $dates["year1"], $dates["year2"], $dates["month1"], $month, $dates["month2"], $idUsuario, 1, 2, 3, 4, 5, 6, 7 )
        );

        return $query;
    }

    public function checkAppointmentNormal($dataValue, $fechaInicioSuma, $fechaFinalResta){
        $query = $this->ch->query(
            "SELECT *FROM PRUEBA_beneficiosCM.citas AS ct WHERE
            ((ct.fechaInicio BETWEEN ? AND ?)
            OR (ct.fechaFinal BETWEEN ? AND ?)
            OR (? BETWEEN ct.fechaInicio AND ct.fechaFinal)
            OR (? BETWEEN ct.fechaInicio AND ct.fechaFinal))
            AND ct.idEspecialista = ? AND ct.estatusCita IN(?, ?)",
            array(
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma, $fechaFinalResta,
                $fechaInicioSuma,
                $fechaFinalResta,
                $dataValue["idUsuario"],
                1,
                6
            )
        );
        
        return $query;
    }

    public function getPending($idUsuario){
        $query = $this->ch->query(
            "SELECT ct.idCita as id, ct.titulo, ct.fechaInicio as 'start', ct.fechaFinal as 'end', 
            CONCAT(usEsp2.nombre_persona, usEsp2.pri_apellido, usEsp2.sec_apellido) AS especialista, us2.mail_emp as correo, sed.nsede as sede, ofi.noficina AS oficina,
            CASE 
            WHEN usEsp2.idpuesto = 537 THEN 'nutrición'
            WHEN usEsp2.idpuesto = 585 THEN 'psicología'
            WHEN usEsp2.idpuesto = 686 THEN 'guía espiritual' 
            WHEN usEsp2.idpuesto = 158 THEN 'quantum balance'
            END AS 'beneficio'
            FROM PRUEBA_beneficiosCM.citas AS ct
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idPaciente
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEsp ON usEsp.idUsuario = ct.idEspecialista
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEsp2 ON usEsp2.idcontrato = usEsp.idContrato
            INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS ats ON ats.idAtencionXSede = ct.idAtencionXSede
            INNER JOIN PRUEBA_CH.beneficioscm_vista_sedes AS sed ON sed.idsede = ats.idSede
            LEFT JOIN PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = ats.idOficina
            WHERE estatusCita IN(1) AND ct.idEspecialista = 8 AND fechaInicio < CURRENT_TIMESTAMP();", array(1, $idUsuario));

        return $query;
    }

    public function getPendientesPago($idUsuario){
        $query = $this->ch->query("        SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
        ct.fechaInicio AS occupied, ct.estatusCita AS estatus, CONCAT(us2.nombre_persona, us2.pri_apellido, us2.sec_apellido) AS nombre, ct.idPaciente, us2.telefono_personal, CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
        CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', sed.nsede AS sede, atc.idOficina, us2.mail_emp as correo, usEspe2.mail_emp as correoEspecialista, 
        CONCAT(usEspe2.nombre_persona, usEspe2.pri_apellido, usEspe2.sec_apellido) AS especialista, ct.idDetalle, usEspe2.telefono_personal as telefonoEspecialista,
        usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
        CASE WHEN us cEspe2.idPuesto = 537 THEN 'Nutrición'
        WHEN usEspe2.idPuesto = 585 THEN 'Psicología'
        WHEN usEspe2.idPuesto = 686 THEN 'Guía espiritual'
        WHEN usEspe2.idPuesto = 158 THEN 'Quantum balance'
        END AS beneficio
        FROM PRUEBA_beneficiosCM.citas AS ct
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idPaciente
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
        INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
        INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
        INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede
        LEFT join PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
        INNER join PRUEBA_CH.beneficioscm_vista_sedes AS sed ON sed.idSede = atc.idSede
		  LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM PRUEBA_beneficiosCM.citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
        WHERE ct.estatusCita IN(?) AND ct.idPaciente = ?", array(6, $idUsuario));

        return $query;
    }

    public function getPendientesEvaluacion($idUsuario){
        $query = $this->ch->query("SELECT TRIM(CAST(ct.idCita AS CHAR(36))) AS id, ct.titulo AS title, ct.fechaInicio AS 'start', ct.fechaFinal AS 'end', 
            ct.fechaInicio AS occupied, ct.estatusCita AS estatus, CONCAT(us2.nombre_persona, us2.pri_apellido, us2.sec_apellido) AS nombre, ct.idPaciente, us2.telefono_personal, CASE WHEN ofi.noficina IS NULL THEN 'VIRTUAL' ELSE ofi.noficina END as 'oficina',
            CASE WHEN ofi.direccion IS NULL THEN 'VIRTUAL' ELSE ofi.direccion END as 'ubicación', sed.nsede AS sede, atc.idOficina, us2.mail_emp as correo, usEspe2.mail_emp as correoEspecialista, 
            CONCAT(usEspe2.nombre_persona, usEspe2.pri_apellido, usEspe2.sec_apellido) AS especialista, ct.idDetalle, usEspe2.telefono_personal as telefonoEspecialista,
            usEspe2.sexo as sexoEspecialista, tf.fechasFolio, ct.idEventoGoogle, ct.evaluacion,
            CASE 
            WHEN usEspe2.idPuesto = 537 THEN 'Nutrición'
            WHEN usEspe2.idPuesto = 585 THEN 'Psicología'
            WHEN usEspe2.idPuesto = 686 THEN 'Guía espiritual'
            WHEN usEspe2.idPuesto = 158 THEN 'Quantum balance'
            END AS beneficio
            FROM PRUEBA_beneficiosCM.citas AS ct
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS us ON us.idUsuario = ct.idPaciente
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS us2 ON us2.idcontrato = us.idContrato
            INNER JOIN PRUEBA_beneficiosCM.usuarios AS usEspe ON usEspe.idUsuario = ct.idEspecialista
            INNER JOIN PRUEBA_CH.beneficioscm_vista_usuarios AS usEspe2 ON usEspe2.idcontrato = usEspe.idContrato
            INNER JOIN PRUEBA_beneficiosCM.atencionxsede AS atc ON atc.idAtencionXSede = ct.idAtencionXSede  
            LEFT join PRUEBA_CH.beneficioscm_vista_oficinas AS ofi ON ofi.idoficina = atc.idOficina
            INNER join PRUEBA_CH.beneficioscm_vista_sedes AS sed ON sed.idSede = atc.idSede
		    LEFT JOIN (SELECT idDetalle, GROUP_CONCAT(DATE_FORMAT(fechaInicio, '%d / %m / %Y A las %H:%i horas.'), '') AS fechasFolio FROM PRUEBA_beneficiosCM.citas WHERE estatusCita IN(8) GROUP BY idDetalle) tf ON tf.idDetalle = ct.idDetalle 
            WHERE ct.estatusCita IN(?) AND ct.evaluacion is NULL AND ct.idPaciente = ?", array(4, $idUsuario));

        return $query;
    }
}