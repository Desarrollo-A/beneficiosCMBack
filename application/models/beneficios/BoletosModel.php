<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class BoletosModel extends CI_Model
{
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function getBoletos($dt)
    {
        $condicion = "";

        $dt = intval($dt);

        if ($dt != 6 && $dt != 4) {
            $condicion = "WHERE bt.estatus = 1 AND NOW() >= bt.inicioPublicacion AND NOW() <= bt.finPublicacion AND bt.sede = 1";
        }

        $query = $this->ch->query(
            "SELECT
                bt.idBoleto AS id,
                bt.titulo,
                bt.descripcion,
                bt.fechaPartido,
                bt.inicioPublicacion,
                bt.finPublicacion,
                bt.lugarPartido,
                bt.imagen,
                bt.imagenPreview,
                bt.limiteBoletos,
                bt.sede,
                bt.sede as idsede,
                bt.estatus,
                bt.fechaCreacion
            FROM " . $this->schema_cm . ".boletos bt $condicion
            ORDER BY bt.fechaCreacion DESC"
        );
        return $query;
    }

    public function getSolicitud($data)
    {

        $idBoleto = $data["idBoleto"];
		$idUsuario = $data["idUsuario"];

        $query = $this->ch->query(
            "SELECT solicitud FROM solicitudboletos sb
            WHERE idBoleto = $idBoleto AND idBeneficiario = $idUsuario"
        );

        return $query;
    }

    public function getSolicitudBoletos()
    {
        $query = $this->ch->query(
            "SELECT sb.idSolicitudBoletos AS id, bl.titulo AS nombre, 
            CONCAT(IFNULL(us2.nombre_persona, ''), ' ', IFNULL(us2.pri_apellido, ''), ' ', IFNULL(us2.sec_apellido, '')) AS beneficiario, 
            sb.solicitud, sb.fechaCreacion, us2.num_empleado, us2.ndepto, us2.nsede, us2.telefono_personal 
            FROM " . $this->schema_cm . ".solicitudboletos sb
            INNER JOIN " . $this->schema_cm . ".boletos bl ON bl.idBoleto = sb.idBoleto 
            INNER JOIN " . $this->schema_cm . ".usuarios us ON us.idUsuario = sb.idBeneficiario 
            INNER JOIN " . $this->schema_ch . ".beneficioscm_vista_usuarios us2 ON us2.idcontrato = us.idContrato 
            WHERE bl.estatus = 1 AND (NOW() >= bl.inicioPublicacion AND NOW() <= bl.finPublicacion)"
        );

        return $query;
    }
}
