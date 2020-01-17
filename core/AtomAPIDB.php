<?php

class AtomAPIDB
{
	// рабочая
	private static $dbname = "serviceapi";
	private static $username = "u_serviceapi";
	private static $password = "yg8uBcqY";
	private static $hostname = "localhost";
	private static $sqlchar = "utf8";
	private static $db = null;

	private function __construct(){}
	private function __clone(){}

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function r(){
		if(is_null(self::$db)){
			try {
				self::$db = new PDO("mysql:host=".self::$hostname.";dbname=".self::$dbname,self::$username,self::$password);
				self::$db->query('SET character_set_connection = '.self::$sqlchar);
				self::$db->query('SET character_set_client = '.self::$sqlchar);
				self::$db->query('SET character_set_results = '.self::$sqlchar);
			}
			catch(PDOException $e){
				die("Error: ".$e->getMessage());
			}
		}
		return self::$db;
	}

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function __callStatic($method,$args){
 		return call_user_func_array(array(self::r(), $method), $args);
	}

}

?>