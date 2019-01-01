<?php
require_once getcwd() . '/games.config.php';
require_once GAMES_PATH . '/include/simple_html_dom.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$html = new simple_html_dom();
$ageFlag = FALSE; // if Steam's age check is in place, the expected page data will not be there, error out.
$tag_limit = 4; // limits the number of genre tags stored.

if (strpos($_REQUEST['steam_store_url'], 'store.steampowered.com') !== FALSE) {
	$html->load_file($_REQUEST['steam_store_url']);
	$gate_text = $html->find('div#app_agegate');
	if (count($gate_text) >= 1) {
		$ageFlag = TRUE;
		$return_values = array(
			"display_message" => "errorGetWebData-agegate"
		);
		// clear out DOM to prevent memory leaks
		$html->clear();
		unset($html);
	}
}
else { // something wrong with the URL; don't continue and return error.
	$return_values = array(
		"display_message" => "errorGetWebData-url"
	);
}

if (!isset($return_values) && isset($html) && !$ageFlag) { // everything looks good, start getting data.

// get popular genre tags, cut array down to the amount set in $tag_limit
	foreach($html->find('a[class=app_tag]') as $tag) {
		$tmp = ltrim($tag->innertext);
		$tmp = rtrim($tmp);
		$item['tag'] = $tmp;
		$popular_tags[] = $item;
	}
	$popular_tags = array_splice($popular_tags, 0, $tag_limit);
	$game_name_raw = $html->find('div.apphub_AppName',-1)->plaintext;
	$game_name = str_replace("�", "'", $game_name_raw); // replace &raquo; with standard apostrophe for url escaping
	$game_name = str_replace("™", "", $game_name);
	$game_name = str_replace("®", "", $game_name);
	$game_name = mb_convert_encoding($game_name, 'UTF-8', 'UTF-8');
	$image_description = $game_name . " Header";
	$image_server_src = $html->find('img.game_header_image_full',0)->src;
	$store_id = $html->find('div[data-appid]', -1)->attr['data-appid'];

// get the game's header image, copy it to local server
		$file_name = "gameimage-" . UUID::v4() . ".jpg";
		$destination = GAMES_PATH . "/images/" . $file_name;
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
	$dbh = null;

// build array of returned values
	$return_values = array(
	 "popular_tags" => implode(', ', array_map(function ($entry) { return $entry['tag'];}, $popular_tags)),
	 "game_name" => $game_name,
	 "description" => $image_description,
	 "store_id" => $store_id,
	 "display_message" => "successGetWebData"
	);

	// clear out DOM to prevent memory leaks
	$html->clear();
	unset($html);
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

// added below as the functions include was causing namespace issues with the simple_html_dom.php code.
class UUID {
 public static function v4() {
   return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

     // 32 bits for "time_low"
     mt_rand(0, 0xffff), mt_rand(0, 0xffff),

     // 16 bits for "time_mid"
     mt_rand(0, 0xffff),

     // 16 bits for "time_hi_and_version",
     // four most significant bits holds version number 4
     mt_rand(0, 0x0fff) | 0x4000,

     // 16 bits, 8 bits for "clk_seq_hi_res",
     // 8 bits for "clk_seq_low",
     // two most significant bits holds zero and one for variant DCE1.1
     mt_rand(0, 0x3fff) | 0x8000,

     // 48 bits for "node"
     mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
   );
 }
}