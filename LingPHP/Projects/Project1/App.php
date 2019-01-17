<?php
require_once dirname(dirname(__DIR__)) . '/App/BaseApp.php';

use Projects\Project1\Test;

function main()
{
    $instance = new Test();
    $instance->getConfig();
}

$processConfig = array();
global $command;
switch ($command) {
    case "a":
        $processConfig = array(
            "workFunction" => "main"
            , "daemonize" => false,
        );
        break;
    case "b":
        $processConfig = array(
            "workFunction" => "main"
            , "workerNumber" => 3
            , "daemonize" => true
            , "loopTimespan" => 2,
        );
        break;
    default:
        echo "Nothing to do!\n";
}

appStart($processConfig);
