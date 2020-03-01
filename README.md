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
if (!$_SESSION['logged_id']) {
    $url = "user_login.php";
    header("Location: $url");
}

/* Start DB Config
Modify DB information & credentials to match your system config
 */
define('DB_USERNAME', "your_db_username"); // update with your DB's username
define('DB_PASSWORD', "your_db_password");  // update with your DB's password
define('DSN', "mysql:dbname=games;host=localhost");  // dbname assumed to games; update as required.
/* End DB Config */

define('GAMES_PATH', getcwd());
define('HTDOC_PATH', "/www/htdocs");////modify to match your system config

$current_script_name = basename($_SERVER['PHP_SELF']);
require_once GAMES_PATH . '/include/db.inc.php';
if ($current_script_name !== "ajax_game_web_scrape.php") {
    require_once GAMES_PATH . '/include/functions.php';
}
```
* Example images included, create directory "images" and unzip images.zip to that destination
* SQL directory contains two scripts to create the database tables, and insert example data

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact