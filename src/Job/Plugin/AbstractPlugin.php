<?php
namespace LittleBot\Job\Plugin;

abstract class AbstractPlugin implements PlugingInterface
{
    protected $bot;

    public function __construct()
    {
        $this->bot = $this->setBot();
    }
}