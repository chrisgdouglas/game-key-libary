<?php

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
	if ($rs_return === FALSE || empty($rs_return)) {
		return false;
	}
	else {
		return $rs_return;
	}
}

function getOne($dbh, $sql) { // return one result, static query
	$sql = $sql . " LIMIT 1";
	$statement = $dbh->prepare($sql);
	$statement->execute();
	$rs_return = $statement->fetch();
	$statement->closeCursor();
	if ($rs_return === FALSE || empty($rs_return)) {
		return false;
	}
	else {
		return $rs_return[0];
	}
}

function closeDBConnection($dbh, $statement=null) {
	if ($statement !== null) {
		$statement->closeCursor();
	}
	$dbh = null;
}

