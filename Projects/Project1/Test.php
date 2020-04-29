<?php
namespace Projects\Project1;

use App\Config;
use TestPlugin\Hello;

class Test
{
    public function getConfig()
    {
        echo Config::getCommonConfig("a");
        echo "\n";
        echo Config::getLocalConfig("a");
        echo "\n";
        (new Hello())->sayHello();
    }
}
