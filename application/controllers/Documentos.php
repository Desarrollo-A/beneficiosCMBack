<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

class Documentos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

        $storage = new StorageClient([
            'keyFilePath' => APPPATH . 'config/google.json'
        ]);

        $this->config->load("google_api");

        $bucket = $this->config->item("bucket");

        $this->bucket = $storage->bucket($bucket);
	}

    public function archivo($name){
        $object = $this->bucket->object(urldecode($name));

        if($object->exists()){
            $contentType = $object->info()['contentType'];

            $file = $object->downloadAsString();

            header("Content-type: $contentType");

            print($file);
        }else{
            header("HTTP/1.1 404 Not Found");

            http_response_code(404);
        }
    }

}