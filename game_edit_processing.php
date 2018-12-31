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
	$sql = "
	UPDATE games
	SET
	  game_name = :game_name,
	  game_owner = :game_owner,
	  purchase_date = :purchase_date,
	  store = :store,
	  game_key = :game_key,
	  redeemed = :redeemed,
	  cost = :cost,
	  purchase_currency = :purchase_currency,
	  played = :played,
	  distribution_platform = :distribution_platform,
	  store_id = :store_id,
	  image = :image,
	  popular_tags = :popular_tags,
	  notes = :notes
	WHERE
	  id = :id";

	try {
		$db->beginTransaction();
		$statement = $db->prepare($sql);
		$statement->bindParam(':game_name', $_POST['game_name'], PDO::PARAM_STR, 255);
		$statement->bindParam(':game_owner', $_POST['game_owner'], PDO::PARAM_STR, 37);
		$statement->bindParam(':purchase_date', $_POST['purchase_date'], PDO::PARAM_STR);
		$statement->bindParam(':store', $_POST['store'], PDO::PARAM_STR, 255);
		$statement->bindParam(':game_key', $_POST['game_key'], PDO::PARAM_STR, 255);
		$statement->bindParam(':redeemed', $_POST['redeemed'], PDO::PARAM_STR, 50);
		$statement->bindParam(':cost', $_POST['cost'], PDO::PARAM_INT);
		$statement->bindParam(':purchase_currency', $_POST['purchase_currency'], PDO::PARAM_STR, 25);
		$statement->bindParam(':played', $_POST['played'], PDO::PARAM_BOOL);
		$statement->bindParam(':distribution_platform', $_POST['distribution_platform'], PDO::PARAM_STR, 255);
		$statement->bindParam(':store_id', $_POST['store_id'], PDO::PARAM_STR, 50);
		$statement->bindParam(':image', $_POST['image'], PDO::PARAM_STR, 255);
		$statement->bindParam(':popular_tags', $_POST['popular_tags'], PDO::PARAM_STR, 1024);
		$statement->bindParam(':notes', $_POST['notes'], PDO::PARAM_STR);
		$statement->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
		$statement->execute();
		$db->commit();
	} catch (Exception $e) {
		$db->rollback();
		$action_message = "errorDisplay";
	}
	closeDBConnection($db, $statement);
	if (!isset($action_message)) {
			$action_message = "succesEdit";
	}
}
else {
	$action_message = "actionMsg=errorDisplay";
}
$url = "/games/game_details.php?id=" . $_POST['id'] . '&actionMsg=' . $action_message;
header("Location: $url");
