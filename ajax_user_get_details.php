<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');
$isAdmin = getCurrentUser($db, $_SESSION['user_id'], TRUE);

if ($isAdmin) {
	$sql = "SELECT display_name, email, user_role FROM users where email = :email";
  $statement = $db->prepare($sql);
  $statement->bindParam(':email', $_POST['user_list'], PDO::PARAM_STR, 255);
  $statement->execute();
  $return_rs = $statement->fetch();
	// $return_rs['display_message'] = "successDetails";
} else {
		$return_rs['display_message'] = "errorUpdate";
}

closeDBConnection($db, $statement);
echo json_encode($return_rs);