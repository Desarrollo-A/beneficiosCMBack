<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class EncuestasModel extends CI_Model {
	public function __construct()
	{
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
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
        $query = $this->ch-> query("SELECT idOpcion, nombre FROM ". $this->schema_cm .".opcionesporcatalogo WHERE idCatalogo = 4");
		return $query;
    }

    public function encuestaMinima()
    {
        $query = $this->ch->query("SELECT COALESCE(MAX(idEncuesta), 0) AS minIdEncuesta FROM ". $this->schema_cm .".encuestascreadas;");
        return $query;
    }

    public function getEncuesta($dt)
    {
        $query = $this->ch->query("SELECT DISTINCT ec.idPregunta, pg.pregunta, ec.respuestas, ec.idArea FROM ". $this->schema_cm .".encuestascreadas ec
        INNER JOIN ". $this->schema_cm .".preguntasgeneradas pg ON pg.idPregunta = ec.idPregunta
        WHERE ec.idEncuesta = $dt AND pg.idEncuesta = $dt");
		return $query;
    }

    public function getResp1()
    {
        $query = $this->ch-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM ". $this->schema_cm .".catalogos ca 
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN ". $this->schema_cm .".respuestasgenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 1"); 
		return $query;
    }

    public function getResp2()
    {
        $query = $this->ch-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM ". $this->schema_cm .".catalogos ca 
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN ". $this->schema_cm .".respuestasgenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 2");
		return $query;
    }

    public function getResp3()
    {
        $query = $this->ch-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM ". $this->schema_cm .".catalogos ca 
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN ". $this->schema_cm .".respuestasgenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 3");
		return $query;
    }

    public function getResp4()
    {
        $query = $this->ch-> query("SELECT  rp.idRespuestaGeneral AS value, rp.respuesta AS label, rp.tipo  FROM ". $this->schema_cm .".catalogos ca 
        INNER JOIN ". $this->schema_cm .".opcionesporcatalogo op ON op.idCatalogo = ca.idCatalogo AND ca.idCatalogo = 4
        INNER JOIN ". $this->schema_cm .".respuestasgenerales rp ON rp.grupo = op.idOpcion
        WHERE idOpcion = 4");
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
			$idPregunta = 0; 

			if ($datosValidos) {

				foreach ($items as $item) {

					$idPregunta++;

					$pregunta = $item->pregunta;
					$resp = $item->resp;
					$idUsuario = $item->idUsuario;
					$idEncuesta = $item->idEnc;
					$idArea = $item->idArea;
                    $idEsp = $item->idEsp;

					$abierta = is_numeric($resp) ? 1 : 0;

                    /* $query_idEspecialista = $this->ch->query("SELECT ct.idEspecialista, MAX(ct.fechaFinal) AS fechaMasReciente
					FROM ". $this->schema_ch .".beneficioscm_vista_usuarios us2
					INNER JOIN ". $this->schema_cm .".usuarios us ON us.idContrato = us2.idcontrato
					INNER JOIN ". $this->schema_cm .".citas ct ON ct.idEspecialista = us.idUsuario
					WHERE us2.idcontrato = $idArea AND ct.idPaciente = $idUsuario
					GROUP BY ct.idEspecialista
					ORDER BY fechaMasReciente DESC
					LIMIT 1;");

                    $idEspecialista = [];
                    foreach ($query_idEspecialista->result() as $row) {
                        $idEspecialista[] = $row->idEspecialista;
                    } */
                    
                    $this->ch->query("INSERT INTO ". $this->schema_cm .".encuestascontestadas (idPregunta, idRespuesta, idEspecialista, idArea, idEncuesta, fechaCreacion, idUsuario, creadoPor, modificadoPor ) 
					VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)", 
					array($idPregunta, $resp, $idEsp, $idArea, $idEncuesta, $idUsuario, $idUsuario, $idUsuario ));
				
				}
				
				$this->ch->trans_complete();

				if ($this->ch->trans_status() === FALSE) {
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

                    $query_idEncuesta = $this->ch->query("SELECT * FROM ". $this->schema_cm .".encuestascreadas WHERE idArea = $area AND estatus = 1");

                    $idEnc = 0;
                    foreach ($query_idEncuesta->result() as $row) {
                        $idEnc = $row->idEncuesta;
                    }

                    $data_1 = array(
                        "estatus" => 0,
                    );

                    $this->GeneralModel->updateRecord('". $this->schema_cm .".encuestascreadas', $data_1, 'idEncuesta', $idEnc);

                }

                $idPregunta = 0; 
                
				foreach ($items as $item) {

                    $idPregunta++;

					$pregunta = $item->pregunta;
					$respuesta = $item->respuesta;
					$idEncuesta = $item->idEncuesta;

                    $abierta = 0;

                    if($respuesta < 5){$abierta = 1;}

                    $this->ch->query("INSERT INTO ". $this->schema_cm .".preguntasgeneradas (idPregunta, pregunta, estatus, abierta, idArea, idEncuesta) 
					VALUES (?, ?, 1, ?, ?, ?)", 
					array($idPregunta, $pregunta, $abierta, $area, $idEncuesta ));

                    $this->ch->query("INSERT INTO ". $this->schema_cm .".encuestascreadas (idPregunta, respuestas, idArea, estatus, fechaCreacion, idEncuesta) 
                    VALUES (?, ?, ?, ?, NOW(), ?)", 
                    array($idPregunta, $respuesta, $area, $estatus, $idEncuesta));

				}

				$this->ch->trans_complete();

				if ($this->ch->trans_status() === FALSE) {
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

        $query = $this->ch->query("SELECT idEncuesta, fechaCreacion, estatus
        FROM (
        SELECT idEncuesta, fechaCreacion, estatus,
            CASE WHEN @prev_idEncuesta = idEncuesta THEN @row_number := @row_number + 1
                ELSE @row_number := 1 AND @prev_idEncuesta := idEncuesta END AS rn
                FROM (SELECT @row_number := 0, @prev_idEncuesta := NULL) AS vars,
                     ". $this->schema_cm .".encuestascreadas
                WHERE idArea = $dt
                ORDER BY idEncuesta, fechaCreacion DESC
            ) AS subquery
        WHERE rn = 1;");   

        return $query;
    }

    public function getEstatusUno($dt){

        $query = $this->ch->query("SELECT COUNT(estatus) AS cantidadEstatus
        FROM (
            SELECT estatus, 
                   CASE WHEN @prev_idEncuesta = idEncuesta THEN @row_number := @row_number + 1
                        ELSE @row_number := 1 AND @prev_idEncuesta := idEncuesta END AS rn
            FROM (
                SELECT estatus, idEncuesta, fechaCreacion
                FROM ". $this->schema_cm .".encuestascreadas
                WHERE idArea = $dt
                ORDER BY idEncuesta, fechaCreacion DESC
            ) AS subquery,
            (SELECT @row_number := 0, @prev_idEncuesta := NULL) AS vars
        ) AS final_query
        WHERE rn = 1 AND estatus = 1;");

        return $query;
    }
}