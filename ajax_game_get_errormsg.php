<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');
$sql = "SELECT display_message FROM action_messages WHERE id = :id AND page_name = :page_name";
$statement = $db->prepare($sql);
$statement->bindParam(':id', $_POST['id'], PDO::PARAM_INT, 2);
$statement->bindParam(':page_name', $_POST['page_name'], PDO::PARAM_STR, 50);
$statement->execute();
$return_rs = $statement->fetchAll();
closeDBConnection($db, $statement);

echo json_encode($return_rs[0]['display_message']);
