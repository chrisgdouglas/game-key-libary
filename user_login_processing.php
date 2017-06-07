<?php
require_once getcwd() . '/include/db.inc.php';
require_once getcwd() . '/include/functions.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');

$sql = "SELECT id, password, game_key_privacy FROM users WHERE email= :email";
$statement = $db->prepare($sql);
$statement->bindParam(':email', $_POST['email_address'], PDO::PARAM_STR, 512);
$statement->execute();
$user_data_rs = $statement->fetch();

if ($user_data_rs) {
	if (password_verify($_POST['password'], $user_data_rs['password'])) {
		session_start();
		$_SESSION['logged_id'] = "1";
		$_SESSION['user_id'] = $user_data_rs['id'];
		$_SESSION['game_key_privacy'] = (int)$user_data_rs['game_key_privacy'];
		$return_value = "success";
	} else {
		$return_value = "error";
	}
} else {
	$return_value = "error";
}

closeDBConnection($db, $statement);
$url = "/games/user_login.php?result=" . $return_value;
header("Location: $url");
