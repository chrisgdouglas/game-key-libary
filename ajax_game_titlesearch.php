<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');

$sql = "SELECT game_name AS name FROM games WHERE game_name LIKE CONCAT('%',:game_name,'%')";
$statement = $db->prepare($sql);
$statement->bindParam(':game_name', $_POST['game_name'], PDO::PARAM_STR, 255);
$statement->execute();
$return_rs = $statement->fetchAll();

echo json_encode($return_rs);