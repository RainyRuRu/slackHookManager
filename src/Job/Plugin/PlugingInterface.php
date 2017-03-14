<?php
/**
 * Created by PhpStorm.
 * User: rainy
 * Date: 2017/3/14
 * Time: 下午12:03
 */

namespace LittleBot\Job\Plugin;


interface PlugingInterface
{
    public function __construct();
    public function run();
    public function setBot();
}