<?php
session_start();
if (!$_SESSION['logged_id']) {
	$url = "user_login.php";
	header("Location: $url");
}

/* Start DB Config
Modify DB information & credentials to match your system config
 */
define(DB_USERNAME, "games");
define(DB_PASSWORD, "yourpassword");  // update with your DB's password
define(DSN, "mysql:dbname=games;host=localhost");  // dbname assumed to games; update as required.
/* End DB Config */

define(GAMES_PATH,getcwd());
define(HTDOC_PATH, "/var/www"); // modify to match your system config

require_once GAMES_PATH . '/include/db.inc.php';
require_once GAMES_PATH . '/include/functions.php';
