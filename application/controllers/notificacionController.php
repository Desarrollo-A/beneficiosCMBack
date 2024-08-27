<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "/controllers/BaseController.php");

class NotificacionController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->load->model('NotificacionModel');
        $this->load->model('GeneralModel');
        $this->ch = $this->load->database('ch', TRUE);
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function getNotificacion(){
        $dt = $this->input->post('dataValue[idUsuario]', true);
		$data['data'] = $this->NotificacionModel->getNotificacion($dt)->result();
		echo json_encode($data, JSON_NUMERIC_CHECK);
    }

    public function deleteNotificacion(){
        $dt = $this->input->post('dataValue', true);
		$data['data'] = $this->NotificacionModel->deleteNotificacion($dt);
		echo json_encode($data, JSON_NUMERIC_CHECK);
    }
}

?>