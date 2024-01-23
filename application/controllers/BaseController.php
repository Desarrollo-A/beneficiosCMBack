<?php

abstract class BaseController extends CI_Controller{
    public function __construct(){
        parent::__construct();

        date_default_timezone_set('America/Mexico_City');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
    }
}

?>