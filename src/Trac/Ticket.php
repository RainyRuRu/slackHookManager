<?php
namespace LittleBot\Trac;

use LittleBot\Config;

class Ticket
{
    private $auth;
    private $api_url;
    private $owner;

    public function __construct() {
        $this->auth = Config::get("trac_auth");
        $this->api_url = Config::get("trac_api_url");
        $this->owner = Config::get("trac_owner");
    }

    public function fetchTicketByOwner()
    {
        return $response = $this->tracRequest(
            $this->buildPostBody(
                [
                    "status=!closed&owner={$this->owner}"
                ],
                "ticket.query"
            )
        );
    }


    public function fetchTicketByCC()
    {
        return $response = $this->tracRequest(
            $this->buildPostBody(
                [
                    "status=!closed&owner!={$this->owner}&cc=~{$this->owner}",
                ],
                "ticket.query"
            )
        );
    }

    public function getRecentChange($timestamp)
    {
        $body = $this->buildPostBody([$this->buildDatetimeArray($timestamp)], "ticket.getRecentChanges");
        $response = $this->tracRequest($body);
        $trac_list_id = (null === $response) ? [] : $response;
        return $trac_list_id;
    }

    public function get($id)
    {
        $id = (int) $id;
        $body = '{"params": [' . $id . '], "method": "ticket.get"}';
        $response = $this->tracRequest($body);
        return $response;
    }

    public function changeLog($id)
    {
        $id = (int) $id;
        $body = '{"params": [' . $id . '], "method": "ticket.changeLog"}';
        $response = $this->tracRequest($body);
        return $response;

    }

    private function tracRequest($body)
    {
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: basic {$this->auth}",
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        if (!is_array($data) || !is_array($data['result'])) {
            return null;
        }
        curl_close($ch);
        $result = $data['result'];
        return $result;
    }

    private function buildPostBody(array $params, $method)
    {
        $body = [
            "params" => $params,
            "method" => $method,
        ];

        return json_encode($body);
    }

    private function buildDatetimeArray($timestamp)
    {
        $date = gmdate("o-m-d\TH:i:s", $timestamp);
        $time_array = [
            "__jsonclass__" => [
                "datetime",
                $date
            ],
        ];

        return $time_array;
    }

    private function parseJsonClassToArray()
    {

    }
}
