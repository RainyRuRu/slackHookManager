<?php

namespace LittleBot\Data;

class DataManager
{
    private static $bot_path = __DIR__ . '/bot.json';
    private static $message_path = __DIR__ . '/message.json';

    public static function getAllBot()
    {
        $data = json_decode(file_get_contents(static::$bot_path), true);
        return $data['bot'];
    }

    public static function getAllMessage()
    {
        $data = json_decode(file_get_contents(static::$message_path), true);
        $data = $data['message'];
        return $data;
    }

    public static function getUrlById($id)
    {
        $url = null;
        $data = json_decode(file_get_contents(static::$bot_path), true);
        foreach ($data['bot'] as $b) {
            if ($b['id'] === (int)$id) {
                $url = $b['url'];
                break;
            }
        }
        return $url;
    }
}
