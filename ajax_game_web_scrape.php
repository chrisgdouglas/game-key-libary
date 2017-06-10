<?php
require_once '/var/www/games/include/simple_html_dom.php';
$games_path = getcwd();
$db_username=  "username"; // update with your DB's username
$db_password=  "yourpassword%357"; // update with your DB's password
$dsn= "mysql:dbname=games;host=localhost";

$db = getDBConnect($dsn,$db_username,$db_password);

$html = new simple_html_dom();
$ageFlag = FALSE; // if Steam's age check is in place, the expected page data will not be there, error out.
$tag_limit = 4; // limits the number of genre tags stored.

if (strpos($_REQUEST['steam_store_url'], 'store.steampowered.com') !== FALSE) {
	$html->load_file($_REQUEST['steam_store_url']);
	foreach($html->find('h2') as $element) {
	  if ($element->plaintext == "Please enter your birth date to continue:") {
	  	$ageFlag = TRUE;
			$return_values = array(
				"display_message" => "errorGetWebData-agegate"
			);
	  	break;
	  }
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
	$game_name = str_replace("’", "'", $game_name_raw); // replace &raquo; with standard apostrophe for url escaping
	$image_description = $game_name . " Header";
	$image_server_src = $html->find('img.game_header_image_full',0)->src;
	$store_id = $html->find('div[data-appid]', -1)->attr['data-appid'];

// get the game's header image, copy it to local server
		$file_name = "gameimage-" . UUID::v4() . ".jpg";
		$destination = $games_path . "/images/" . $file_name;
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
	$sql = "INSERT INTO images (description, file_path)
	VALUES (:description, :file_path)";
	try {
		$statement = $db->prepare($sql);
		$statement->bindParam(':description', $image_description, PDO::PARAM_STR, 255);
		$statement->bindParam(':file_path', $file_path, PDO::PARAM_STR, 728);
		$statement->execute();
	} catch (PDOException $e) {
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

function getDBConnect($dsn, $db_username, $db_password) {
	try {
	    $dbh = new PDO($dsn, $db_username, $db_password);
	} catch (PDOException $e) {
	    echo "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	return $dbh;
}