<?php

class ClassLoader
{
    private static $_namespaceArr = array();

    public static function defaultLoader($className)
    {
        //获取根目录
        $dir = dirname(__DIR__);

        $className = str_replace("\\", "/", $className);
        $className = trim($className, "/");

        $classNameArr = explode("/", $className);
        $namespace = $classNameArr[0];

        //默认命名空间目录是从根目录算，如果有注册过，则按注册的来
        if (array_key_exists($namespace, self::$_namespaceArr)) {
            $dir = self::$_namespaceArr[$namespace];
        }

        $filename = $dir . "/" . $className . ".php";

        if (is_file($filename)) {
            require_once $filename;
        }
    }

    public static function registNamespace($namespace, $filePath)
    {
        if (!empty($namespace) && !empty($filePath)) {
            self::$_namespaceArr[$namespace] = $filePath;
        }
    }

    public static function registDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $dirPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $dir;
        $filenames = scandir($dir);

        foreach ($filenames as $filename) {
            $file = $dirPath . DIRECTORY_SEPARATOR . $filename;

            if (is_dir($file)) {
                self::registNamespace($filename, $dirPath);
            }
        }
    }
}

spl_autoload_register(array('ClassLoader', 'defaultLoader'));
ClassLoader::registDirectory("Plugins");
