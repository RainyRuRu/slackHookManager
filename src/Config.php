<?php
namespace LittleBot;

class Config
{
    private static $config_path = __DIR__ . '/../config.php';

    public static function get($key)
    {
        $config_data = Config::read();

        if ($config_data === false) {
            throw new \Exception("config error");
        }

        if (!key_exists($key, $config_data)) {
            throw new \Exception("key is not found");
        }

        return $config_data[$key];
    }


    private static function read()
    {
        if (!is_file(Config::$config_path)) {
            return false;
        }

        return include(Config::$config_path);
    }
}