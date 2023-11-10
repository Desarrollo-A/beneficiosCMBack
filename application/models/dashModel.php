<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class dashModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function citas_count_status()
	{
		$query = $this->db-> query("SELECT o.nombre as estatus, COUNT(o.nombre) AS total
        FROM citas ct
        INNER JOIN 
        opcionesPorCatalogo o ON o.idOpcion = ct.estatus
        GROUP BY o.nombre
        HAVING COUNT(o.nombre)>0");
		return $query->result();
	}

	public function total_status_citas()
	{
		$query = $this->db-> query("SELECT COUNT(*) total
		FROM opcionesPorCatalogo 
		WHERE idOpcion >= 5 AND idOpcion <= 7");
		return $query->result();
	}

	public function estatus_fecha_asistencia($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, fechaModificacion) AS mes,
		COUNT(*) AS cantidad, o.nombre AS nombre
	FROM
		citas ct
	INNER JOIN 
		opcionesPorCatalogo o ON o.idOpcion = ct.estatus
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 5
	GROUP BY
		DATEPART(MONTH, fechaModificacion), o.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function estatus_fecha_cancelada($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, fechaModificacion) AS mes,
		COUNT(*) AS cantidad, o.nombre AS nombre
	FROM
		citas ct
	INNER JOIN 
		opcionesPorCatalogo o ON o.idOpcion = ct.estatus
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 6
	GROUP BY
		DATEPART(MONTH, fechaModificacion), o.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function estatus_fecha_penalizada($year)
	{
		$query = $this->db-> query("SELECT
		DATEPART(MONTH, fechaModificacion) AS mes,
		COUNT(*) AS cantidad, o.nombre AS nombre
	FROM
		citas ct
	INNER JOIN 
		opcionesPorCatalogo o ON o.idOpcion = ct.estatus
	WHERE
		DATEPART(YEAR, fechaModificacion) = ? AND ct.estatus = 7
	GROUP BY
		DATEPART(MONTH, fechaModificacion), o.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}

	public function fecha_minima()
	{
		$query =$this->db->query("SELECT DATEPART(YEAR, MIN(fechaModificacion)) AS year FROM citas");

		return $query->result();
	}

	public function citas_anual($year)
	{
		$query =$this->db->query("SELECT
		DATEPART(MONTH, fechaModificacion) AS mes,
		COUNT(*) AS cantidad, o.nombre AS nombre
	FROM
		citas ct
	INNER JOIN 
		opcionesPorCatalogo o ON o.idOpcion = ct.estatus
	WHERE
		DATEPART(YEAR, fechaModificacion) = ?
	GROUP BY
		DATEPART(MONTH, fechaModificacion), o.nombre
	ORDER BY
		Mes", $year);

		return $query->result();
	}
}