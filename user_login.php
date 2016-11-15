<?php

session_start();
if (isset($_SESSION['logged_id'])) {
	$url = "/games/";
	header("Location: $url");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="icon" href="../../favicon.ico"> -->

    <title>Games Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="/games/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/games/css/dashboard.css" rel="stylesheet">
  </head>
  	<div class="container-flud">
  		<div class="row">
  			<div class="col-xs-12 col-md-6 col-md-push-3 center-block">
	        <div class="panel panel-primary" id="loginpanl">
	          <div class="panel-heading">
	            <h3 class="panel-title">
	              Game Key Manager Login
	            </h3>
	          </div>
	          <div class="panel-body">
	          	<div id="error" class="alert alert-danger hidden" role="alert">Invalid Username or Password.</div>
              <div id="success" class="alert alert-success hidden" role="alert">Logged in successfully.</div>
              <div id="logout" class="alert alert-success hidden" role="alert">Logged out successfully.</div>
	            <form name="user_login" id="userlogin" method="POST" action="user_login_processing.php">
			          <div class="form-group">
			            <label for="gamename">Email Address</label>
			            <input type="text" class="form-control" placeholder="email@domain.com" id="username" name="email_address"">
			          </div>
			          <div class="form-group">
			            <label for="gamename">Password</label>
			            <div class="input-group">
			            	<input type="password" class="form-control" id="passwordid" name="password">
			            	<span id="pwd_toggle" class="input-group-addon glyphicon glyphicon-eye-close" aria-hidden="true" title="Show/Hide password"></span>
			            </div>
			          </div>
			          <button type="submit" class="btn btn-primary pull-right">Login</button>
	            </form>
	          </div>
	        </div>
  			</div>
  		</div>
  	</div>
  	<script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>
    	$( document ).ready(function() {
    		$("#pwd_toggle").bind("click", function() {
    			var pwd_field = $("#passwordid");
    			var pwd_toggle = $("#pwd_toggle");
    			if (pwd_field.attr("type") == "text") {
    				pwd_field.attr("type", "password");
    				pwd_toggle.removeClass("glyphicon-eye-open");
    				pwd_toggle.addClass("glyphicon-eye-close");
    			} else {
    				pwd_field.attr("type", "text");
    				pwd_toggle.addClass("glyphicon-eye-open");
    				pwd_toggle.removeClass("glyphicon-eye-close");
    			}
    		});
    		if (location.search) {
    			locsearchArray = location.search.split("=");
    			document.getElementById(locsearchArray[1]).classList.remove("hidden");
    		}
    	});
    </script>
  <body>
  </body>
 </html>