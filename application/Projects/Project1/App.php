<?php
require_once dirname(dirname(__DIR__)).'/APP/BaseApp.php';

use Projects\Project1\Test;

global $command;

$instance = new Test();
switch ($command)
{
	case "a":
	    $instance->getConfig();
	    break;
	default:
	    echo "Nothing to do!\n";
}