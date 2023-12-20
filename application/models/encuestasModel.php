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
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 1");
		return $query->result();
    }

    public function getResp2()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 2");
		return $query->result();
    }

    public function getResp3()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 3");
		return $query->result();
    }

    public function getResp4()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
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
        WHERE ct.estatus = 4 AND us.idUsuario = $dt AND ct.fechaFinal BETWEEN '2023-11-01' AND '2023-12-05'");

        $idEspecialistas = [];
        foreach ($query_especialistas->result() as $row) {
            $idEspecialistas[] = $row->idEspecialista;
        }

        $query_encuestas = $this->db->query("SELECT DISTINCT idEncuesta, ps.puesto
            FROM usuarios us 
            INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
            INNER JOIN puestos ps ON ps.idPuesto = ec.idArea
            WHERE us.idUsuario IN (" . implode(',', $idEspecialistas) . ") AND ec.estatus = 1");

        $idEcuestas = [];
        foreach ($query_encuestas->result() as $row) {
            $idEcuestas[] = $row->idEncuesta;
        }

        $query_encuestasC = $this->db->query("SELECT * FROM encuestasContestadas WHERE idEncuesta IN (" . implode(',', $idEcuestas) . ") AND idUsuario = 1");

        $idEnc = [0];
        foreach ($query_encuestasC->result() as $row) {
            $idEnc[] = $row->idEncuesta;
        }

        $query_enc = $this->db->query("SELECT DISTINCT idEncuesta, ps.puesto
        FROM usuarios us 
        INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
        INNER JOIN puestos ps ON ps.idPuesto = ec.idArea
        WHERE us.idUsuario IN (" . implode(',', $idEspecialistas) . ") AND ec.estatus = 1 AND idEncuesta NOT IN (" . implode(',', $idEnc) . ")");

        $result_encuestas = $query_enc->result_array();

        return $result_encuestas;
    }

    public function getEcuestaValidacion($dt){

        $dataArray = $dt;

        $jsonString = json_encode($dataArray);

        $idEncuesta = $dataArray[0];
        $idUsuario = $dataArray[1];

        $query_v1 = $this->db->query("SELECT DISTINCT ct.idEspecialista
        FROM usuarios us
        INNER JOIN citas ct ON ct.idPaciente = us.idUsuario
        WHERE ct.estatus = 4 AND us.idUsuario = $idUsuario AND ct.fechaFinal BETWEEN '2023-11-01' AND '2023-12-05'");

        if ($query_v1->num_rows() > 0) {

            $query_v2 = $this->db->query("SELECT * FROM encuestasContestadas WHERE idEncuesta = $idEncuesta AND idUsuario = $idUsuario");

            if ($query_v2->num_rows() > 0) {
                return false;
            } else {
                return true;
            }
        } else {

            return false;
        }

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

    public function encuestaInsert($dt){
        $items = json_decode($dt);

		$datosValidos = true;

		if (isset($items)) {

			foreach ($items as $item) {
				if (!isset($item->pregunta, $item->resp) || empty($item->pregunta) 
				|| is_null($item->resp) || empty($item->resp) || is_null($item->pregunta)) {
					echo json_encode(array("estatus" => -5, "mensaje" => "Hay preguntas sin contestar!" ));
					$datosValidos = false;
					break; 
				}
			}
			$idPregunta = 0; 

			if ($datosValidos) {

				foreach ($items as $item) {

					$idPregunta++;

					$pregunta = $item->pregunta;
					$resp = $item->resp;
					$idUsuario = $item->idUsuario;
					$idEncuesta = $item->idEncuesta;
					$idArea = $item->idArea;

					$abierta = is_numeric($resp) ? 1 : 0;

					$query = $this->db->query("INSERT INTO encuestasContestadas (idPregunta, idRespuesta, idEspecialista, idArea, idEncuesta, fechaCreacion, idUsuario ) 
					VALUES (?, ?, 1, ?, ?, GETDATE(), ?)", 
					array($idPregunta, $resp, $idArea, $idEncuesta, $idUsuario ));
					
					$queryPreguntasGeneradas = $this->db->query("INSERT INTO preguntasGeneradas (idPregunta, pregunta, estatus, abierta, especialidad, idEncuesta) 
					VALUES (?, ?, 1, ?, ?, ?)", 
					array($idPregunta, $pregunta, $abierta, $idArea, $idEncuesta ));
				
				}
				
				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => 200, "mensaje" => "Encuesta enviada exitosamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => -5, "mensaje" => "Error Faltan Datos" ));
		}
    }

    public function encuestaCreate($dt){
        $dataArray = json_decode($dt);

		$datosValidos = true;

		if (isset($dataArray->area) && isset($dataArray->items)) {
			$area = $dataArray->area;
			$items = $dataArray->items;

			if (empty($area)) {
				echo json_encode(array("estatus" => -5, "mensaje" => "Error Hay Campos Vacios" ));
				$datosValidos = false;
			}

			foreach ($items as $item) {

				if (!isset($item->pregunta, $item->respuesta) || empty($item->pregunta) 
				|| is_null($item->respuesta) || empty($item->respuesta) || is_null($item->pregunta)) {
					echo json_encode(array("estatus" => -5, "mensaje" => "Error Hay campos vacios!" ));
					$datosValidos = false;
					break; 
				}
			}

			if ($datosValidos) {

				foreach ($items as $item) {
					$pregunta = $item->pregunta;
					$respuesta = $item->respuesta;
					$idEncuesta = $item->idEncuesta;

					$query = $this->db->query("INSERT INTO encuestasCreadas (pregunta, respuestas, idArea, estatus, fechaCreacion, idEncuesta) 
					VALUES (?, ?, ?, 1, GETDATE(), ?)", 
					array($pregunta, $respuesta, $area, $idEncuesta));
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => 200, "mensaje" => "Encuesta Creada Correctamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => -5, "mensaje" => "Error Faltan Datos" ));
		}
    }
}