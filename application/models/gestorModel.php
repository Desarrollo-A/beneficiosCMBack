<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class gestorModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}


    public function getOficinasVal($dt)
    {
        $query = $this->db-> query("SELECT idOficina, oficina FROM oficinas WHERE idSede =  $dt");
        
        if($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getEspecialistasVal($dt)
    {

        $idSede = $dt["idSd"];
        $idPuesto = $dt["idPs"];

        $query = $this->db-> query("SELECT idUsuario, nombre FROM usuarios WHERE idSede = $idSede AND idRol = 3 AND idPuesto = $idPuesto");
        
        if($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getEsp($dt)
    {

        $query = $this->db-> query("SELECT idUsuario, nombre FROM usuarios WHERE idRol = 3 AND idPuesto = $dt");
        
        if($query->num_rows() > 0){
            return $query->result();
        }
        else{
            return false;
        }
    }

    public function getSedeNone($dt)
    {
        $query = $this->db-> query("SELECT  *
        FROM sedes
        WHERE idSede NOT IN 
            (SELECT axs.idSede 
            FROM atencionXSede axs
            INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
            INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
            WHERE ps.idPuesto = $dt)");
        
        return $query->result();
        
    }

    public function getSedeNoneEsp($dt)
    {
        $query = $this->db-> query("SELECT  *
        FROM sedes
        WHERE idSede NOT IN 
            (SELECT axs.idSede 
            FROM atencionXSede axs
            INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
            INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
            WHERE ps.idPuesto = $dt)");
        
        return $query->result();
        
    }

    public function insertAtxSede($dt)
    {
        $data = json_decode($dt, true);

        $datosValidos = true;

        if (isset($data)) {

			foreach ($data["items"] as $item) {

                $idEspecialista = $item["especialista"];
                $idSede = $item["sede"];
                $idOficina = $item["oficina"];
                $tipoCita = $item["modalidad"];
                $idUsuario = $item["usuario"];

				if (empty($idEspecialista) ||
                    empty($idSede) ||
                    empty($idOficina) ||
                    empty($tipoCita) ||
                    empty($idUsuario)) {

					echo json_encode(array("estatus" => false, "msj" => "Faltan datos!" ));
					$datosValidos = false;

					break; 
				}else{

                    $query = $this->db->query("INSERT INTO atencionXSede
                    (idEspecialista, idSede, idOficina, tipoCita, estatus, creadoPor, fechaCreacion, modificadoPor)
                    VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?)", 
                    array($idEspecialista, $idSede, $idOficina, $tipoCita, 1, $idUsuario, $idUsuario ));
                    
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        echo "Error al realizar el registro";
                    } else {
                        echo json_encode(array("estatus" => true, "msj" => "Registro realizado exitosamente" ));
                    }
                
                }
			}
        } else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function getAtencionXsedeEsp($dt)
    {
        $query = $this->db-> query("SELECT axs.idAtencionXSede AS id,axs.idSede, sd.sede, o.oficina, o.ubicación, us.nombre, ps.idPuesto, ps.puesto, op.nombre AS modalidad, axs.estatus
        FROM atencionXSede axs
        INNER JOIN sedes sd ON sd.idSede = axs.idSede
        INNER JOIN oficinas o ON o.idOficina = axs.idOficina
        INNER JOIN usuarios us ON us.idUsuario = axs.idEspecialista
        INNER JOIN puestos ps ON ps.idPuesto = us.idPuesto
        INNER JOIN catalogos ct ON ct.idCatalogo = 5
        INNER JOIN opcionesPorCatalogo op ON op.idCatalogo = ct.idCatalogo AND op.idOpcion = axs.tipoCita
		WHERE us.idPuesto = $dt");
        
        return $query->result();
        
    }

}