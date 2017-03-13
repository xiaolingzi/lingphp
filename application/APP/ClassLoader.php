<?php
//如有第三方插件的自动加载类，也在这里引用执行即可

class ClassLoader
{
	public static function defaultLoader($className)
	{
	    //获取根目录
	    $dir=dirname(__DIR__);
	    //将命名空间按斜杠切割，然后再拼装成路径
	    $classNameArr=explode("\\", $className);
	    $count=count($classNameArr);
	    for($i=0;$i<$count;$i++)
	    {
	    	$dir.="/".$classNameArr[$i];
	    	
	    }
		$filename=$dir.".php";
		
		if(is_file($filename))
		{
		  require_once $filename;
		}
	}
}

spl_autoload_register(array('ClassLoader', 'defaultLoader'));
