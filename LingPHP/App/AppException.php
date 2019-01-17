<?php

class AppException
{
    public static function LogException($errno, $errstr, $errfile, $errline)
    {
        $errTypeName = "Notice";
        if ($errno == E_ERROR) {
            $errTypeName = "Error";
        } else if ($errno == E_WARNING) {
            $errTypeName = "Warning";
        }
        $exceptionMessage = $errTypeName . ":" . $errstr . "\n in " . $errfile . " on " . $errline;
        $exceptionMessage = date("Y-m-d H:i:s") . "\n" . $exceptionMessage . "\n\n";

        $filePath = __DIR__ . "/logs";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileName = $filePath . "/" . date("Ymd", time()) . ".txt";
        $fp = fopen($fileName, "a");
        fwrite($fp, $exceptionMessage);
        fclose($fp);
        echo $exceptionMessage;
    }
}

set_error_handler(array("AppException", "LogException"));
