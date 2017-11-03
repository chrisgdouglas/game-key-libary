<?php
require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');
$id = UUID::v4();
$sql = "INSERT INTO games (id, game_name, popular_tags, game_owner, purchase_date, store, game_key, redeemed, cost, purchase_currency, played, distribution_platform, store_id, image, notes)
VALUES (:id, :game_name, :popular_tags, :game_owner, :purchase_date, :store, :game_key, :redeemed, :cost, :purchase_currency, :played, :distribution_platform, :store_id, :image, :notes)";

try {
	$statement = $db->prepare($sql);
	$statement->bindParam(':id', $id, PDO::PARAM_STR, 37);
	$statement->bindParam(':game_name', $_POST['game_name'], PDO::PARAM_STR, 255);
	$statement->bindParam(':popular_tags', $_POST['popular_tags'], PDO::PARAM_STR, 1024);
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
	$statement->bindParam(':notes', $_POST['notes'], PDO::PARAM_STR);
	$statement->execute();
	var_dump($statement->errorInfo());
} catch (PDOException $e) {
	$action_message = "errorDisplay";
}
closeDBConnection($db, $statement);
if (!isset($action_message)) {
		$action_message = "succesAdd";
}
$url = "/games/game_details.php?id=" . $id . "&actionMsg=" . $action_message;
header("Location: $url");
