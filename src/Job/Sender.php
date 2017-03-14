<?php

namespace LittleBot\Job;

use LittleBot\Config;

class Sender
{
    public function run()
    {
        $this->loadPlugin();
    }

    private function loadPlugin()
    {
        foreach ($this->getPlugin() as $pluginClass) {
            $plugin = new $pluginClass();
            $plugin->run();
        }
    }

    private function getPlugin()
    {
        return Config::get("plugin");
    }
}
