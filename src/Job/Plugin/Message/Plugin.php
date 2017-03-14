<?php
namespace LittleBot\Job\Plugin\Message;

use LittleBot\Job\Plugin\AbstractPlugin;
use LittleBot\Job\SlackHook;
use LittleBot\Data\DataManager;
use LittleBot\Config;

class Plugin extends AbstractPlugin
{
    protected $bot;

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $messages = DataManager::getAllMessage();
        foreach ($messages as $message) {
            if ($this->isReady($message)){
                $bot_info = DataManager::getBotInfo($message['id']);
                $this->bot->setBotInfo($bot_info)->send($message['message']);
            }
        }
    }

    public function setBot()
    {
        $bot_info = DataManager::getBotInfo(1);
        return (new SlackHook())
            ->setBotInfo($bot_info)
            ->setChannel("#hello_sherman");
    }

    private function isReady($message)
    {
        $time_interval = Config::get("time_interval");
        $end_timestamp = strtotime("+{$time_interval} minutes");

        $week = date('N') - 1;
        if ($message['repeat'][$week] !== "1" && $message['repeat'] !== "00000") {
            return false;
        }

        if ($message['repeat'] === '00000') {
            $timestamp = strtotime($message['date']);
        } else {
            $today = date('Y-m-d', time());
            $date = explode(" ", $message['date']);
            $time = $date[1];
            $timestamp = strtotime($today . " " . $time);
        }

        if (time() <= $timestamp && $timestamp < $end_timestamp) {
            return true;
        }

        return false;
    }


}