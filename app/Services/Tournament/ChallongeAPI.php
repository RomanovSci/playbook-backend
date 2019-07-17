<?php
declare(strict_type = 1);

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

    /**
     * @param string $path
     * @param array $params
     * @param string $method
     * @return array
     */
    public function makeCall($path = '', $params = [], $method = 'get')
    {
        $this->api_key = env('CHALLONGE_API_KEY', '');

        // Clear the public vars
        $this->errors = [];
        $this->status_code = 0;
        $this->result = false;

        // Append the api_key to params so it'll get passed in with the call
        $params['api_key'] = $this->api_key;

        // Build the URL that'll be hit. If the request is GET, params will be appended later
        $call_url = "https://api.challonge.com/v1/" . $path . '.json';

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
        $this->status_code = (int) $info['http_code'];

        if ($curl_result === false) {
            // CURL Failed
            $this->errors[] = curl_error($curl_handle);
        } else {
            switch ($this->status_code) {
                case 401: // Bad API Key
                case 422: // Validation errors
                case 404: // Not found/Not in scope of account
                    $this->errors = json_decode($curl_result)->errors;
                    break;
                case 500: // Oh snap!
                    $this->errors[] = "Server returned HTTP 500";
                    break;
                case 200:
                    $this->result = json_decode($curl_result);
                    break;
                default:
                    $this->errors[] = "Server returned unexpected HTTP Code ($this->status_code)";
            }
        }

        curl_close($curl_handle);

        return [
            'status_code' => $this->status_code,
            'errors' => $this->errors,
            'data' => $this->result ?? null,
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getTournaments($params = [])
    {
        return $this->makeCall('tournaments', $params, 'get');
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function getTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id", $params, "get");
    }

    /**
     * @param array $params
     * @return array
     */
    public function createTournament(array $params)
    {
        return $this->makeCall("tournaments", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function updateTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id", $params, "put");
    }

    /**
     * @param $tournament_id
     * @return array
     */
    public function deleteTournament($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id", [], "delete");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function publishTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/publish/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function startTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/start/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function resetTournament($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/reset/$tournament_id", $params, "post");
    }

    /**
     * @param $tournament_id
     * @return array
     */
    public function getParticipants($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @param array $params
     * @return array
     */
    public function getParticipant($tournament_id, $participant_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", $params);
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function createParticipant($tournament_id, array $params)
    {
        return $this->makeCall("tournaments/$tournament_id/participants", $params, "post");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @param array $params
     * @return array
     */
    public function updateParticipant($tournament_id, $participant_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", $params, "put");
    }

    /**
     * @param $tournament_id
     * @param $participant_id
     * @return array
     */
    public function deleteParticipant($tournament_id, $participant_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants/$participant_id", [], "delete");
    }

    /**
     * @param $tournament_id
     * @return array
     */
    public function randomizeParticipants($tournament_id)
    {
        return $this->makeCall("tournaments/$tournament_id/participants/randomize", [], "post");
    }

    /**
     * @param $tournament_id
     * @param array $params
     * @return array
     */
    public function getMatches($tournament_id, $params = [])
    {
        return $this->makeCall("tournaments/$tournament_id/matches", $params);
    }

    /**
     * @param $tournament_id
     * @param $match_id
     * @return array
     */
    public function getMatch($tournament_id, $match_id)
    {
        return $this->makeCall("tournaments/$tournament_id/matches/$match_id");
    }

    /**
     * @param $tournament_id
     * @param $match_id
     * @param array $params
     * @return array
     */
    public function updateMatch($tournament_id, $match_id, array $params)
    {
        return $this->makeCall("tournaments/$tournament_id/matches/$match_id", $params, "put");
    }
}
