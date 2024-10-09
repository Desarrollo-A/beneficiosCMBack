<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class CatalogosController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('catalogos/CatalogosModel');
        $this->load->model('GeneralModel');
        $this->load->library('GoogleApi');
        $this->ch = $this->load->database('ch', TRUE);
        date_default_timezone_set('America/Mexico_City');
        $this->schema_cm = $this->config->item('schema_cm');
    }

    public function getCatalogos()
    {
        $data['data'] = $this->CatalogosModel->getCatalogos();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function getCatalogosOp($idCatalogo)
    {
        $data['data'] = $this->CatalogosModel->getCatalogosOp($idCatalogo);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function updateCatalogoEstatus()
    {
        $idCatalogo = $this->input->post('dataValue[idCatalogo]');
        $estatus = $this->input->post('dataValue[estatus]');
        $idUsuario = $this->input->post('dataValue[idUsuario]');
        $data = [
            "estatus" => $estatus,
            "modificadoPor" => $idUsuario,
            "fechaModificacion" => date("Y-m-d H:i:s")
        ];
        $resultado = $this->GeneralModel->updateRecord($this->schema_cm . ".catalogos", $data, 'idCatalogo', $idCatalogo);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function updateCatalogOpEstatus()
    {
        $idOpcion = $this->input->post('dataValue[idOpcion]');
        $idCatalogo = $this->input->post('dataValue[idCatalogo]');
        $estatusOp = $this->input->post('dataValue[estatusOp]');
        $idUsuario = $this->input->post('dataValue[idUsuario]');

        $resultado = $this->CatalogosModel->updateEstatusOp($idOpcion, $idCatalogo, $estatusOp, $idUsuario);

        if ($resultado) {
            $response = ["estatus" => true, "msj" => "Datos actualizados"];
        } else {
            $response = ["estatus" => false, "msj" => "Error al actualizar"];
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function addCatalogos()
    {
        $nombreCatalogo = $this->input->post('dataValue[nombreCatalogo]');
        $estatus = $this->input->post('dataValue[estatus]');
        $idUsuario = $this->input->post('dataValue[idUsuario]');
        $response['result'] = isset($nombreCatalogo, $estatus, $idUsuario);

        if ($response['result']) {
            $dataCatalogos = [
                'nombre' => $nombreCatalogo,
                'estatus' => $estatus,
                'creadoPor' => $idUsuario,
                'modificadoPor' => $idUsuario
            ];
            $this->GeneralModel->addRecord($this->schema_cm . ".catalogos", $dataCatalogos);
            $response = [
                'status' => 'success',
                'msg' => 'Catálogo agregado'
            ];
        } else {
            $response = [
                'status' => 'error',
                'msg' => 'Faltan datos '
            ];
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }

    public function addCatalogosOp()
    {
        $idOpcion = $this->input->post('dataValue[idOpcion]');
        $idCatalogo = $this->input->post('dataValue[idCatalogo]');
        $nombreCatalogOp = $this->input->post('dataValue[nombreCatalogOp]');
        $estatusOp = $this->input->post('dataValue[estatusOp]');
        $idUsuario = $this->input->post('dataValue[idUsuario]');
        $response['result'] = isset($idOpcion, $nombreCatalogOp, $estatusOp, $idUsuario);

        if ($response['result']) {
            $dataCatalogosOp = [
                'idOpcion' => $idOpcion,
                'idCatalogo' => $idCatalogo,
                'nombre' => $nombreCatalogOp,
                'estatus' => $estatusOp,
                'creadoPor' => $idUsuario,
                'modificadoPor' => $idUsuario
            ];
            $this->GeneralModel->addRecord($this->schema_cm . ".opcionesporcatalogo", $dataCatalogosOp);
            $response = [
                'status' => 'success',
                'msg' => 'Catálogo agregado.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'msg' => 'Faltan datos'
            ];
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
    }
}