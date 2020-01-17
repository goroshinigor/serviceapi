<?php

namespace App\Infrastructure\Services\Legacy;

use PDO;

class AtomApiDb
{
    private static $db = null;
    private static $dbParams = [];

    private function __construct()
    {

    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::r(), $method), $args);
    }

    private static function setDB()
    {
        self::$dbParams = [
            'dbname' => $_ENV['SERVICE_API_DB_NAME'],
            'username' => $_ENV['SERVICE_API_DB_USERNAME'],
            'password' => $_ENV['SERVICE_API_DB_PASSWORD'],
            'hostname' => $_ENV['SERVICE_API_DB_HOSTNAME'],
            'sqlchar' => $_ENV['SERVICE_API_DB_SQLCHAR'],
        ];
    }

    public static function r()
    {
        if (is_null(self::$db)) {
            try {
                self::setDB();
                self::$db = new PDO("mysql:host=" . self::$dbParams['hostname']
                    . ";dbname=" . self::$dbParams['dbname'], self::$dbParams['username'], self::$dbParams['password']);
                self::$db->query('SET character_set_connection = ' . self::$dbParams['sqlchar']);
                self::$db->query('SET character_set_client = ' . self::$dbParams['sqlchar']);
                self::$db->query('SET character_set_results = ' . self::$dbParams['sqlchar']);
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
        return self::$db;
    }


    private function __clone()
    {
    }

}
