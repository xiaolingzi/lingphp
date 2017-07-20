<?php
namespace Lib\Utility\IO;

class ConfigHandler
{
	/**
     * 读取公共配置
     * @param string $key
     * @return Ambigous <>
     */
	static public function getCommonConfigs($key)
	{
		$filename=dirname(dirname(ROOT_PATH)).'/config/'.ENVIRONMENT.'/common_config.json';
		$result=self::getArrayFromJsonFile($filename);
		return $result[$key];
	}
	
	/**
	 * 读取项目配置
	 * @param string $key
	 * @return Ambigous <>
	 */
	static public function getLocalConfigs($key)
	{
	    $filename=ROOT_PATH.'/config/'.ENVIRONMENT.'/common_config.json';
	    $result=self::getArrayFromJsonFile($filename);
	    return $result[$key];
	}
	
	static public function getArrayFromJsonFile($filename)
    {
        $content=self::getContentFromFile($filename);
        if(!empty($content))
        {   
            return json_decode($content,true);
        }
        return array();
    }
	
	static public function getContentFromFile($filename)
    {
        if(! file_exists($filename))
        {
            return null;
        }
        $content = file_get_contents($filename);
        return $content;
    }
	
}