<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class GestorModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}


    public function getOficinasVal($dt)
    {
        $query = $this->db-> query("SELECT idOficina, oficina FROM oficinas WHERE idSede =  $dt OR idSede =  0");
        
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

        $query = $this->db-> query("SELECT idUsuario, nombre FROM usuarios WHERE idRol = 3 AND idPuesto = $idPuesto");
        
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

    public function getOficinas($dt)
    {
        $idRol = $dt["idRol"];
        $idPuesto = $dt["idPuesto"];
        
            $query = $this->db-> query("SELECT ofi.idOficina, ofi.oficina, sd.idSede, sd.sede, ofi.ubicación, ofi.estatus 
            FROM oficinas ofi
            INNER JOIN sedes sd ON sd.idSede = ofi.idSede
            ORDER BY ofi.idOficina");
            return $query;
    }

    public function insertOficinas($dt)
    {
        $data = json_decode($dt, true);

        $datosValidos = true;

        if (isset($data)) {

                $oficina = $data["ofi"];
                $idSede = $data["idSede"];
                $ubicacion = $data["ubi"];
                $estatus = $data["estatus"];
                $creadoPor = $data["creadoPor"];

				if (empty($oficina) ||
                    empty($idSede) ||
                    empty($ubicacion) ||
                    empty($creadoPor)) {

					echo json_encode(array("estatus" => false, "msj" => "Faltan datos!" ));
					$datosValidos = false;

				}else{

                    $query = $this->db->query("INSERT INTO oficinas (oficina, idSede, ubicación, estatus, creadoPor, fechaCreacion ) 
                    VALUES (?, ?, ?, ?, ?, GETDATE())", 
                    array($oficina, $idSede, $ubicacion, $estatus, $creadoPor));
                    
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        echo "Error al realizar el registro";
                    } else {
                        echo json_encode(array("estatus" => true, "msj" => "Registro realizado exitosamente" ));
                    }
                
                }

        } else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function insertSedes($dt)
    {
        $data = json_decode($dt, true);

        $datosValidos = true;

        if (isset($data)) {

                $sede = $data["sede"];
                $abreviacion = $data["abreviacion"];
                $estatus = $data["estatus"];
                $creadoPor = $data["creadoPor"];

				if (empty($sede) ||
                    empty($abreviacion) ||
                    empty($creadoPor)) {

					echo json_encode(array("estatus" => false, "msj" => "Faltan datos!" ));
					$datosValidos = false;

				}else{

                    $query = $this->db->query("INSERT INTO sedes (sede, abreviacion, estatus, creadoPor, fechaCreacion ) 
                    VALUES (?, ?, ?, ?, GETDATE())", 
                    array($sede, $abreviacion, $estatus, $creadoPor));
                    
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        echo "Error al realizar el registro";
                    } else {
                        echo json_encode(array("estatus" => true, "msj" => "Registro realizado exitosamente" ));
                    }
                
                }

        } else {
			echo json_encode(array("estatus" => false, "msj" => "Error Faltan Datos" ));
		}
    }

    public function checkAxs($dt){
        $query = $this->db->query("SELECT *from atencionXSede where idEspecialista = ? AND idSede = ? AND idOficina = ? AND tipoCita = ?", 
        array($dt["especialista"], $dt["sede"], $dt["oficina"], $dt["modalidad"]));

        return $query;
    }

    public function getAxs($idAts){
        $query = $this->db->query("SELECT *from atencionXSede WHERE idAtencionXSede = ?", $idAts);

        return $query;
    }
}