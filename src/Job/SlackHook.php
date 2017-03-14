<?php

namespace LittleBot\Job;

class SlackHook
{
    private $bot_info;
    private $channel;

    public function __construct()
    {
    }

    /**
     * @param array $bot_info
     */
    public function setBotInfo($bot_info)
    {
        $this->bot_info = $bot_info;
        return $this;
    }

    /**
     * @param array $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function send($message)
    {
        $payload = [
            'channel' => $this->channel,
            'username' => $this->bot_info['name'],
        ];

        if (is_array($message)) {
            $payload['attachments'] = [$message];
        } else {
            $payload['text'] = $message;
        }

        $data = [
            'payload' => json_encode($payload),
        ];

        $this->request($data);
    }

    private function request($data)
    {
        $ch = curl_init($this->bot_info['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        var_dump($res);
        curl_close($ch);
    }

    public static function buildMessage($title, $id, $date, $author, $message, $color = "#36a64f")
    {
        $slack_message = [
            "color" => $color,
            "author_name" => "Trac ticket",
            "title" => $title,
            "title_link" =>  "https://issue.kkcorp/trac/ticket/{$id}",
            "fields" => [
                [
                    "title" => "date",
                    "value" => $date,
                    "short" => true,
                ],
                [
                    "title" => "author",
                    "value" => $author,
                    "short" => true,
                ],
                [
                    "title" => "內容",
                    "value" => $message,
                    "short" => false,
                ]
            ]
        ];
        return $slack_message;

    }
}
