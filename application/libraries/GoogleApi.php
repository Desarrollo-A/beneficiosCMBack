<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authorization_Token
 * ----------------------------------------------------------
 * API Token Generate/Validation
 * 
 * @author: Jeevan Lal
 * @version: 0.0.1
 */

require_once(APPPATH . "libraries/google-api/vendor/autoload.php");

class GoogleApi {
    protected $client;

    protected $calendar;

    protected $service;

    protected $application_name;

    protected $scopes;

    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->config('google_api');

        $this->application_name = $this->CI->config->item('application_name');
        $this->scopes = $this->CI->config->item('scopes');

        $this->client = new Google_Client();
        $this->client->setApplicationName($this->application_name);
        $this->client->setScopes($this->scopes);
        $this->client->setAuthConfig(APPPATH . 'config/google_api_credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        //$this->client->addScope(Google_Service_Calendar::CALENDAR);

        //$this->calendar = new Google\Service\Calendar($this->client);
        $this->calendar = new Google_Service_Calendar($this->client);
    }

    public function getAccessToken($code){
        return $access_token = $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function refreshToken($refresh_token){
        return $access_token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
    }

    public function createEvent($token, $data = null){

        $this->client->setAccessToken($token);

        $event = new Google_Service_Calendar_Event(array(
            'summary' =>'something',
            'location' => 'something',
            'description' => ' test',
            'start' => array(
                'dateTime' => '2024-01-25T09:00:00-07:00',
                // 'dateTime' => $start.':00-04:00',
                'timeZone' => 'America/Mexico_City',
            ),
            'end' => array(
                'dateTime' => '2024-01-25T10:00:00-07:00',
                //'dateTime' => $end.':00-04:00',
                'timeZone' => 'America/Mexico_City',
            ),
            'attendees' => array(
                array('email' => 'programador.analista40@ciudadmaderas.com'),
                'responseStatus' => 'needsAction',
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
            'visibility' => 'public',
        ));

        $calendar_id = 'primary';

        $opts = array('sendNotifications' => true, 'conferenceDataVersion' => true);

        $event = $this->calendar->events->insert($calendar_id, $event, $opts);

        print_r($event);
    }

    public function editEvent($token, $id_event, $data = null){

        $access_token = $this->client->fetchAccessTokenWithAuthCode("4/0AfJohXltjnBeLTo9WVwcBVRiefkJmtXdAbUVBLiLpgEC-KTloTxa-q8TChHcZm9oVNXfoQ");

        print_r($access_token);
        exit();

        $this->client->setAccessToken($token);

        $event = new Google_Service_Calendar_Event(array(
            'summary' =>'evento actualizado',
            'location' => 'something',
            'description' => ' test',
            'start' => array(
                'dateTime' => '2024-01-25T10:00:00-07:00',
                // 'dateTime' => $start.':00-04:00',
                'timeZone' => 'America/Mexico_City',
            ),
            'end' => array(
                'dateTime' => '2024-01-25T11:00:00-07:00',
                //'dateTime' => $end.':00-04:00',
                'timeZone' => 'America/Mexico_City',
            ),
            'attendees' => array(
                array('email' => 'programador.analista40@ciudadmaderas.com'),
                'responseStatus' => 'needsAction',
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
            'visibility' => 'public',
        ));

        $calendar_id = 'primary';

        $opts = array('sendNotifications' => true, 'conferenceDataVersion' => true);

        $event = $this->calendar->events->update($calendar_id, $id_event, $event);

        print_r($event);
    }
}

?>