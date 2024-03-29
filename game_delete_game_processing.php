<?php

require_once getcwd() . '/games.config.php';
$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');

$sql = "SELECT game_owner FROM games WHERE game_owner = :user_id AND id = :game_id";
$statement = $db->prepare($sql);
$statement->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR, 37);
$statement->bindParam(':game_id', $_POST['id'], PDO::PARAM_STR, 37);
$statement->execute();
$owner_match = $statement->fetch();

if ($owner_match !== FALSE) {
	$sql = "DELETE FROM games WHERE id = :id";
	try {
		$db->beginTransaction();
		$statement = $db->prepare($sql);
		$statement->bindParam(':id', $_POST['id'], PDO::PARAM_STR, 37);
		$statement->execute();
		$db->commit();
	} catch (Exception $e) {
		$db->rollback();
		$action_message = "errorDeleteGame";
	}
	if (!isset($action_message)) {
			$action_message = "successDeleteGame";
	}
}
else {
	$action_message = "errorDeleteGame";
}
closeDBConnection($db, $statement);
$url = "/?actionMsg=" . $action_message;
header("Location: $url");
