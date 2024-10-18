<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class FondoAhorroModel extends CI_Model {
	public function __construct()
	{
		$this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
		$this->ch = $this->load->database('ch', TRUE);
		parent::__construct();
	}

    public function getFondo($id)
	{
        $query = $this->ch->query(
		"SELECT * FROM fondosahorros 
        WHERE idContrato = $id AND estatusFondo IN(1,2,3,4,5) AND estatus = 1
		ORDER BY fechaCreacion DESC
		LIMIT 1;");
		return $query;
	}

	public function getSolicitudes(){
        $query = $this->ch->query(
			"SELECT 
			fa.idFondo, fa.idContrato, fa.fechaInicio, fa.fechaFin, fa.monto, fa.esReinversion, fa.estatusFondo,
			fa.estatus, fa.creadoPor, fa.fechaCreacion, fa.modificadoPor, fa.fechaModificacion, -- fondosahorros
			us.idUsuario, -- USUARIOS
			us2.num_empleado, us2.activo, us2.nombre_persona, us2.pri_apellido, us2.sec_apellido, -- DATOS CH
			oxc1.nombre AS nombreEstatusFondo, -- OXC FONDOS AHORROS
			ct.correo AS correo -- correos temporales
			FROM ". $this->schema_cm .".fondosahorros AS fa
			INNER JOIN ". $this->schema_cm .".usuarios AS us ON fa.idContrato = us.idContrato
			INNER JOIN ". $this->schema_ch .".beneficioscm_vista_usuarios AS us2 ON us.idContrato = us2.idcontrato
			INNER JOIN ". $this->schema_cm .".opcionesporcatalogo as oxc1 ON oxc1.idOpcion = fa.estatusFondo AND oxc1.idCatalogo = 21
			INNER JOIN ". $this->schema_cm .".correostemporales AS ct ON ct.idContrato = us.idContrato AND ct.estatus = 1
			WHERE fa.estatus = 1 AND us.estatus = 1"
		);
       	return $query;
    }
}