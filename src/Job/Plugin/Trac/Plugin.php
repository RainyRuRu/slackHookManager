<?php
namespace LittleBot\Job\Plugin\Trac;

use LittleBot\Job\Plugin\AbstractPlugin;
use LittleBot\Trac\Ticket;
use LittleBot\Job\SlackHook;
use LittleBot\Data\DataManager;
use LittleBot\Config;

class Plugin extends AbstractPlugin
{
    protected $bot;
    protected $ticket;

    public function __construct()
    {
        parent::__construct();
        $this->ticket = new Ticket();
    }

    public function run()
    {
        $time_interval = Config::get("time_interval");
        $start_timestamp = strtotime("-{$time_interval} minutes");
        $trac_ids = $this->getRecentTracIds($start_timestamp);
        foreach ($trac_ids as $id) {
            $this->sendTracMessage($id, $start_timestamp);
        }
    }

    public function setBot()
    {
        $bot_info = DataManager::getBotInfo(2);
        return (new SlackHook())
            ->setBotInfo($bot_info)
            ->setChannel("@rainycheng");
    }


    private function getRecentTracIds($timestamp)
    {
        $own_ticket = $this->ticket->fetchTicketByOwner();
        $cc_ticket = $this->ticket->fetchTicketByCC();
        $recent_ticket = $this->ticket->getRecentChange($timestamp);

        $filter_ticket = array_intersect(array_merge($own_ticket, $cc_ticket), $recent_ticket);

        return $filter_ticket;
    }


    private function sendTracMessage($id, $timestamp)
    {
        $logs = $this->ticket->changeLog($id);
        $logs = array_reverse($logs);
        $ticket_info = $this->ticket->get($id);
        //$pre_messsage = "`<https://issue.kkcorp/trac/ticket/{$ticket_info[0]}|#{$ticket_info[0]}>` {$ticket_info[3]['summary']} \n";


        foreach ($logs as $log) {
            if ($this->getDate($log[0]) < gmdate("o-m-d H:i:s", $timestamp)) {
                break;
            }

            $date = $this->getDate($log[0]);
            $author = $log[1];
            $field = $log[2];
            $oldvalue = $log[3];
            $newvalue = $log[4];
            $message = "";

            $message = $this->buildTracMessage($log);
            $a = SlackHook::buildMessage($ticket_info[3]['summary'],$ticket_info[0], $date, $author, $message);
            //var_dump($a);
            $url = DataManager::getUrlById(1);
            $this->slack->send($url, $message, "@rainycheng", $a);
        }
    }


    private function buildTracMessage($log)
    {
        $fields = [
            "datetime" => 0,
            "author" => 1,
            "field" => 2,
            "oldvalue" => 3,
            "newvalue" => 4,
            "parmanent" => 5
        ];

        $date = $this->getDate($log[0]);
        $author = $log[1];
        $field = $log[2];
        $oldvalue = $log[3];
        $newvalue = $log[4];
        $message = "";

        switch ($field) {
            case "cc":
                $message = "CC 你 :interrobang:";
                break;
            case "owner":
                if ("rainycheng" === $newvalue) {
                    $message = "把票給你！！ :hecrycry:";
                } else {
                    $message = "票到 {$newvalue} 手上了！";
                }
                break;
            case "comment":
                $message = "新留言 :memo: \n  {$newvalue} ";
                break;
            case "status":
                if ("fixed" === $newvalue) {
                    $message = "關票了！:white_check_mark:";
                } else {
                    $message = "{$newvalue}";
                }
                break;
            case "description":
                $message = "{$author} 改了內容，要來看看嗎？ :mag_right:";
                break;
            default:
                $message = "{$fields} {$newvalue}";
                break;
        }

        return $message;

    }


    private function getDate($time_array)
    {

        $date = $time_array['__jsonclass__'][1];
        return str_replace("T", " ", $date);
    }
}