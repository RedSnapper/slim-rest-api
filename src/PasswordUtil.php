<?php
/**
 * Created by PhpStorm.
 * User: akis
 * Date: 02/04/15
 * Time: 11:54
 */

namespace Src;


class PasswordUtil {


   private $userName;
   private $password;


	public function __construct($userName,$password)
	{
		$this->userName = $userName;
		$this->password = $password;
	}

	//This is just to insert passwords in the db for testing purposes , not used in the script
	function generatePassword()
	{
		$options = array( 'cost' => 10,
		                  'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM) );
		$hash = password_hash($this->password,PASSWORD_BCRYPT,$options);

		$sql = "INSERT INTO pwds (username,pwd) VALUES (:username, :pwd)";
		try {
			$db = Dbase::getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("username", $this->userName);
			$stmt->bindParam("pwd", $hash);

			$stmt->execute();

		} catch(PDOException $e) {
			//error_log($e->getMessage(), 3, '/var/tmp/php.log');
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}

	function retrievePassword()
	{
		$sql = "SELECT username,pwd FROM pwds WHERE username =  :username  LIMIT 1";
		try {
			$db = Dbase::getConnection();
			$stmt = $db->prepare($sql);

			$stmt->bindParam("username", $this->userName);
			$stmt->execute();
			$users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 0){return false;}
			else{

				$hash = $users[0]['pwd'];
				return $hash;
			}
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}

	function verifyPassword() {

		return password_verify($this->password,$this->retrievePassword($this->userName)) ;
	}


} 