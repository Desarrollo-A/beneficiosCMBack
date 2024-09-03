<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Sedes extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->load->model('EspecialistasModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');

        $this->load->model('SedesModel');
    }

    public function list(){
        $sedes = $this->SedesModel->getSedes();

        $this->json($sedes);
    }

    public function save(){
        $area = $this->input->get('area');
        $especialista = $this->input->get('especialista');
        $modalidad = $this->input->get('modalidad');
        $sede = $this->input->get('sede');
        $checked = $this->input->get('checked') === 'true' ? 1 : 0;

        $sede = $this->SedesModel->saveAtencionXEspecialista($area, $especialista, $modalidad, $sede, $checked);

        $this->json($sede);
    }

    public function oficina(){
        $especialista = $this->input->get('especialista');
        $modalidad = $this->input->get('modalidad');
        $sede = $this->input->get('sede');
        $oficina = $this->input->get('oficina');

        $save = $this->SedesModel->saveOficinaXSede($especialista, $modalidad, $sede, $oficina);

        if($save){
            $response["result"] = true;
            $response["message"] = "Se ha asignado la oficina correctamente";
        }
        else{
            $response["result"] = false;
            $response["message"] = "Error al asignar la oficina";
        }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
}

?>