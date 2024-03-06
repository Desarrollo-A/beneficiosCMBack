<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Api extends BaseController{
    public function __construct(){
		parent::__construct();

		$this->load->model('ApiModel');
		$this->load->helper(array('form','funciones'));
	}

    public function index()
	{
		$this->load->view('welcome_message');
	}

	public function nuevoHash()
	{
		$hash = $this->input->post('dataValue[hash]');
		// $hash = $this->input->post('hash');
		
		$response['result'] = isset($hash);
		if ($response['result']) {
			$ruta_archivo = APPPATH . '../dist/keys/private_key_BeneficioMaderas.pem';
			$nvoHash = SignData($hash, $ruta_archivo); // SignData($hash, $ruta_archivo);
			$response['data'] = trim($nvoHash);
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function confirmarPago()
	{
		$cl_folio = $this->input->post('cl_folio');
        $t_concepto = $this->input->post('t_concepto');
        $cl_referencia = $this->input->post('cl_referencia');
        $dl_monto = $this->input->post('dl_monto');
        $dt_fechaPago = $this->input->post('dt_fechaPago');
        $nl_tipoPago = $this->input->post('nl_tipoPago');
        $nl_estatus = $this->input->post('nl_estatus');
        $hash = $this->input->post('hash');

        $cadena = $cl_folio.'|'.$t_concepto.'|'.$cl_referencia.'|'.$dl_monto.'|'.$dt_fechaPago.'|'.$nl_tipoPago.'|'.$nl_estatus.'|';

        $response['result'] = $hash === $cadena AND isset($cl_folio,$t_concepto,$cl_referencia,$dl_monto,$dt_fechaPago,$nl_tipoPago,$nl_estatus);
        if ($response['result']) {
			
			$values['folio'] = $cl_folio;
			$values['concepto'] = $t_concepto;
			$values['referencia'] = $cl_referencia;
			$values['monto'] = $dl_monto;
			$values['fechaPago'] = $dt_fechaPago;
			$values['tipoPago'] = $nl_tipoPago;
			$values['estatusPago'] = $nl_estatus;

			$updateRecord = $this->GeneralModel->updateRecord("detallePagos", $values, "folio", $cl_folio);
            // $rs = $this->ApiModel->confirmarPago($cl_folio, $t_concepto, $cl_referencia, $dl_monto, $dt_fechaPago, $nl_tipoPago, $nl_estatus)->result();
			$response['data'] = $updateRecord;
        }else {
			$response['msg'] = '!ERROR!';
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode('estatus_notificacion=0', JSON_NUMERIC_CHECK));
    }
}