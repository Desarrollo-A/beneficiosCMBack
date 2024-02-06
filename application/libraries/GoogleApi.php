<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . "libraries/http/IClient.php");
require_once(APPPATH . "libraries/http/IRequest.php");
require_once(APPPATH . "libraries/http/Request.php");
require_once(APPPATH . "libraries/http/IResponse.php");
require_once(APPPATH . "libraries/http/Response.php");
require_once(APPPATH . "libraries/http/Client.php");

use RestClient\Client;

class GoogleApi {
    protected $token;

    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->config('google_api');
    }

    public function getAccessToken($email){
        // Get config vars
        $token_url = $this->CI->config->item('token_url');
        $private_key_id = $this->CI->config->item('private_key_id');
        $private_key = $this->CI->config->item('private_key');
        $service_account = $this->CI->config->item('service_account');
        $scopes = $this->CI->config->item('scopes');
        $redirect_uri = $this->CI->config->item('redirect_uri');
        $application_name = $this->CI->config->item('application_name');
        $client_id = $this->CI->config->item('client_id');
        $client_secret = $this->CI->config->item('client_secret');
        $api_key = $this->CI->config->item('api_key');

        $header = [
            "alg" => "RS256",
            "typ" => "JWT",
            "kid" => $private_key_id
        ];

        $header = base64_encode(json_encode($header));

        $claims = [
            "iss" => $service_account,
            "scope" => $scopes,
            "aud" => $token_url,
            "exp" => time() + (1 * 60),
            "iat" => time(),
            "sub" => $email,
        ];

        $claims = base64_encode(json_encode($claims));

        $binary_signature = "";
        $algo = "SHA256";
        $data = $header.".".$claims;
        openssl_sign($data, $binary_signature, $private_key, $algo);

        $firm = urlencode(base64_encode($binary_signature));

        //$firm = hash_hmac('sha256', "$header.$claims", $private_key_id);

        //print_r($firm);
        //exit;

        $jwt = "$header.$claims.$firm";

        //$code = "4/0AfJohXnIdjh2Is1Lt9KzmWWjO9eNd3ullAXvDUHgWz7VGGNpqE1PjqGemu4fihwGZXD25A";

        $data = "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion=$jwt";

        $client = new Client();

        $headers = [
            "Content-type" => "application/x-www-form-urlencoded",
        ];

        $request = $client->newRequest($token_url, 'POST', $data, $headers);

        $response = $request->getResponse();

        $this->token = json_decode($response->getParsedResponse())->access_token;
    }

    public function createCalendarEvent($calendar_name = 'primary', $data){

        //INSERT ITEM IN CALENDAR
        $calendar_url = "https://www.googleapis.com/calendar/v3/calendars/$calendar_name/events?sendNotifications=true&sendUpdates=all";

        $headers = [
            "Authorization" => "Bearer $this->token",
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Referer" => "https://prueba.gphsis.com/",
        ];

        $client = new Client();

        $request = $client->newRequest($calendar_url, 'POST', $data, $headers);

        $response = $request->getResponse();

        return json_decode($response->getParsedResponse());
    }

    public function updateCalendarEvent($calendar_name = 'primary', $id, $data){

        //INSERT ITEM IN CALENDAR
        $calendar_url = "https://www.googleapis.com/calendar/v3/calendars/$calendar_name/events/$id?sendNotifications=true&sendUpdates=all";

        $headers = [
            "Authorization" => "Bearer $this->token",
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Referer" => "https://prueba.gphsis.com/",
        ];

        $client = new Client();

        $request = $client->newRequest($calendar_url, 'PATCH', $data, $headers);

        $response = $request->getResponse();

        return json_decode($response->getParsedResponse());
    }

    public function deleteCalendarEvent($calendar_name = 'primary', $id){

        //INSERT ITEM IN CALENDAR
        $calendar_url = "https://www.googleapis.com/calendar/v3/calendars/$calendar_name/events/$id?sendUpdates=all";

        $headers = [
            "Authorization" => "Bearer $this->token",
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Referer" => "https://prueba.gphsis.com/",
        ];

        $client = new Client();

        $request = $client->newRequest($calendar_url, 'DELETE', null, $headers);

        $response = $request->getResponse();

        return json_decode($response->getParsedResponse());
    }
}

?>