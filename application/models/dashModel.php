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
}