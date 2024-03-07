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

	public function encodedHash()
	{
		$hash = $this->input->post('dataValue[hash]');
		
		$response['result'] = isset($hash);
		if ($response['result']) {
			$key = APPPATH . '../dist/keys/private_key_BeneficioMaderas.pem';
			$nvoHash = SignData($hash, $key);
			$response['data'] = trim($nvoHash);
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response, JSON_NUMERIC_CHECK));
	}

	public function confirmarPago()
	{
        $fecha = date('Y-m-d H:i:s');
		$usuario = 1; //Banco
		$folio = $this->input->post('cl_folio');
		$referencia = $this->input->post('cl_referencia');
		$concepto = $this->input->post('t_concepto');
		$cantidad = $this->input->post('dl_monto');
		$metodoPago = $this->input->post('nl_tipoPago');
		$estatusPago = $this->input->post('nl_estatus');
		$fechaPago = $this->input->post('dt_fechaPago');
		$hash = $this->input->post('hash');

        $cadena = $folio.'|'.$concepto.'|'.$referencia.'|'.$cantidad.'|'.$fechaPago.'|'.$metodoPago.'|'.$estatusPago.'|';
		$key = APPPATH . '../dist/keys/public_key_Banbajio.pem';
		$cadenaEncriptada = SignData($cadena, $key);

        $response['result'] = $hash === $cadenaEncriptada;
		if ($response['result']) {
			$values = [
				"folio" => $folio,
				"idConcepto" => $concepto,
				"referencia" => $referencia,
				"cantidad" => $cantidad,
				"metodoPago" => $metodoPago,
				"estatusPago" => $estatusPago,
				"fechaPago" => $fechaPago,
				"estatus" => 1,
				"creadoPor" => $usuario,
				"fechaCreacion" => $fecha,
				"modificadoPor" => $usuario,
				"fechaModificacion" => $fecha
			];
			$response["result"] = $this->GeneralModel->addRecord("detallePagos", $values);
			if ($response["result"]) {
				$rs = $this->calendarioModel->getDetallePago($folio)->result();
				if (!empty($rs) && isset($rs[0]->idDetalle)) {					
					$partes = explode('-', $referencia); // Sacamos el ultimo dato de la referencia
					$idCita = substr(end($partes), 1); //Cortamos la inicial del dato que es una letra para extraer solo el numero del id

					if ($concepto == 1) { // Actualizamos el id de cita
						$response["result"] = $this->GeneralModel->updateRecord("citas", ["idDetalle" => $rs[0]->idDetalle], 'idCita', $idCita);
						if ($response["result"]) {
							$response["msg"] = "¡Se ha generado el detalle de pago de cita con éxito!";
						}else {
							$response["msg"] = "¡Surgió un error al enlazar la cita con el pago!";
						}
					}else {
						$response["msg"] = "¡Se ha generado el detalle de pago con éxito!";
					}
				} else {
					$response["msg"] = 'No se pudo consultar los datos de detalle de pago';
				}
			} 
			else {
				$response["msg"] = "¡Surgió un error al intentar generar el detalle de pago!";
			}
		} else{
			$response['msg'] = "¡Parámetros inválidos!";
		}

		// 'estatus_notificacion=0' 
		// $response
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode('estatus_notificacion=0', JSON_NUMERIC_CHECK));
    }
}