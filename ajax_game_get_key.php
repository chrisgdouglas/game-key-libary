<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');

$sql = "SELECT game_key FROM games WHERE id = :id";
$statement = $db->prepare($sql);
$statement->bindParam(':id', $_POST['id'], PDO::PARAM_STR, 255);
$statement->execute();
$return_rs = $statement->fetchAll();

echo json_encode($return_rs[0]['game_key']);