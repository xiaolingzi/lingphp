<?php

class AppException
{
	public function LogException($ex)
	{
	    $exceptionMessage = strval($ex);
	    $exceptionMessage = date("Y-m-d H:i:s")."\n".$exceptionMessage."\n\n";
	    
	    $filePath=__DIR__."/logs";
	    if(!file_exists($filePath))
	    {
	        mkdir($filePath,0777,true);
	    }
	    $fileName=$filePath."/".date("Ymd",time()).".txt";
	    $fp=fopen($fileName, "a");
	    fwrite($fp, $exceptionMessage);
	    fclose($fp);
	    throw $ex;
	}
}

set_exception_handler(array("AppException","LogException"));