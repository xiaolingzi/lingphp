<?php
namespace LingORM;

class Config
{
    const DEFAULT_DATABASE_SERVER = "haviea";

    public static function getDatabaseConfigPath()
    {
        return dirname(dirname(ROOT_PATH)) . '/config/' . ENVIRONMENT . '/database.json';
    }
}
