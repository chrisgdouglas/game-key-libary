<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');
parse_str($_SERVER['QUERY_STRING']); //$id
$isAdmin = getCurrentUser($db, $_SESSION['user_id'], TRUE);

if ($isAdmin && $_POST['formAction'] !== "editUser") {
	switch ($_POST['formAction']) {

		// Add User Flow
		case "addUser":
			$uuid = UUID::v4();;
		  $sql = "INSERT INTO users (id, display_name, password, email, user_role) VALUES (:uuid, :display_name, :hashed_password, :email, :user_role)";
		  try {
				$db->beginTransaction();
				$statement = $db->prepare($sql);
				$statement->bindParam(':uuid', $uuid, PDO::PARAM_STR, 37);
				$statement->bindParam(':display_name', $_POST['display_name'], PDO::PARAM_STR, 255);
				$statement->bindParam(':hashed_password', password_hash($_POST['password'], PASSWORD_DEFAULT), PDO::PARAM_STR, 255);
				$statement->bindParam(':email', $_POST['email'], PDO::PARAM_STR, 512);
				$statement->bindParam(':user_role', $_POST['user_role'], PDO::PARAM_INT, 1);
				$statement->execute();
				$db->commit();
			} catch (Exception $e) {
				$db->rollback();
				$action_message = "errorUpdate";
			}
			break;

		// Delete User Flow
		case "deleteUser":
		  $sql = "DELETE FROM users WHERE email = :email";
		  try {
				$db->beginTransaction();
				$statement = $db->prepare($sql);
				$statement->bindParam(':email', $_POST['user_list'], PDO::PARAM_STR, 512);
				$statement->execute();
				$db->commit();
			} catch (Exception $e) {
				$db->rollback();
				$action_message = "errorUpdate";
			}
			break;
	}
}

// Edit User Flow
if ($_POST['formAction'] == "editUser") {
	if (array_key_exists('password', $_POST) && strlen($_POST['password']) > 0 && $_POST['password'] === $_POST['password_verify']) {
		$has_password = TRUE;
	}
	if (array_key_exists('user_role', $_POST) && strlen($_POST['user_role']) > 0) {
		$has_role = TRUE;
	}
	if (array_key_exists('game_key_privacy', $_POST)) {
		$game_key_privacy = 1;
		$_SESSION['game_key_privacy'] = 1;
	}
	else {
		$game_key_privacy = 0;
		$_SESSION['game_key_privacy'] = 0;
	}
	$sql = "UPDATE users SET display_name = :new_display_name, email = :new_email, game_key_privacy = :game_key_privacy";
  if ($has_password) {
  	$sql = $sql . ", password = :hashed_password";
  }
  if ($has_role) {
  	$sql = $sql . ", user_role = :user_role";
  }
  $sql = $sql . " WHERE email = :old_email";

  try {
		$db->beginTransaction();
		$statement = $db->prepare($sql);
		$statement->bindParam(':new_display_name', $_POST['new_display_name'], PDO::PARAM_STR, 255);
		$statement->bindParam(':new_email', $_POST['new_email'], PDO::PARAM_STR, 512);
		$statement->bindParam(':game_key_privacy', $game_key_privacy, PDO::PARAM_BOOL , 1);
		if ($has_password) {
			$statement->bindParam(':hashed_password', password_hash($_POST['password'], PASSWORD_DEFAULT), PDO::PARAM_STR, 255);
		}
		if ($isAdmin && $has_role) {
			$statement->bindParam(':user_role', $_POST['user_role'], PDO::PARAM_INT, 1);
		}
		$statement->bindParam(':old_email', $_POST['old_email'], PDO::PARAM_STR, 512);
		$statement->execute();
		$db->commit();
	} catch (Exception $e) {
		$db->rollback();
		$action_message = "errorUpdate";
	}
}

if (!isset($action_message)) {
	$action_message = "successUpdate";
}

$url = "/user_details.php?id=" . $_POST['id'] . '&actionMsg=' . $action_message;
header("Location: $url");