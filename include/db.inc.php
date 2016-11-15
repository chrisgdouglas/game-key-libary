<?php

/* Start DB Config
Modify DB information & credentials to match your system config
*/
define(DB_USERNAME, "username");  // update with your DB's username
define(DB_PASSWORD, "yourpassword");  // update with your DB's password
define(DSN,"mysql:dbname=games;host=localhost");  // dbname assumed to games; update as required.
/* End DB Config */

function getDBConnect($dsn, $db_username, $db_password) {
	try {
	    $dbh = new PDO($dsn, $db_username, $db_password);
	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
	return $dbh;
}

function dbGetRows($dbh, $sql) { // all rows returned, static query
	$statement = $dbh->prepare($sql);
	$statement->execute();
	return $statement->fetchAll();
}

function getOne($dbh, $sql) { // return one result, static query
	$sql = $sql . " LIMIT 1";
	$statement = $dbh->prepare($sql);
	$statement->execute();
	return $statement->fetch();
}

function closeDBConnection($dbh, $statement) {
	$statement->closeCursor();
	$dbh = null;
}

