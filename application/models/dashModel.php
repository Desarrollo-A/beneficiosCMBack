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
		INNER JOIN citas ct ON ct.estatus = op.idOpcion 
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
	FROM
		catalogos ca
	INNER JOIN 
		opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
	INNER JOIN 
		citas ct ON ct.estatus = op.idOpcion 
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 1
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
	FROM
		catalogos ca
	INNER JOIN 
		opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
	INNER JOIN 
		citas ct ON ct.estatus = op.idOpcion 
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 2
	GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function estatusFechaPenalizada($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, ct.fechaModificacion) AS mes,
		COUNT(*) AS cantidad, op.nombre AS nombre
	FROM
		catalogos ca
	INNER JOIN 
		opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
	INNER JOIN 
		citas ct ON ct.estatus = op.idOpcion 
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 3
	GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
	ORDER BY
		Mes", $year);

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
	FROM
		catalogos ca
	INNER JOIN 
		opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 2
	INNER JOIN 
		citas ct ON ct.estatus = op.idOpcion
	WHERE
		DATEPART(YEAR, fechaModificacion) = ?
	GROUP BY
		DATEPART(MONTH, fechaModificacion), op.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function getPregunta($dt){

		$query = $this->db-> query("SELECT DISTINCT pg.pregunta, ec.respuestas, pg.idPregunta, ec.idEncuesta  
		FROM encuestasCreadas ec
		INNER JOIN preguntasGeneradas pg ON pg.pregunta = ec.pregunta
		WHERE ec.estatus = 1 AND abierta = 1 AND especialidad = $dt");
		
		$result = $query->result(); # added

		if(!empty($result))
			return $result;
		else
		{
			return false;
		}

	}

	public function getRespuestas($dt){

		if(!empty ($dt)){
			$respuestas = $dt[1]["respuestas"];

			$query = $this->db-> query("SELECT STRING_AGG(respuesta, ', ') AS respuestas 
			FROM respuestasGenerales 
			WHERE grupo = $respuestas
			GROUP BY grupo;");
			return $query->result();
		}else
		{
			return false;
		}
	}

	public function getCountRespuestas($dt){

		if(!empty ($dt)){

        $idEncuesta = $dt[2]["idEncuesta"];
        $idPregunta = $dt[0]["idPregunta"];
		
		$query = $this->db-> query("SELECT rg.respuesta, COUNT(*) AS cantidad, (COUNT(*) * 100.0 / SUM(COUNT(*)) OVER ()) AS porcentaje
		FROM encuestasContestadas ec
		INNER JOIN respuestasGenerales rg ON rg.idRespuestaGeneral = ec.idRespuesta
		WHERE ec.idEncuesta = $idEncuesta AND ec.idPregunta = $idPregunta
		GROUP BY rg.respuesta");

		return $query->result();
		}else
		{
			return false;
		}
	}
}