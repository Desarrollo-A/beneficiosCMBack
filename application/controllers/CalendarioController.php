<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarioController extends CI_Controller{
    public function __construct(){
		parent::__construct();

        $this->load->model('calendarioModel');
		$this->load->library('session');
		date_default_timezone_set('America/Mexico_City');
	}

	public function getOccupied(){

		$data = $this->input->post("dataValue", true);
		$year = $data["year"];
		$month = $data["month"];
		$id_usuario = $data["idUsuario"];

		$dates = [
			"month_1" => $month_1 = ($month - 1) === 0 ? 12 : ($month - 1),
        	"month_2" => $month_2 = ($month + 1) > 12 ? 1 : ($month + 1),
        	"year_1" => $year_1 =  intval($month) === 1 ? $year - 1 : $year,
        	"year_2" => $year_2 =  intval($month) === 12 ? $year + 1 : $year
		];
		
		$data = $this->calendarioModel->getOccupied($year, $month, $id_usuario, $dates);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	public function get1(){

		$data = $this->input->post("dataValue", true);
		$year = $data["year"];
		$month = $data["month"];
		$id_usuario = $data["idUsuario"];

		$dates = [
			"month_1" => $month_1 = ($month - 1) === 0 ? 12 : ($month - 1),
        	"month_2" => $month_2 = ($month + 1) > 12 ? 1 : ($month + 1),
        	"year_1" => $year_1 =  intval($month) === 1 ? $year - 1 : $year,
        	"year_2" => $year_2 =  intval($month) === 12 ? $year + 1 : $year
		];
		
		$data1 = $this->calendarioModel->get1($year, $month, $id_usuario, $dates);
		$data2 = $this->calendarioModel->get2($year, $month, $id_usuario, $dates);

		if ($data1->num_rows() > 0 || $data2->num_rows() > 0) {
            $dataa["events"] = array_merge($data1->result(), $data2->result());
        } 
        else {
            $dataa["events"] = array('');
        }

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($dataa));
	}


	public function saveOccupied(){
		$data = $this->input->post("dataValue");

		$save = $this->calendarioModel->saveOccupied($data);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($save));
	}

	public function update_occupied(){
		$data = $this->input->post("data", true);

		$start = $data["start"];
		$oldStart = $data["oldStart"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');

		if($oldStart > $now){
			if($start > $now){
				$update = $this->calendarioModel->updateOccupied($data);
			}
			else{
				$update["status"] = false;
				$update["message"] = "No se pueden mover las fechas a un dia anterior o actual";
			}
		}
		else{
			$update["status"] = false;
			$update["message"] = "Las citas u horarios pasados no se pueden mover";
		} 

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($update));
	}

	public function delete_occupied(){
		$id_unico = $this->input->post("data", true);
		
		$delete = $this->calendarioModel->deleteOccupied($id_unico);

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($delete));
	}

	public function delete_date(){
		$id = $this->input->post("data", true);
		$delete = $this->calendarioModel->deleteDate($id);

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($delete));
	}

	function create_appointment(){
		$data = $this->input->post("data", true);

		$create = $this->calendarioModel->createAppointment( $data );

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($create));
	}

	function update_on_drop(){
		$data = $this->input->post("data", true);
		$oldStart = $data["oldStart"];
		$start = $data["start"];
		$current = new DateTime();
		$now = $current->format('Y/m/d');
		
		if($oldStart > $now){
			if($start > $now){
				if($data["tipo"] === "cita"){
					$update = $this->calendarioModel->onDropAppointment($data);
				}
				else{
					$update = $this->calendarioModel->onDropOccupied($data);
				}
			}
			else{
				$update["status"] = false;
				$update["message"] = "No se pueden mover las fechas a un dia anterior o actual";
			}
		  }
		  else{
			$update["status"] = false;
			$update["message"] = "Las citas u horarios pasados no se pueden mover";
		  } 

		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($update));
	}

	function getBeneficiosDisponibles(){
		$datosEmpleado = json_decode(file_get_contents('php://input'));
		print_r($datosEmpleado);
		echo '<br><br>';
		print_r($this->session->userdata());

		exit;
    	$dataButton = $this->calendarioModel->revisaCitas();
		$data['beneficios'] = $this->calendarioModel->getBeneficiosDisponibles();
		print_r(json_encode($data));
	}
}
