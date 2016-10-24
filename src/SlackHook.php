<?php

namespace LittleBot;

class SlackHook
{
    private $url;

    public function  __construct($url) {
        $this->url = $url;
    }
    public function log($msg) {
        $payload = [
            'text' => $msg,
            'channel' => '#hello_sherman',
            'username' => '沙沙',
        ];
        $data = [
            'payload' => json_encode($payload),
        ];
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        curl_close($ch);
    }
}
