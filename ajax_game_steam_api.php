<?php
require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
$page_request = $_REQUEST['steam_store_url'];

if (strpos($page_request, 'store.steampowered.com') !== FALSE) {
    preg_match('/[0-9]{6}/', $page_request, $match);
    if (empty($match)) {
        $app_id = FALSE;
    }
    else {
        $app_id = $match[0];
    }
}
else {
    $app_id = FALSE;
}

if ($app_id === FALSE) { // something wrong with the URL; don't continue and return error.
	$return_values = array(
		"display_message" => "errorGetWebData-url"
	);
}
if (!isset($return_values)) {
    $gameObj = curl_api_get($app_id);
    if ($gameObj->$app_id->success === true) {
        $game_name = $gameObj->$app_id->data->name;
        $popular_tags = [];
        foreach ($gameObj->$app_id->data->genres as $genre) {
            array_push($popular_tags, $genre->description);
        }
        $image_server_src = $gameObj->{$app_id}->data->header_image;
        $image_background_src = $gameObj->$app_id->data->background;
        $store_id = $app_id;
        $image_description = $game_name . " Header";

        // get the game's header image, copy it to local server
        $file_name = "gameimage-" . UUID::v4() . ".jpg";
        $destination = GAMES_PATH . "/games/images/" . $file_name;
        if (!copy($image_server_src, $destination)) {
            $return_values = array(
                "display_message" => "errorGetWebData-getimage"
            );
        }
        else {
            chmod($destination, 0775);
            $file_path = "/games/images/" . $file_name;
        }
        // Update the database with image data
        $sql = "INSERT INTO images (description, file_path, owner)
        VALUES (:description, :file_path, :owner)";
        try {
            $db->beginTransaction();
            $statement = $db->prepare($sql);
            $statement->bindParam(':description', $image_description, PDO::PARAM_STR, 255);
            $statement->bindParam(':file_path', $file_path, PDO::PARAM_STR, 728);
            $statement->bindParam(':owner', $_SESSION['user_id'], PDO::PARAM_STR, 37);
            $statement->execute();
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            $return_values = array(
                "display_message" => "errorGetWebData-dbimage"
            );
        }
        $statement->closeCursor();
        $db = null;
        // build array of returned values
        $return_values = array(
            "popular_tags" => implode(', ', $popular_tags),
            "game_name" => $game_name,
            "description" => $image_description,
            "store_id" => $store_id,
            "game_background_img" => $image_background_src,
            "display_message" => "successGetWebData"
        );
    }
    else {
        $return_values = array(
            "display_message" => "errorGetWebData-url"
        );
    }
}
//Something has gone horribly wrong.
else {
	if (!isset($return_values)) { // no other errors have accrued.
		$return_values = array(
			"display_message" => "errorGetWebData-dom"
		);
	}
}
echo json_encode($return_values);
?>