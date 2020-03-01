# README #

Game Key Library

### Features ###

* Keep track of your Game Keys purchased from different vendors
* Flag keys as used, unused, or gifted
* Quickly get all relevant data for the game
* Track if you have played the game or not
* Track your gaming expenditures
* Have the application choose an un-played game for you to play next.

### How do I get set up? ###

* Clone repo to your web server or dev environment
* Create games.config.php in the root directory of the app. Contents below
```
<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
if (isset($_SESSION['logged_in']) && $_SESSION['logged_id'] === FALSE) {
	$url = "/user_login.php";
	header("Location: $url");
}

/* Start DB Config
Modify DB information & credentials to match your system config
*/
define("DB_USERNAME", "username");
define("DB_PASSWORD", "password");
define("DSN","mysql:dbname=games;host=127.0.0.1");
/* End DB Config */

define("GAMES_PATH", getcwd());
define("HTDOC_PATH", "/var/www/htdocs/"); // modify to match your system config

require_once GAMES_PATH . '/include/db.inc.php';
require_once GAMES_PATH . '/include/functions.php';

```
* Example images included, create directories "/images/games/" and unzip images.zip to that destination
* SQL directory contains two scripts to create the database tables, and insert example data
