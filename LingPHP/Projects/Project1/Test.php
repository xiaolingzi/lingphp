<?php
namespace Projects\Project1;

use Lib\Utility\IO\ConfigHandler;

class Test
{
    public function getConfig()
    {
        echo ConfigHandler::getCommonConfig("a");
        echo "\n";
        echo ConfigHandler::getLocalConfig("a");
    }
}
