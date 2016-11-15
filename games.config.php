<?php
session_start();
if (!$_SESSION['logged_id']) {
	$url = "user_login.php";
	header("Location: $url");
}

define(GAMES_PATH,getcwd());
define(HTDOC_PATH, "/var/www"); // modify to match your system config

require_once GAMES_PATH . '/include/db.inc.php';
require_once GAMES_PATH . '/include/functions.php';
