<?php

function initEnv()
{
    //项目根目录
    define("ROOT_PATH", dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
    //框架目录
    define("FRAME_PATH", dirname(dirname(__FILE__)));
    //环境变量前缀
    // define("ENV_PREFIX", "LING_");

    $envPath = FRAME_PATH . "/.env";
    if (is_file($envPath)) {
        $env = parse_ini_file($envPath, true);

        foreach ($env as $key => $val) {
            // $name = ENV_PREFIX . strtoupper($key);
            $name = strtoupper($key);

            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $name . '_' . strtoupper($k);
                    putenv("$item=$v");
                }
            } else {
                putenv("$name=$val");
            }
        }
    }
}

initEnv();
