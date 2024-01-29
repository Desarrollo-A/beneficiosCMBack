<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class dashModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function citasCountStatus()
	{
		$query = $this->db-> query("SELECT op.nombre as estatus, COUNT(op.nombre) AS total
		FROM catalogos ca
		INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
		INNER JOIN citas ct ON ct.estatusCita = op.idOpcion 
		GROUP BY op.nombre
		HAVING COUNT(op.nombre)>0");
		return $query->result();
	}

	public function totalStatusCitas()
	{
		$query = $this->db-> query("SELECT COUNT(*) total
		FROM opcionesPorCatalogo 
		WHERE idCatalogo = 2");
		return $query->result();
	}

	public function estatusFechaAsistencia($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, ct.fechaModificacion) AS mes,
		COUNT(*) AS cantidad, op.nombre AS nombre
		FROM catalogos ca
		INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
		INNER JOIN citas ct ON ct.estatusCita = op.idOpcion 
		WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatusCita = 1
		GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
		ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function estatusFechaCancelada($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, ct.fechaModificacion) AS mes,
		COUNT(*) AS cantidad, op.nombre AS nombre
		FROM catalogos ca
		INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
		INNER JOIN citas ct ON ct.estatusCita = op.idOpcion 
		WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatusCita = 2
		GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
		ORDER BY Mes", $year);

		return $query->result();
	}

	public function estatusFechaPenalizada($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, ct.fechaModificacion) AS mes,
		COUNT(*) AS cantidad, op.nombre AS nombre
		FROM catalogos ca
		INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
		INNER JOIN citas ct ON ct.estatusCita = op.idOpcion 
		WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatusCita = 3
		GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
		ORDER BY Mes", $year);

		return $query->result();
	}

	public function fechaMinima()
	{
		$query =$this->db->query("SELECT DATEPART(YEAR, MIN(fechaModificacion)) AS year FROM citas");

		return $query->result();
	}

	public function citasAnual($year)
	{
		$query =$this->db->query("SELECT
		DATEPART(MONTH, fechaModificacion) AS mes,
		COUNT(*) AS cantidad, op.nombre AS nombre
		FROM catalogos ca
		INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
		INNER JOIN citas ct ON ct.estatusCita = op.idOpcion
		WHERE
		DATEPART(YEAR, fechaModificacion) = ?
		GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
		ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function getPregunta($dt){

		$query = $this->db-> query("SELECT DISTINCT ec.idPregunta, pg.pregunta, ec.respuestas, pg.idPregunta, ec.idEncuesta, ec.idEncuestaCreada, ec.idArea  
		FROM encuestasCreadas ec
		INNER JOIN preguntasGeneradas pg ON pg.idPregunta = ec.idPregunta
		WHERE ec.estatus = 1 AND abierta = 1 AND ec.idArea = $dt AND pg.idArea = $dt AND ec.respuestas <= 4");
		
		$result = $query->result();

		if(!empty($result))
			return $result;
		else
		{
			return false;
		}

	}

	public function getRespuestas($dt){

		if(!empty ($dt)){

			$idPregunta = $dt[0]["idPregunta"];
			$idEncuesta = $dt[1]["idEncuesta"];
			$idEspecialidad = $dt[2]["idArea"];

			$query_pregunta = $this->db->query("SELECT DISTINCT pg.pregunta  
			FROM encuestasCreadas ec
			INNER JOIN preguntasGeneradas pg ON pg.idPregunta = ec.idPregunta
			WHERE ec.estatus = 1 AND abierta = 1 AND pg.idArea = ? AND pg.idPregunta = ? AND ec.idPregunta = ?",
				array($idEspecialidad, $idPregunta, $idPregunta));

			if ($query_pregunta->num_rows() > 0) {

			$idPrg = [];
			foreach ($query_pregunta->result() as $row) {
				$idPrg[] = "'" . $row->pregunta . "'";
			}

			$idPreguntasString = implode(",", $idPrg);

			$query = $this->db->query("SELECT STRING_AGG(rg.respuesta, ', ') AS respuestas, rg.grupo  
			FROM encuestasCreadas ec
			INNER JOIN respuestasGenerales rg ON rg.grupo = ec.respuestas 
			INNER JOIN preguntasGeneradas pg ON pg.idPregunta = ec.idPregunta
			WHERE pg.pregunta IN ($idPreguntasString) AND ec.idEncuesta = 1 AND pg.idEncuesta = 1
			GROUP BY grupo",
				array($idEncuesta));

			return $query->result();

			}else
			{
				return false;
			}

		}else
		{
			return false;
		}
	}

	public function getCountRespuestas($dt){

		if(!empty ($dt)){

		$idPregunta = $dt[0]["idPregunta"];
        $idEncuesta = $dt[1]["idEncuesta"];
		
		$query = $this->db-> query("SELECT rg.respuesta, COUNT(*) AS cantidad, (COUNT(*) * 100.0 / SUM(COUNT(*)) OVER ()) AS porcentaje, 
			ec.idPregunta, ec.idArea, ec.idEncuesta
			FROM encuestasContestadas ec
			INNER JOIN respuestasGenerales rg ON rg.idRespuestaGeneral = ec.idRespuesta
			WHERE ec.idEncuesta = $idEncuesta AND ec.idPregunta = $idPregunta
			GROUP BY rg.respuesta, ec.idPregunta, ec.idArea, ec.idEncuesta");

		if ($query->num_rows() > 0) {

			return $query->result();
		}else{
			return false;
		}

		
			}else{
				return false;
			}
	}

	public function getMetas($dt)
    {
        $idData = $dt["idData"];
        $idRol = $dt["idRol"];
        $inicio = $dt["inicio"];
        $fin = $dt["fin"];

        if($idRol == 1){
            $query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idPuesto = $idData AND ct.estatusCita = 4 AND fechaFinal BETWEEN '$inicio' AND '$fin'");
        }else{
            $query = $this->db-> query("SELECT COUNT(*) AS [citas] FROM usuarios us
            INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
            WHERE us.idUsuario = $idData AND ct.estatusCita = 4 AND fechaFinal BETWEEN '$inicio' AND '$fin'");
        }

        return $query;
    }
}