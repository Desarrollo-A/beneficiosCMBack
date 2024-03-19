<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class AvisosPrivacidadController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->ch = $this->load->database('ch', TRUE);
		$this->load->model('calendarioModel');
		$this->load->model('avisosPrivacidadModel');
		$this->load->model('generalModel');
		$this->load->model('usuariosModel');

		date_default_timezone_set('America/Mexico_City');
	}


	function getEspecialidades(){
		$data['data'] = $this->avisosPrivacidadModel->getEspecialidades();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	function getAvisoPrivacidad($idEspecialidad){
		$data = $this->avisosPrivacidadModel->getAvisoPrivacidadByEsp($idEspecialidad);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
	}

	function actualizarArchivoPrivacidad(){
		$accion = $this->input->post('accion');
		//tipoDeAccion: 1: nuevo 2: Editar
		$nombreEspecialidad = $this->input->post('nombreEspecialidad');
		$nombreEspecialidad =  eliminar_acentos($nombreEspecialidad);
		$file = $_FILES["archivo"]['name'];
		$fileExt = pathinfo($file, PATHINFO_EXTENSION);
		$folder = 'dist/documentos/avisos-privacidad/';
		$dataExpedienteGenerado = $this->nuevoNombreArchivo($nombreEspecialidad,$file);
		$id_usuario_actual = $this->input->post('idUsuario');
			if ($fileExt != 'pdf') {
				// SE INTENTÓ SUBIR UN ARCHIVO DIFERENTE A UN .pdf (CORRIDA)
				echo json_encode(array('code' => 400, 'message' => 'El archivo que se intenta subir no cuenta con la extención .pdf'));
				return;
			}
			else{
					$movement = move_uploaded_file($_FILES["archivo"]['tmp_name'], $folder . $dataExpedienteGenerado['expediente']);
					if ($movement) {

						//acciónEditar
						if ($accion == 2) {
							$idDocumento = $this->input->post('idDocumento');
							$validacionRama = $this->avisosPrivacidadModel->revisaRamaActiva($idDocumento);

							if (count($validacionRama) > 0) {
								$updateDocumentData = array(
									"expediente" => $dataExpedienteGenerado['expediente'],
									"fechaModificacion" => date_format($dataExpedienteGenerado['date'], "Y-m-d H:i:s"),
									"modificadoPor" => $this->session->userdata('id_usuario')
								);
								$result = $this->generalModel->updateRecord("PRUEBA_beneficiosCM.historialdocumento", $updateDocumentData, "idDocumento", $idDocumento);
								$archivoAnterior = $validacionRama[0]['expediente'];
								$rutaArchivo = 'dist/documentos/avisos-privacidad/';
								$rutaEliminarArchivo =  $rutaArchivo.$archivoAnterior;
								if (file_exists($rutaEliminarArchivo)) {
									unlink($rutaEliminarArchivo);
								}
							}
						}
						elseif ($accion == 1) {//accion primer insert
							$idEspecialidad = $this->input->post('idEspecialidad');
							$insertDocumentData = array(
								"movimiento" => 'Se sube un nuevo archivo por usuario',
								"expediente" => $dataExpedienteGenerado['expediente'],
								"estatus" => 1,
								"tipoDocumento" => 1,
								"tipoEspecialidad" => $idEspecialidad,
								"creadoPor" => $id_usuario_actual,
								"fechaCreacion" => date('Y-m-d H:i:s'),
								"fechaModificacion" => date('Y-m-d H:i:s'),
								"modificadoPor" => $id_usuario_actual
							);

							$result = $this->generalModel->addRecord('PRUEBA_beneficiosCM.historialdocumento', $insertDocumentData);
						}
						return ($result)
							? print_r(json_encode(array('code' => 200, 'message'=>'Se ha editado correctamente')))
							: print_r(json_encode(array('code' => 500, 'message'=>'Ocurrió un error, intentalo nuevamente')));
					}else{
						return print_r(json_encode(array('code' => 500, 'message'=>'Ocurrió un error al mover el archivo en el servidor, inténtalo nuevamente')));
					}


				}



		exit;
	}

	function nuevoNombreArchivo($nombreEspecialidad, $file = 'nombreDelArchivo'){
		$aleatorio = rand(100, 1000);
		$ahora = date('Y-m-d H:i:s');
		$date = date_create($ahora);
		$nombreEspecialidad = str_replace(' ', '', $nombreEspecialidad);
		$composicion =  "AvisoDePrivacidad-" . $nombreEspecialidad . "-" . date_format($date,"YmdHis");
		$nombArchivo = $composicion;
		$extension = pathinfo($file, PATHINFO_EXTENSION);
		return
			array(
				'expediente'=>$expediente = $nombArchivo . '_' . $aleatorio . '.' . $extension,
				'date' => $date
				);
	}


}
