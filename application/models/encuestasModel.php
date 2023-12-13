<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class encuestasModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

    public function insertBatch($table, $data)
    {
        $response = array();  // Initialize $response as an array

        try {
            $this->db->trans_begin();
            $this->db->insert($table, $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Error en la transacción de la base de datos.");
            } else {
                $this->db->trans_commit();
                $response['result'] = true;
                $response['msg'] = "¡Registro insertado exitosamente!";
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['result'] = false;
            $response['msg'] = "Error en la inserción de datos: " . $e->getMessage();
        }

        return $response;
    }

    public function getRespuestas()
    {
        $query = $this->db-> query("SELECT idOpcion, nombre FROM opcionesPorCatalogo WHERE idCatalogo = 4");
		return $query->result();
    }

    public function encuestaMinima()
    {
        $query = $this->db-> query("SELECT COALESCE(MAX(idEncuesta), 0) AS minIdEncuesta FROM encuestasCreadas;");
		return $query->result();
    }

    public function getEncuesta($dt)
    {
        $query = $this->db-> query("SELECT * FROM encuestasCreadas WHERE idEncuesta =$dt");
		return $query->result();
    }

    public function getResp1()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 1");
		return $query->result();
    }

    public function getResp2()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 2");
		return $query->result();
    }

    public function getResp3()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 3");
		return $query->result();
    }

    public function getResp4()
    {
        $query = $this->db-> query("SELECT  rp.respuesta AS value, rp.respuesta AS label FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 4");
		return $query->result();
    }

    public function getEncNotificacion($dt)
    {
        $query_especialistas = $this->db->query("SELECT DISTINCT ct.idEspecialista
            FROM usuarios us
            INNER JOIN citas ct ON ct.idPaciente = us.idUsuario
            WHERE ct.estatus = 4 AND us.idUsuario = 28");

        $resultado = $query_especialistas->result();

        $resultados_encuestas = array();

        foreach ($resultado as $especialista) {
            $idEspecialista = $especialista->idEspecialista;

            $query = $this->db->query("SELECT us.puesto, ps.puesto, idEncuesta, ec.pregunta, ec.respuestas FROM usuarios us 
                INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
                INNER JOIN puestos ps ON ps.idPuesto = ec.idArea
                WHERE us.idUsuario = $idEspecialista");

            $resultados_encuestas[] = $query->result();
        }

        return $resultados_encuestas;

    }

    public function getPuestos(){
        $query = $this->db->query("SELECT * FROM puestos WHERE idPuesto = 537 OR idPuesto = 686 OR idPuesto = 158 OR idPuesto = 585");

        return $query->result();
    }

    public function encuestaContestada(){
        $query = $this->db->query("SELECT 
        CASE
            WHEN EXISTS (SELECT 1 FROM encuestasContestadas WHERE idEncuesta = 1 AND idUsuario = 1) THEN 1
            ELSE 0
        END AS Resultado;
    ");

        return $query->result();
    }
}