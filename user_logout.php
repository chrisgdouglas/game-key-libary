<?php

if (session_status() == PHP_SESSION_ACTIVE) {
	session_destroy();
}
$return_value = "logout";
$url = "/user_login.php?result=" . $return_value;
header("Location: $url");