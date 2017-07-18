<?php

class ClassLoader
{
    private static $_namespaceArr = array();
	public static function defaultLoader($className)
	{
	    //获取根目录
	    $dir=dirname(__DIR__);
	    
	    $className=str_replace("\\", "/", $className);
	    $className=trim($className,"/");
	    
	    $classNameArr=explode("/", $className);
	    $namespace = $classNameArr[0];
	    
	    if(array_key_exists($namespace, self::$_namespaceArr))
	    {
	    	$dir=self::$_namespaceArr[$namespace];
	    }
	    
		$filename=$dir."/".$className.".php";
		
	    if(is_file($filename))
		{
		  require_once $filename;
		}
	}
	
	public static function registNamespace($namespace,$filePath)
	{
	    if(!empty($namespace) && !empty($filePath))
	    {
            self::$_namespaceArr[$namespace]=$filePath;
	    }
	}
}

spl_autoload_register(array('ClassLoader', 'defaultLoader'));

ClassLoader::registNamespace("LingORM", dirname(__DIR__)."/Plugins");
ClassLoader::registNamespace("Thrift", dirname(__DIR__)."/Plugins");

