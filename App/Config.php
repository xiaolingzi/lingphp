<?php
namespace App;

class Config
{
    /**
     * 读取公共配置
     * @param string $key
     * @return Ambigous <>
     */
    public static function getCommonConfig($key)
    {
        $filename = FRAME_PATH . '/config/' . self::env("APP_ENV") . '/common.json';
        $result = self::getArrayFromJsonFile($filename);
        return $result[$key];
    }

    /**
     * 读取项目配置
     * @param string $key
     * @return Ambigous <>
     */
    public static function getLocalConfig($key)
    {
        $filename = ROOT_PATH . '/config/' . self::env("APP_ENV") . '/common.json';
        $result = self::getArrayFromJsonFile($filename);
        return $result[$key];
    }

    public static function getArrayFromJsonFile($filename)
    {
        $content = self::getContentFromFile($filename);
        if (!empty($content)) {
            return json_decode($content, true);
        }
        return array();
    }

    public static function getContentFromFile($filename)
    {
        if (!file_exists($filename)) {
            return null;
        }
        $content = file_get_contents($filename);
        return $content;
    }

    public static function env($key){
        // $name = ENV_PREFIX . strtoupper($key);
        $name = strtoupper($key);
        return getenv($name);
    }

}
