<?php
namespace Projects\Project1;

use Lib\Utility\IO\ConfigHandler;

class Test
{
	public function getConfig()
	{
		echo ConfigHandler::getCommonConfigs("a");
		echo "\n";
		echo ConfigHandler::getLocalConfigs("a");
	}
}