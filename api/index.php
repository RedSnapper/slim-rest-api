<?php
/**
 * Created by PhpStorm.
 * User: akis
 * Date: 01/04/15
 * Time: 13:18
 */



ini_set('display_errors',1);
require __DIR__."/../vendor/autoload.php";


$db = \Src\Dbase::getConnection();

$app = new \Slim\Slim();
$app->add(new \Slim\Extras\Middleware\HttpBasicAuth());

$app->get('/users',function() use ($db){

	$sql = "SELECT user_id,firstname,lastname,job FROM snappers ORDER BY user_id";
	try {

		$stmt = $db->query($sql);
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"users": ' . json_encode($users) . '}';
	} catch(PDOException $e) {

		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}



});


$app->get('/users/search/:query',function($query) use ($db){

	$sql = "SELECT user_id,firstname,lastname,job FROM snappers WHERE UPPER(firstname) LIKE :query ORDER BY user_id";
	try {

		$stmt = $db->prepare($sql);
		$query = "%".$query."%";
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"users": ' . json_encode($users) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

});

$app->run();



?>