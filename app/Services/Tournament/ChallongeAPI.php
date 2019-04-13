<?php

namespace App\Services\Tournament;

/**
 * Class ChallongeAPI
 * @package App\Services\Tournament
 *
 * challonge-php v1.0.1 - A PHP API wrapper class for Challonge! (http://challonge.com)
 * (c) 2014 Tony Drake
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */
class ChallongeAPI
{
    /**
     * @var string
     */
    private $api_key;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    public $warnings = [];

    /**
     * @var int
     */
    public $status_code = 0;

    /**
     * @var bool
     */
    public $verify_ssl = true;

    /**
     * @var bool
     */
    public $result = false;

    /*
      Class Constructor
      $api_key - String
    */
    public function __construct($api_key = '')
    {
        $this->api_key = $api_key;
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $method
     * @return \SimpleXMLElement|bool
     */
    public function makeCall($path = '', $params = [], $method = 'get')
    {
        // Clear the public vars
        $this->errors = [];
        $this->status_code = 0;
        $this->result = false;

        // Append the api_key to params so it'll get passed in with the call
        $params['api_key'] = $this->api_key;

        // Build the URL that'll be hit. If the request is GET, params will be appended later
        $call_url = "https://api.challonge.com/v1/" . $path . '.xml';

        $curl_handle = curl_init();
        // Common settings
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

        if (!$this->verify_ssl) {
            // WARNING: this would prevent curl from detecting a 'man in the middle' attack
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $curlheaders = []; //array('Content-Type: text/xml','Accept: text/xml');

        // Determine REST verb and set up params
        switch (strtolower($method)) {
            case "post":
                $fields = http_build_query($params, '', '&');
                $curlheaders[] = 'Content-Length: ' . strlen($fields);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $fields);
                break;

            case 'put':
                $fields = http_build_query($params, '', '&');
                $curlheaders[] = 'Content-Length: ' . strlen($fields);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $fields);
                break;

            case 'delete':
                $params["_method"] = "delete";
                $fields = http_build_query($params, '', '&');
                $curlheaders[] = 'Content-Length: ' . strlen($fields);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $fields);
                // curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;

            case "get":
            default:
                $call_url .= "?" . http_build_query($params, "", "&");
        }

        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $curlheaders);
        curl_setopt($curl_handle, CURLOPT_URL, $call_url);

        $curl_result = curl_exec($curl_handle);
        $info = curl_getinfo($curl_handle);
        $this->status_code = (int)$info['http_code'];
        $return = false;

        if ($curl_result === false) {
            // CURL Failed
            $this->errors[] = curl_error($curl_handle);
        } else {
            switch ($this->status_code) {

                case 401: // Bad API Key
                case 422: // Validation errors
                case 404: // Not found/Not in scope of account
                    $return = $this->result = new \SimpleXMLElement($curl_result);
                    foreach ($return->error as $error) {
                        $this->errors[] = $error;
                    }
                    $return = false;
                    break;
                case 500: // Oh snap!
                    $return = $this->result = false;
                    $this->errors[] = "Server returned HTTP 500";
                    break;
                case 200:
                    $return = $this->result = new \SimpleXMLElement($curl_result);
                    // Check if the result set is nil/empty
                    if (sizeof($return) == 0) {
                        $this->errors[] = "Result set empty";
                        $return = false;
                    }
                    break;
                default:
                    $this->errors[] = "Server returned unexpected HTTP Code ($this->status_code)";
                    $return = false;
            }
        }

        curl_close($curl_handle);
        return $return;
    }

    /**
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function getTournaments($params = [])
    {
        return $this->makeCall('tournaments', $params, 'get');
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function getTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id", $params, "get");
    }

    /**
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function createTournament($params = [])
    {
        if (sizeof($params) == 0) {
            $this->errors = ['$params empty'];
            return false;
        }

        return $this->makeCall("tournaments", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function updateTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id", $params, "put");
    }

    /**
     * @param $tournament_id
     * @return bool|\SimpleXMLElement
     */
    public function deleteTournament($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id", [], "delete");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function publishTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/publish/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function startTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/start/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function resetTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/reset/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @return bool|\SimpleXMLElement
     */
    public function getParticipants($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function getParticipant($tournament_id, $participant_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", $params);
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function createParticipant($tournament_id, $params = [])
    {
        if (sizeof($params) == 0) {
            $this->errors = ['$params empty'];
            return false;
        }

        return $this->makeCall("tournaments/$tournament_id/participants", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function updateParticipant($tournament_id, $participant_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", $params, "put");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @return bool|\SimpleXMLElement
     */
    public function deleteParticipant($tournament_id, $participant_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", [], "delete");
    }

    /**
     * @param $tournament_id
     * @return bool|\SimpleXMLElement
     */
    public function randomizeParticipants($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants/randomize", [], "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function getMatches($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/matches", $params);
    }

    /**
     * @param $tournament_id
     * @param $match_id
     * @return bool|\SimpleXMLElement
     */
    public function getMatch($tournament_id, $match_id)
    {
        return $this->makeCall("tournaments/$tournament_id/matches/$match_id");
    }

    /**
     * @param $tournament_id
     * @param $match_id
     * @param array $params
     * @return bool|\SimpleXMLElement
     */
    public function updateMatch($tournament_id, $match_id, $params = [])
    {
        if (sizeof($params) == 0) {
            $this->errors = ['$params empty'];
            return false;
        }

        return $this->makeCall("tournaments/$tournament_id/matches/$match_id", $params, "put");
    }
}
