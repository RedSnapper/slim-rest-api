<?php
/**
 * Created by PhpStorm.
 * User: akis
 * Date: 02/04/15
 * Time: 10:55
 */

namespace Src;

require_once __DIR__."/config.php";
class Dbase {

	private static  $connection ;



	public static function getConnection()
	{
		if((self::$connection === null))
		{
			try{
				$dbConnection = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME."", DB_USER, DB_PASS);
				$dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				self::$connection = $dbConnection;

			}catch (\Exception $e)
			{
				echo $e->getMessage();
				exit;
			}
		}
		return self::$connection;
	}
} 