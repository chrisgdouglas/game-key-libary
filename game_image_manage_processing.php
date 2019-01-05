<?php
require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
array_walk($_POST,'wsafe');

if ($_POST['addSelected'] === "1") {
	if (!file_exists(GAMES_PATH . "/images")) {
		if (!mkdir(GAMES_PATH . "/images/", 0755)) {
			die("Unable to create images directory");
		}
	}
	$description = $_POST['add_description'];
	if (strlen($_POST['add_file_by_url']) > 0 && strpos($_POST['add_file_by_url'], 'http') !== FALSE) {
		$source = $_POST['add_file_by_url'];
		$file_name = "gameimage-" . UUID::v4() . ".jpg";
		$destination = GAMES_PATH . "/images/" . $file_name;
		if (!copy($source, $destination)) {
			$action_message = "errorImage";
		}
		else {
			chmod($destination, 0775);
			$file_path = "/games/images/" . $file_name;
		}
	}
	elseif (strlen($_FILES['add_file_by_upload']['name'] && getimagesize($_FILES['add_file_by_upload']['tmp_name']) !== FALSE) > 0) { // file exists, and it's an image
		$imageFileType = pathinfo($_FILES['add_file_by_upload']['name'],PATHINFO_EXTENSION); // get extension
		$file_name = "gameimage-" . UUID::v4() . "." . $imageFileType;
		$uploadfile = GAMES_PATH . "/images/" . $file_name;
		if (move_uploaded_file($_FILES['add_file_by_upload']['tmp_name'], $uploadfile)) {
			$file_path = "/games/images/" . $file_name;
			chmod($uploadfile, 0755);
		} else {
			die($_FILES['add_file_by_upload']['error']);
		}
	}
	else {
		$action_message = "errorImage";
	}
	if (isset($file_path)) {
		$sql = "INSERT INTO images (description, file_path, owner)
		VALUES (:description, :file_path, :owner)";
		try {
			$db->beginTransaction();
			$statement = $db->prepare($sql);
			$statement->bindParam(':description', $description, PDO::PARAM_STR, 255);
			$statement->bindParam(':file_path', $file_path, PDO::PARAM_STR, 728);
			$statement->bindParam(':owner', $_SESSION['user_id'], PDO::PARAM_STR, 37);
			$statement->execute();
			$db->commit();
		} catch (Exception $e) {
			$db->rollback();
			$action_message = "errorImage";
		}
	}
}

if ($_POST['editSelected'] == 1) {
	$sql = "UPDATE images
		SET
			description = :new_description,
			file_path = :new_file_path
		WHERE 
			description = :original_description";
	$original_description = $_POST['edittedImage'];
	$new_file_path = $_POST['edit_file_path'];
	$new_description = $_POST['edit_description'];
	$old_file_name = HTDOC_PATH . $_POST['edittedImagePathid'];
	$new_file_name = HTDOC_PATH . $_POST['edit_file_path'];
	rename($old_file_name, $new_file_name);
	// chmod($new_file_name, 0765);
	try {
		$db->beginTransaction();
		$statement = $db->prepare($sql);
		$statement->bindParam(':new_description', $new_description, PDO::PARAM_STR, 255);
		$statement->bindParam(':new_file_path', $new_file_path, PDO::PARAM_STR, 728);
		$statement->bindParam(':original_description', $original_description, PDO::PARAM_STR, 255);
		$statement->execute();
		$db->commit();
	} catch (Exception $e) {
		$db->rollback();
		$action_message = "errorImage";
	}
}

if ($_POST['deleteSelected'] === "1") {
	$isAdmin = getCurrentUser($db, $_SESSION['user_id'], TRUE);
	$sql = "SELECT owner FROM images where file_path = :file_path_to_delete";
	$statement = $db->prepare($sql);
	$statement->bindParam(':file_path_to_delete', $_POST['deleteimage'], PDO::PARAM_STR, 728);
	$statement->execute();
	$rs_return = $statement->fetch();
	$statement->closeCursor();
	if ($statement !== false && $rs_return[0] === $_SESSION['user_id']) {
		$isOwner = true;
	} else {
		$isOwner = false;
	}
	if ($isAdmin || $isOwner) {
		$system_file_name = HTDOC_PATH . $_POST['deleteimage'];
		unlink($system_file_name);
		$sql = "DELETE FROM images WHERE file_path = :file_path_to_delete";
		try {
			$db->beginTransaction();
			$statement = $db->prepare($sql);
			$statement->bindParam(':file_path_to_delete', $_POST['deleteimage'], PDO::PARAM_STR, 728);
			$statement->execute();
			$db->commit();
		} catch (Exception $e) {
			$db->rollback();
			$action_message = "errorImage";
		}
	} else {
		$action_message = "errorImage";
	}
}
if (!isset($action_message)) {
	$action_message = "succesImage";
	closeDBConnection($db, $statement);
}
$url = "/games/?actionMsg=" . $action_message;
header("Location: $url");