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
		return $query;
    }

    public function encuestaMinima()
    {
        $query = $this->db-> query("SELECT COALESCE(MAX(idEncuesta), 0) AS minIdEncuesta FROM encuestasCreadas;");
		return $query;
    }

    public function getEncuesta($dt)
    {
        $query = $this->db-> query("SELECT * FROM encuestasCreadas WHERE idEncuesta =$dt");
		return $query;
    }

    public function getResp1()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 1");
		return $query;
    }

    public function getResp2()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 2");
		return $query;
    }

    public function getResp3()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 3");
		return $query;
    }

    public function getResp4()
    {
        $query = $this->db-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM catalogos ca 
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN respuestasGenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 4");
		return $query;
    }

    public function getEncNotificacion($dt)
    {

        $idUsuario = $dt["idUsuario"];
        $vigenciaInicio = $dt["vigenciaInicio"];
        $vigenciaFin = $dt["vigenciaFin"];
        $trimestreInicio = $dt["trimDefault"];
        $fechaActual = $dt["fechActual"];

        $query_citas = $this->db->query("SELECT DISTINCT ct.idCita
        FROM usuarios us
        INNER JOIN citas ct ON ct.idPaciente = us.idUsuario
        WHERE ct.estatusCita = 4 AND us.idUsuario = $idUsuario AND ct.fechaFinal BETWEEN '$vigenciaInicio' AND '$vigenciaFin'");

        if ($query_citas->num_rows() > 0) {
            
        $idCitas = [];
        foreach ($query_citas->result() as $row) {
            $idCitas[] = $row->idCita;
        }

        $query_especialistas = $this->db->query("WITH cte AS (
            SELECT us.puesto, ct.idEspecialista,fechaFinal, ROW_NUMBER() OVER (PARTITION BY us.puesto ORDER BY fechaFinal DESC) AS rn
            FROM citas ct
            INNER JOIN usuarios us ON us.idUsuario = ct.idEspecialista
            WHERE idCita IN (" . implode(',', $idCitas) . "))
            SELECT idEspecialista
            FROM cte WHERE rn = 1 ");

        $idEspecialistas = [];
        foreach ($query_especialistas->result() as $row) {
            $idEspecialistas[] = $row->idEspecialista;
        }

        $query_encuestas = $this->db->query("SELECT DISTINCT idEncuesta, ps.puesto
            FROM usuarios us 
            INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
            INNER JOIN puestos ps ON ps.idPuesto = ec.idArea
            WHERE us.idUsuario IN (" . implode(',', $idEspecialistas) . ") AND ec.estatus = 1");

        if ($query_encuestas->num_rows() > 0) {

        $idEcuestas = [];
        foreach ($query_encuestas->result() as $row) {
            $idEcuestas[] = $row->idEncuesta;
        }

        $query_encuestasC = $this->db->query("SELECT * FROM encuestasContestadas WHERE idEncuesta IN (" . implode(',', $idEcuestas) . ") AND idUsuario = $idUsuario");

        $idEnc = [0];
        foreach ($query_encuestasC->result() as $row) {
            $idEnc[] = $row->idEncuesta;
        }

        $query_enc = $this->db->query("SELECT DISTINCT diasVigencia FROM encuestasCreadas WHERE idEncuesta IN (" . implode(',', $idEnc) . ")");

        $vig = 0;
        foreach ($query_enc->result() as $row) {
            $vig = $row->diasVigencia;
        }

        if($vig == null){
            $vig = 0;
        }

        $date = date($trimestreInicio);
        $mod_date = strtotime($date."+ $vig days");
        $trimestreFin = date("Y-m-d",$mod_date) . "\n";

        if($fechaActual >= $trimestreInicio && $fechaActual <= $trimestreFin){

        $query_enc = $this->db->query("SELECT DISTINCT idEncuesta, ps.puesto
        FROM usuarios us 
        INNER JOIN encuestasCreadas ec ON ec.idArea = us.puesto
        INNER JOIN puestos ps ON ps.idPuesto = ec.idArea
        WHERE us.idUsuario IN (" . implode(',', $idEspecialistas) . ") AND ec.estatus = 1 AND idEncuesta NOT IN (" . implode(',', $idEnc) . ")");

        $result_encuestas = $query_enc->result_array();

        return $result_encuestas;

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

    public function getEcuestaValidacion($dt){

        $dataArray = $dt;

        $jsonString = json_encode($dataArray);

        $idEncuesta = $dataArray[0];
        $idUsuario = $dataArray[1];

        $query_v2 = $this->db->query("SELECT * FROM encuestasContestadas WHERE idEncuesta = $idEncuesta AND idUsuario = $idUsuario");

        $query_v3 = $this->db->query("SELECT * FROM encuestasCreadas WHERE idEncuesta = $idEncuesta AND estatus = 0");

        if ($query_v2->num_rows() > 0 || $query_v3->num_rows() > 0) {
            return false;
        }else{
            return true;
        }

    }

    public function getPuestos(){
        $query = $this->db->query("SELECT * FROM puestos WHERE idPuesto = 537 OR idPuesto = 686 OR idPuesto = 158 OR idPuesto = 585");

        return $query;
    }

    public function encuestaInsert($dt){
        $items = json_decode($dt);

		$datosValidos = true;

		if (isset($items)) {

			foreach ($items as $item) {
				if (!isset($item->pregunta, $item->resp) || empty($item->pregunta) 
				|| is_null($item->resp) || empty($item->resp) || is_null($item->pregunta)) {
					echo json_encode(array("estatus" => false, "msj" => "Hay preguntas sin contestar!" ));
					$datosValidos = false;
					break; 
				}
			}
			$idPregunta = 1; 

			if ($datosValidos) {

				foreach ($items as $item) {

					$idPregunta++;

					$pregunta = $item->pregunta;
					$resp = $item->resp;
					$idUsuario = $item->idUsuario;
					$idEncuesta = $item->idEncuesta;
					$idArea = $item->idArea;

					$abierta = is_numeric($resp) ? 1 : 0;

                    $query_idEspecialista = $this->db->query("SELECT TOP 1 ct.idEspecialista, MAX(ct.fechaFinal) AS fechaMasReciente
                    FROM puestos ps
                    INNER JOIN usuarios us ON us.puesto = ps.idPuesto
                    INNER JOIN citas ct ON ct.idEspecialista = us.idUsuario
                    WHERE ps.idPuesto = $idArea AND ct.idPaciente = $idUsuario
                    GROUP BY ct.idEspecialista
                    ORDER BY fechaMasReciente DESC");

                    $idEspecialista = [];
                    foreach ($query_idEspecialista->result() as $row) {
                        $idEspecialista[] = $row->idEspecialista;
                    }

					$query = $this->db->query("INSERT INTO encuestasContestadas (idPregunta, idRespuesta, idEspecialista, idArea, idEncuesta, fechaCreacion, idUsuario ) 
					VALUES (?, ?, ?, ?, ?, GETDATE(), ?)", 
					array($idPregunta, $resp, $idEspecialista, $idArea, $idEncuesta, $idUsuario ));
					
					$queryPreguntasGeneradas = $this->db->query("INSERT INTO preguntasGeneradas (idPregunta, pregunta, estatus, abierta, especialidad, idEncuesta) 
					VALUES (?, ?, 1, ?, ?, ?)", 
					array($idPregunta, $pregunta, $abierta, $idArea, $idEncuesta ));
				
				}
				
				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => true, "msj" => "Encuesta enviada exitosamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function encuestaCreate($dt){
        $dataArray = json_decode($dt);

		$datosValidos = true;

		if (isset($dataArray->area) && isset($dataArray->items)) {
            $estatus = $dataArray->estatus;
			$area = $dataArray->area;
			$items = $dataArray->items;

			if (empty($area)) {
				echo json_encode(array("estatus" => false, "msj" => "Error Hay Campos Vacios" ));
				$datosValidos = false;
			}

			foreach ($items as $item) {

				if (!isset($item->pregunta, $item->respuesta) || empty($item->pregunta) 
				|| is_null($item->respuesta) || empty($item->respuesta) || is_null($item->pregunta)) {
					echo json_encode(array("estatus" => false, "msj" => "Error Hay campos vacios!" ));
					$datosValidos = false;
					break; 
				}
			}

			if ($datosValidos) {

                if($estatus == 1){

                    $query_idEncuesta = $this->db->query("SELECT * FROM encuestasCreadas WHERE idArea = $area AND estatus = 1");

                    $idEnc = 0;
                    foreach ($query_idEncuesta->result() as $row) {
                        $idEnc = $row->idEncuesta;
                    }

                    $data_1 = array(
                        "estatus" => 0,
                    );

                    $response_1=$this->generalModel->updateRecord('encuestasCreadas', $data_1, 'idEncuesta', $idEnc);

                }
                
				foreach ($items as $item) {
					$pregunta = $item->pregunta;
					$respuesta = $item->respuesta;
					$idEncuesta = $item->idEncuesta;

					$query = $this->db->query("INSERT INTO encuestasCreadas (pregunta, respuestas, idArea, estatus, fechaCreacion, idEncuesta) 
					VALUES (?, ?, ?, ?, GETDATE(), ?)", 
					array($pregunta, $respuesta, $area, $estatus, $idEncuesta));
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo "Error al realizar la transacción";
				} else {
					echo json_encode(array("estatus" => true, "msj" => "Encuesta Creada Correctamente" ));
				}
			}
		} else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function getEncuestasCreadas($dt){

        $query = $this->db->query("WITH cte AS (
            SELECT idEncuesta, fechaCreacion, estatus, ROW_NUMBER() OVER (PARTITION BY idEncuesta ORDER BY fechaCreacion DESC) AS rn, diasVigencia
            FROM encuestasCreadas
            WHERE idArea = $dt)
            SELECT idEncuesta, fechaCreacion, estatus, diasVigencia
            FROM cte WHERE rn = 1");

        return $query;
    }

    public function getEstatusUno($dt){

        $query = $this->db->query("WITH cte AS (SELECT estatus, ROW_NUMBER() OVER (PARTITION BY idEncuesta ORDER BY fechaCreacion DESC) AS rn
        FROM encuestasCreadas
        WHERE idArea = $dt)
        SELECT
        COUNT(estatus) AS cantidadEstatus
        FROM
        cte
        WHERE
        rn = 1 AND estatus = 1");

        return $query;
    }
}