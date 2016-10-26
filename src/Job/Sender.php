<?php

namespace LittleBot\Job;

use LittleBot\Data\DataManager;
use LittleBot\Job\SlackHook;

class Sender
{
    private $slack;

    public function __construct()
    {
        $this->slack = new SlackHook();
    }

    public function run() {
        $messages = DataManager::getAllMessage();
        foreach ($messages as $m) {
            $go = $this->checkTime($m);
            if ($go) {
                $url = DataManager::getUrlById($m['id']);
                $this->slack->send($url, $m['message']);
            }
        }
    }

    private function checkTime($m)
    {
        $fiveMinute = strtotime("+5 minutes");
        $timestamp = 0;

        $week = date('N') - 1;
        if ($m['repeat'][$week] !== "1" && $m['repeat'] !== "00000") {
            return false;
        }

        if ($m['repeat'] === '00000') {
            var_dump("hi");
            $timestamp = strtotime($m['date']);
        } else {
            $today = date('Y-m-d', time());
            $date = explode(" ", $m['date']);
            $time = $date[1];
            $timestamp = strtotime($today . " " . $time);
        }

        if (time() <= $timestamp && $timestamp < $fiveMinute) {
            return true;
        }

        return false;

    }

}
