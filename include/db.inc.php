<?php

/* Start DB Config
Modify DB information & credentials to match your system config
*/
define(DB_USERNAME, "games");
-define(DB_PASSWORD, "yourpassword");  // update with your DB's password
-define(DSN,"mysql:dbname=games;host=localhost");  // dbname assumed to games; update as required.
/* End DB Config */

function getDBConnect($dsn, $db_username, $db_password) {
	try {
	    $dbh = new PDO($dsn, $db_username, $db_password);
	} catch (PDOException $e) {
	    echo "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	return $dbh;
}

function dbGetRows($dbh, $sql) { // all rows returned, static query
	$statement = $dbh->prepare($sql);
	$statement->execute();
	$rs_return = $statement->fetchAll();
	$statement->closeCursor();
	return $rs_return;
}

function getOne($dbh, $sql) { // return one result, static query
	$sql = $sql . " LIMIT 1";
	$statement = $dbh->prepare($sql);
	$statement->execute();
	$rs_return = $statement->fetch();
	$statement->closeCursor();
	return $rs_return;
}

function closeDBConnection($dbh, $statement=null) {
	if ($statement !== null) {
		$statement->closeCursor();
	}
	$dbh = null;
}

