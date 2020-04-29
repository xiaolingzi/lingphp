<?php

class Composer
{
    public static function load()
    {
        $dir = dirname(__DIR__);
        $filename = $dir . DIRECTORY_SEPARATOR . "vendor/autoload.php";

        if (file_exists($filename)) {
            require_once $filename;
        }
    }
}

Composer::load();
