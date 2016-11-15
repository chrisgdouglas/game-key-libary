<?php

session_start();
session_destroy();
$return_value = "logout";
$url = "/games/user_login.php?result=" . $return_value;
header("Location: $url");