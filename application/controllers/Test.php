<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "/controllers/BaseController.php");

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->ch = $this->load->database('ch', TRUE);
        $this->schema_ch = $this->config->item('schema_ch');
	}

    public function database(){
        $citas = $this->ch->query("SELECT * 
		FROM ". $this->schema_ch .".beneficioscm_vista_usuarios")->result();

        // $citas = $this->ch->query($query)

        print_r($citas);

        exit;
    }

}

?>