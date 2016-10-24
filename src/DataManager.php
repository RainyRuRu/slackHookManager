<?php

namespace LittleBot;

class DataManager
{
    private static $bot_path = __DIR__ . '/../data/bot.json';
    private static $massage_path = __DIR__ . '../data/message.json';

    public static function readBot()
    {
        $data = json_decode(file_get_contents(static::$bot_path), true);
        return $data;
    }

    public static function getAllMessage($id)
    {
        $data = json_decode(file_get_contents(static::$message_path), true);

    }
}
