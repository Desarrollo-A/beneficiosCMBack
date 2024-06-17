<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class DashModel extends CI_Model
{
	public function __construct()
  {
      $this->schema_cm = $this->config->item('schema_cm');
      $this->schema_ch = $this->config->item('schema_ch');
      $this->ch = $this->load->database('ch', TRUE);
      parent::__construct();
  }
  
	public function getPregunta($dt)
	{
		$idArea = isset($dt[0]["idArea"]) ? $dt[0]["idArea"] : 0;
		$tipoEnc = isset($dt[1]["tipoEnc"]) ? $dt[1]["tipoEnc"] : 0;

		$query = $this->ch->query("SELECT DISTINCT ec.idPregunta, pg.pregunta, ec.tipoEncuesta, ec.respuestas, pg.idPregunta, ec.idEncuesta, ec.idEncuestaCreada, ec.idArea, ec.idPregunta
		FROM ". $this->schema_cm .".encuestascreadas ec
		LEFT JOIN ". $this->schema_cm .".preguntasgeneradas pg ON pg.idPregunta = ec.idPregunta AND pg.idEncuesta = ec.idEncuesta
		WHERE ec.estatus = 1 AND ec.tipoEncuesta = $tipoEnc AND (pg.abierta = 1 AND ec.idArea = $idArea AND pg.idArea = $idArea AND ec.respuestas != 5)");

		$result = $query->result();

		if (!empty($result))
			return $result;
		else {
			return false;
		}
	}

	public function getRespuestas($dt)
	{

		if (!empty($dt)) {

			$idRol = isset($dt[3]["idRol"]) ? $dt[3]["idRol"] : $dt[4]["idRol"];

			if ($idRol == 2) {
				return false;
			} else

			$idPregunta = isset($dt[0]["idPregunta"]) ? $dt[0]["idPregunta"] : 0;
			$idEncuesta = isset($dt[1]["idEncuesta"]) ? $dt[1]["idEncuesta"] : 0;
			$idEspecialidad = isset($dt[2]["idArea"]) ? $dt[2]["idArea"] : $dt[3]["idArea"];
			$tipoEnc = isset($dt[4]["tipoEnc"]) ? $dt[4]["tipoEnc"] : 0;

			if (($idPregunta != 0) ||
				($idEncuesta != 0)
			) {

				$query_pregunta = $this->ch->query(
					"SELECT DISTINCT pg.pregunta  
					FROM ". $this->schema_cm .".encuestascreadas ec
					INNER JOIN ". $this->schema_cm .".preguntasgeneradas pg ON pg.idPregunta = ec.idPregunta
					WHERE ec.estatus = 1 AND abierta = 1 AND pg.idArea = ? AND pg.idPregunta = ? AND ec.idPregunta = ? AND ec.tipoEncuesta = ?",
					array($idEspecialidad, $idPregunta, $idPregunta, $tipoEnc)
				);

				if ($query_pregunta->num_rows() > 0) {

					$idPrg = [];
					foreach ($query_pregunta->result() as $row) {
						$idPrg[] = "'" . $row->pregunta . "'";
					}

					$idPreguntasString = implode(",", $idPrg);

					$query = $this->ch->query(
						"SELECT GROUP_CONCAT(rg.respuesta SEPARATOR ', ') AS respuestas, rg.grupo  
						FROM ". $this->schema_cm .".encuestascreadas ec
						INNER JOIN ". $this->schema_cm .".respuestasgenerales rg ON rg.grupo = ec.respuestas 
						INNER JOIN ". $this->schema_cm .".preguntasgeneradas pg ON pg.idPregunta = ec.idPregunta
						WHERE pg.pregunta IN ($idPreguntasString) AND ec.idEncuesta = $idEncuesta AND pg.idEncuesta = $idEncuesta
						AND ec.tipoEncuesta = $tipoEnc
						GROUP BY rg.grupo;",
						array($idEncuesta)
					);

					return $query->result();
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getCountRespuestas($dt)
	{

		if (!empty($dt)) {

			$idPregunta = isset($dt[0]["idPregunta"]) ? $dt[0]["idPregunta"] : 0;
			$idEncuesta = isset($dt[1]["idEncuesta"]) ? $dt[1]["idEncuesta"] : 0;
			$idEspecialista = isset($dt[2]["idEspecialista"]) ? $dt[2]["idEspecialista"] : 0;
			$fhI = isset($dt[4]["fhI"]) ? $dt[4]["fhI"] : $dt[5]["fhI"];
        	$fechaFn = isset($dt[5]["fhF"]) ? $dt[5]["fhF"] : $dt[6]["fhF"];
			$area = isset($dt[6]["idArea"]) ? $dt[6]["idArea"] : $dt[7]["idArea"];
			$tipoEnc = isset($dt[7]["tipoEnc"]) ? $dt[7]["tipoEnc"] : 0;

			$fecha = new DateTime($fechaFn);
			$fecha->modify('+1 day');
			$fhF = $fecha->format('Y-m-d');

			$idRol = isset($dt[3]["idRol"]) ? $dt[3]["idRol"] : $dt[4]["idRol"];

			if (($idPregunta != 0) ||
				($idEncuesta != 0) ||
				($idEspecialista != 0) ||
				($idRol != 0 && $idRol != 2)
			) {

				if($idEspecialista == 0){

					$query = $this->ch->query("SELECT 
					rg.respuesta, 
					COUNT(*) AS cantidad, 
					(COUNT(*) * 100.0 / total_count.total) AS porcentaje, 
					ec.idPregunta, 
					ec.idArea, 
					ec.idEncuesta
					FROM ". $this->schema_cm .".encuestascontestadas ec
					INNER JOIN ". $this->schema_cm .".respuestasgenerales rg ON rg.idRespuestaGeneral = ec.idRespuesta
					CROSS JOIN 
					(SELECT COUNT(*) AS total FROM ". $this->schema_cm .".encuestascontestadas WHERE idEncuesta = $idEncuesta AND idPregunta = $idPregunta AND idArea = $area) AS total_count
					WHERE ec.idEncuesta = $idEncuesta AND ec.idPregunta = $idPregunta AND ec.idArea = $area
					AND (ec.fechaCreacion >= '$fhI' AND ec.fechaCreacion <= '$fhF')
					GROUP BY rg.respuesta, ec.idPregunta, ec.idArea, ec.idEncuesta;");

				}else{

					$query = $this->ch->query("SELECT 
					rg.respuesta, 
					COUNT(*) AS cantidad, 
					(COUNT(*) * 100.0 / total_count.total) AS porcentaje, 
					ec.idPregunta, 
					ec.idArea, 
					ec.idEncuesta
					FROM ". $this->schema_cm .".encuestascontestadas ec
					INNER JOIN ". $this->schema_cm .".respuestasgenerales rg ON rg.idRespuestaGeneral = ec.idRespuesta
					CROSS JOIN 
					(SELECT COUNT(*) AS total FROM ". $this->schema_cm .".encuestascontestadas WHERE idEncuesta = $idEncuesta AND idPregunta = $idPregunta AND idArea = $area) AS total_count
					WHERE ec.idEncuesta = $idEncuesta AND ec.idPregunta = $idPregunta AND ec.idEspecialista = $idEspecialista
					AND (ec.fechaCreacion >= '$fhI' AND ec.fechaCreacion <= '$fhF')
					GROUP BY rg.respuesta, ec.idPregunta, ec.idArea, ec.idEncuesta;");
					
				}

				if ($query->num_rows() > 0) {

					return $query->result();
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getMetas($dt)
	{
		$idData = $dt["idData"];
		$idRol = $dt["idRol"];
		$inicio = $dt["inicio"];
		$fin = $dt["fin"];

		$query = "SELECT COUNT(*) AS citas FROM citas
        	WHERE
        		idEspecialista = $idData";

		return $this->ch->query($query)->result();
	}

	public function getEsp($dt)
    {

		$query = $this->ch-> query("SELECT idUsuario, CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS nombre
		FROM ". $this->schema_cm .".usuarios us
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato
		WHERE idRol = 3 AND us2.idpuesto = $dt");
        
        return $query->result();
    }

	public function getCtDisponibles($dt)
    {

		$query = $this->ch-> query("SELECT COUNT(idCita) AS total
		FROM ". $this->schema_cm .".citas 
		WHERE idPaciente = $dt
		AND estatusCita = 4 
		AND YEAR(fechaFinal) = YEAR(NOW())
		AND MONTH(fechaFinal) = MONTH(NOW())");
        
        return $query;
    }

	public function getCtAsistidas($dt)
    {
        $query = $this->ch-> query("SELECT COUNT(*) AS `asistencia` 
		FROM ". $this->schema_cm .".citas 
		WHERE idPaciente = $dt AND estatusCita = 4;");

        return $query;
    }

    public function getCtCanceladas($dt)
    {
        $query = $this->ch-> query("SELECT COUNT(*) AS `cancelada` 
		FROM ". $this->schema_cm .".citas 
		WHERE idPaciente = $dt AND estatusCita = 2;");
        return $query;
    }

    public function getCtPenalizadas($dt)
    {
        $query = $this->ch-> query("SELECT COUNT(*) AS `penalizada` 
		FROM ". $this->schema_cm .".citas 
		WHERE idPaciente = $dt AND estatusCita = 3;");
        return $query;
    }

	public function getCarrusel()
    {
		$query = $this->ch-> query("SELECT * FROM ". $this->schema_cm .".novedadesdashboard WHERE estatus = 1");
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

                $query = $this->ch->query("SELECT COUNT(DISTINCT ct.idPaciente) AS `pacientes` FROM ". $this->schema_cm .".usuarios us
                INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
                WHERE us2.idpuesto = $idData AND ct.estatusCita = 4 AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");


            }else if( $slEs != 0 ){

                $query = $this->ch-> query("SELECT COUNT(DISTINCT ct.idPaciente) AS `pacientes` FROM usuarios us
                INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
                INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
                WHERE us2.idpuesto = 158 AND ct.estatusCita = 4 AND ct.idEspecialista = $slEs AND 
				(ct.fechaModificacion >= '$fhI' AND ct.fechaModificacion <= '$fhF')");
            }

        }else if($idRol == 2){

            $query = $this->ch-> query("SELECT COUNT(*) AS `pacientes` FROM ". $this->schema_cm .".citas WHERE idPaciente = $idUser AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }else if($idRol == 3){

            $query = $this->ch-> query("SELECT COUNT(DISTINCT idPaciente) AS `pacientes` FROM ". $this->schema_cm .".citas WHERE idEspecialista = $idUser AND estatusCita = 4 AND 
            (fechaModificacion >= '$fhI' AND fechaModificacion <= '$fhF')");

        }
        
        return $query;
    }

	public function getCountModalidades($dt){
    $area = $dt["area"];
    $especialidad = $dt["espe"];
    $fhI = $dt["fhI"];
    $fechaFn = $dt["fhF"];
    $usuario = $dt["usuario"];

    $fecha = new DateTime($fechaFn);
    $fecha->modify('+1 day');
    $fhF = $fecha->format('Y-m-d');

    $especialidadCond = $especialidad != 0 ? "AND us2.idUsuario = $especialidad" : "";
    $usuarioCond = $usuario != 2 ? "AND us.externo = $usuario" : "";

		$query = $this->ch->query("SELECT 
		COUNT(CASE WHEN axs.tipoCita = 2 THEN ct.idPaciente END) AS `virtual`, 
		COUNT(CASE WHEN axs.tipoCita = 1 OR ct.idAtencionXSede = 0 THEN ct.idPaciente END) AS presencial
		FROM ". $this->schema_cm .".citas ct
		INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente 
		INNER JOIN ". $this->schema_cm .".usuarios us2 ON us2.idUsuario = ct.idEspecialista 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us2.idContrato
		LEFT JOIN ". $this->schema_cm .".atencionxsede axs ON axs.idAtencionXSede = ct.idAtencionXSede 
		LEFT JOIN ". $this->schema_cm .".opcionesporcatalogo op2 ON op2.idCatalogo = 5 AND op2.idOpcion = axs.tipoCita 
		WHERE us3.idpuesto = $area $usuarioCond AND ct.estatusCita IN(4)
		AND (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF') $especialidadCond");

    return $query;
	
	}

	public function getCountEstatusCitas($dt){

        $area = $dt["area"];
        $especialidad = $dt["espe"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];
		$usuario = $dt["usuario"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

		$especialidadCond = $especialidad != 0 ? "AND us2.idUsuario = $especialidad" : "";
    	$usuarioCond = $usuario != 2 ? "AND us.externo = $usuario" : "";

            $query = $this->ch->query("SELECT 
			COUNT(CASE WHEN ct.estatusCita = 1 THEN ct.idPaciente END) AS asistir, 
			COUNT(CASE WHEN ct.estatusCita = 2 OR ct.estatusCita = 7 THEN ct.idPaciente END) AS cancelada, 
			COUNT(CASE WHEN ct.estatusCita = 3 THEN ct.idPaciente END) AS penalizada, 
			COUNT(CASE WHEN ct.estatusCita = 4 THEN ct.idPaciente END) AS asistencia, 
			COUNT(CASE WHEN ct.estatusCita = 5 THEN ct.idPaciente END) AS justificada, 
			COUNT(CASE WHEN ct.estatusCita = 6 THEN ct.idPaciente END) AS pendiente, 
			COUNT(CASE WHEN ct.estatusCita = 10 THEN ct.idPaciente END) AS procesandoPago, 
			COUNT(ct.idCita) AS citas 
			FROM ". $this->schema_cm .".citas ct
			INNER JOIN ". $this->schema_cm .".usuarios us ON us.idUsuario = ct.idPaciente 
			INNER JOIN ". $this->schema_cm .".usuarios us2 ON us2.idUsuario = ct.idEspecialista 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us2.idContrato
			WHERE us3.idpuesto = $area AND ct.estatusCita IN (1, 2, 3, 4, 5, 6, 7, 10) $usuarioCond $especialidadCond
            AND (ct.fechaFinal >= '$fhI' AND ct.fechaFinal <= '$fhF')");
        
        return $query;

    }

	public function getCountPacientes($dt){

		$area = $dt["area"];
        $especialidad = $dt["espe"];
        $fhI = $dt["fhI"];
        $fechaFn = $dt["fhF"];

        $fecha = new DateTime($fechaFn);
        $fecha->modify('+1 day');
		$fhF = $fecha->format('Y-m-d');

		if($especialidad == 0){

            /* $query = $this->ch->query("SELECT
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
            AND YEAR(ct.fechaFinal) =  YEAR(CURDATE());"); */

			$query = $this->ch->query("SELECT 
			COUNT(DISTINCT usCO.idUsuario) AS colaborador, 
			COUNT(DISTINCT usEX.idUsuario) AS externo
			FROM ". $this->schema_cm .".citas ct
			LEFT JOIN ". $this->schema_cm .".usuarios usCO ON usCO.idUsuario = ct.idPaciente AND usCO.externo  = 0
			LEFT JOIN ". $this->schema_cm .".usuarios usEX ON usEX.idUsuario = ct.idPaciente AND usEX.externo  = 1
			INNER JOIN ". $this->schema_cm .".usuarios us2 ON us2.idUsuario = ct.idEspecialista 
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us3 ON us3.idcontrato = us2.idContrato
			WHERE ct.estatusCita = 4 AND us3.idpuesto = $area AND (ct.fechaFinal >= '$fhI' 
			AND ct.fechaFinal <= '$fhF')");

        }else{

            /* $query = $this->ch->query("SELECT
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
            AND us.idUsuario = $especialidad"); */

			$query = $this->ch->query("SELECT 
			COUNT(DISTINCT usCO.idUsuario) AS colaborador, 
			COUNT(DISTINCT usEX.idUsuario) AS externo
			FROM ". $this->schema_cm .".citas ct
			LEFT JOIN ". $this->schema_cm .".usuarios usCO ON usCO.idUsuario = ct.idPaciente AND usCO.externo  = 0
			LEFT JOIN ". $this->schema_cm .".usuarios usEX ON usEX.idUsuario = ct.idPaciente AND usEX.externo  = 1
			WHERE ct.estatusCita = 4 AND ct.idEspecialista = $especialidad AND (ct.fechaFinal >= '$fhI' 
			AND ct.fechaFinal <= '$fhF')");

        }
        
        return $query;

    }


	public function getDepartamentos(){
		$query = $this->ch-> query("SELECT dep.iddepto AS id, dep.ndepto AS departamento
		FROM ". $this->schema_cm .".datopuesto dt
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = dt.idPuesto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = ps.idarea 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dep ON dep.iddepto = ar.iddepto 
		WHERE dt.canRegister = 1
		GROUP BY dep.iddepto
		ORDER BY dep.ndepto ASC");
        return $query;
	}

	public function getAreas($dt){
		$query = $this->ch-> query("SELECT ar.idsubarea AS id,ar.narea AS area 
		FROM ". $this->schema_cm .".datopuesto dt
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = dt.idPuesto 
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_area ar ON ar.idsubarea = ps.idarea 
		WHERE dt.canRegister = 1 AND ar.iddepto = $dt
		GROUP BY ar.idsubarea
		ORDER BY ar.narea ASC");
        return $query;
	}

	public function getPuestos($dt){
		$query = $this->ch-> query("SELECT ps.idpuesto AS id, ps.nom_puesto AS puesto 
		FROM ". $this->schema_cm .".datopuesto dt
		INNER JOIN ". $this->schema_ch .".beneficioscm_vista_puestos ps ON ps.idpuesto = dt.idPuesto 
		WHERE dt.canRegister = 1 AND ps.idarea = $dt
		ORDER BY ps.nom_puesto ASC");
        return $query;
	}

	public function getDemandaBeneficio($dt){

		$beneficio = $dt["beneficio"];
		$depa = $dt["departamento"];
		$area = $dt["area"];
		$puesto = $dt["puestos"];

		if($depa == 0){

		$query = $this->ch->query("SELECT
			label,
			value
		FROM (
			SELECT
				us2.ndepto AS label,
				COUNT(CASE WHEN ct.estatusCita = 4 THEN 1 END) AS value
			FROM ". $this->schema_cm .".usuarios us
			LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
			LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dep ON dep.iddepto = us2.idpuesto   
			LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us.idUsuario 
			LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idEspecialista
			LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idcontrato = us3.idContrato
			WHERE us4.idpuesto = $beneficio
			GROUP BY us2.ndepto
		) AS subquery
		ORDER BY value DESC
		LIMIT 10;");

		}else if($depa != 0 && $area == 0){

			$query = $this->ch->query("SELECT
			label,
			value
			FROM (
				SELECT
					us2.ndepto AS label,
					COUNT(CASE WHEN ct.estatusCita = 4 AND us2.iddepto = $depa THEN 1 END) AS value
				FROM ". $this->schema_cm .".usuarios us
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_departamento dep ON dep.iddepto = us2.idpuesto 
				LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us.idUsuario 
				LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idcontrato = us3.idContrato
				WHERE us4.idpuesto = $beneficio AND us2.iddepto = $depa
				GROUP BY us2.ndepto
			) AS subquery");

		}else if($depa != 0 && $area != 0 && $puesto == 0){

			$query = $this->ch->query("SELECT
			label,
			value
			FROM (
				SELECT
					us2.narea AS label,
					COUNT(CASE WHEN ct.estatusCita = 4 AND us2.idarea = $area THEN 1 END) AS value
				FROM ". $this->schema_cm .".usuarios us
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
				LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us.idUsuario 
				LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idcontrato = us3.idContrato
				WHERE us4.idpuesto = $beneficio AND us2.idarea = $area
				GROUP BY us2.narea
			) AS subquery");
			
		}else if($depa != 0 && $area != 0 && $puesto != 0){

			$query = $this->ch->query("SELECT
			label,
			value
			FROM (
				SELECT
					us2.npuesto AS label,
					COUNT(CASE WHEN ct.estatusCita = 4 AND us2.idpuesto = $puesto THEN 1 END) AS value
				FROM ". $this->schema_cm .".usuarios us
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
				LEFT JOIN ". $this->schema_cm .".citas ct ON ct.idPaciente = us.idUsuario 
				LEFT JOIN ". $this->schema_cm .".usuarios us3 ON us3.idUsuario = ct.idEspecialista
				LEFT JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios us4 ON us4.idcontrato = us3.idContrato
				WHERE us4.idpuesto = $beneficio AND us2.idpuesto = $puesto
				GROUP BY us2.npuesto
			) AS subquery");
		}

		return $query;

	}

}
