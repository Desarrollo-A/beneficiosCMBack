<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class calendarioController extends CI_Controller{
    public function __construct(){
        parent::__construct();

        $this->load->model('calendarioModel');
    }

    public function get_occupied(){
		$data = $this->calendarioModel->getOccupied();

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function save_occupied(){
		date_default_timezone_set('America/Mexico_City');

		$fecha = $this->input->post("fecha", true);
		$hora_inicio = $this->input->post("hora_inicio", true);
		$hora_final = $this->input->post("hora_final", true);
		$id_especialista = 0; // $this->input->post("id_especialista", true);
		$creado_por = 0; // $this->input->post("creado_por", true);
		$fecha_modificacion = date("Y-m-d H:i:s");
		$fecha_creacion = date("Y-m-d H:i:s");
		$titulo = $this->input->post("titulo", true);
		$id_unico = $this->input->post('id_unico', true);

		$save = $this->calendarioModel->saveOccupied(
			$fecha, 
			$hora_inicio, 
			$hora_final, 
			$id_especialista, 
			$creado_por, 
			$fecha_modificacion, 
			$fecha_creacion, 
			$titulo, 
			$id_unico
		);

		if($save["status"]){
			$data["status"] = true;
			$data["message"] = "Se ha guardado el horario";
		}
		else{
			$data["status"] = false;
			$data["message"] = "Error al guardar el horario";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function update_occupied(){
		date_default_timezone_set('America/Mexico_City');

		$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
		$request = json_decode($stream_clean);

		$hora_inicio = $request->hora_inicio;
		$hora_final = $request->hora_final;
		$fecha_modificacion = date("Y-m-d H:i:s");
		$titulo = $request->titulo;
		$id_unico = $request->id_unico;

		// $hora_inicio = $this->input->post("hora_inicio", true);
		// $hora_final = $this->input->post("hora_final", true);
		// $fecha_modificacion = date("Y-m-d H:i:s");
		// $titulo = $this->input->post("titulo", true);
		// $id_unico = $this->input->post('id_unico', true);

		$save = $this->calendarioModel->updateOccupied(
			$hora_inicio, 
			$hora_final,
			$fecha_modificacion, 
			$titulo, 
			$id_unico
		);

		if($save["status"]){
			$data["status"] = true;
			$data["message"] = "Se ha guardado el horario";
		}
		else{
			$data["status"] = false;
			$data["message"] = "Error al guardar el horario";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	public function delete_occupied(){
		$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
		$request = json_decode($stream_clean);

		$id_unico = $request->id_unico;
		// $id_unico = $this->input->post('id_unico', true);
		var_dump($id_unico);

		$delete = $this->calendarioModel->deleteOccupied($id_unico);

		if($delete["status"]){
			$data["status"] = true;
			$data["message"] = "Se ha eliminado el horario";
		}
		else{
			$data["status"] = true;
			$data["message"] = "No se ha eliminado el horario";
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
}