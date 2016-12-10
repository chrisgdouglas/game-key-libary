<?php

require_once getcwd() . '/games.config.php';
parse_str($_SERVER['QUERY_STRING']); //game_name, genre, status
$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$sql = "SELECT game_name, id, purchase_date, store, redeemed, played FROM games";

if (isset($game_name)) {
  str_replace("&amp;", "&", $game_name);
	$search_term = $game_name;
	$sql = $sql . " WHERE game_name LIKE CONCAT('%',:game_name,'%')";
}
if (isset($genre)) {
	$search_term = $genre;
	$sql = $sql . " WHERE popular_tags LIKE CONCAT('%',:genre,'%')";
}
if (isset($status) && $status != "Played" && $status != "NotPlayed") {
  $search_term = $status;
  $sql = $sql . " WHERE redeemed = :status";
}
if (isset($status) && $status == "Played") {
  $search_term = "Played";
  $sql = $sql . " WHERE played = 1";
}
if (isset($status) && $status == "NotPlayed") {
  $search_term = "Not Played";
  $sql = $sql . " WHERE played = 0";
}
if (isset($_POST['search'])) {
  $search_term = ltrim($_POST['search']);
  $sql = $sql . " WHERE CONCAT_WS('', game_name, game_key, notes, store, popular_tags) LIKE CONCAT('%',:search,'%')";
}

$sql = $sql . " ORDER BY purchase_date DESC, game_name ASC";

$statement = $db->prepare($sql);

if (isset($game_name)) {
	$statement->bindParam(':game_name', $game_name, PDO::PARAM_STR, 255);
}
if (isset($genre)) {
	$statement->bindParam(':genre', $genre, PDO::PARAM_STR, 255);
}
if (isset($status) && $status !== "Played" && $status !== "NotPlayed") {
  $statement->bindParam(':status', $status, PDO::PARAM_STR, 10);
}
if (isset($_POST['search'])) {
  $statement->bindParam(':search', ltrim($_POST['search']), PDO::PARAM_STR);
}

$statement->execute();
// var_dump($statement->errorInfo());
// var_dump($sql);
$game_list_rs = $statement->fetchAll();
$number_of_rows = $statement->rowCount();
closeDBConnection($db, $statement);
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
    <link href="/games/css/flatpickr.min.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/games/">Game Key Manager</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="user_details.php?id=<?php echo $_SESSION['user_id'] ?>">Profile</a></li>
            <li><a href="user_logout.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a></li>
          </ul>
          <form class="navbar-form navbar-right" name="searchForm" id="searchFormid" action="/games/game_search.php" method="post">
            <div class="input-group dropdown">
              <input type="text" class="form-control" id="searchField" name="search" placeholder="Search..." data-toggle="dropdown" />
              <span id="searchButton" class="input-group-addon pointer"><i class="glyphicon glyphicon-search"></i></span>
                <ul class="dropdown-menu hidden dropdown-menu-right col-xs-12" aria-labelledby="searchField" id="dropDownParent">
                </ul>
             </div>
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid">

      <div class="row">
        <div class="jumbotron text-center">
          <h1>Search Results</h1>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
        	<div class="table-responsive">
            <table class="table table-striped sortable">
              <caption><?php echo $number_of_rows . " results for search term '" . $search_term . "'" ?></caption>
              <thead>
                <tr>
                  <th data-defaultsort="disabled"></th>
                  <th class="pointer" nowrap data-mainsort="1" data-firstsort="desc">Game Name&nbsp;<span class="glyphicon glyphicon-sort small" aria-hidden="true"></span></th>
                  <th data-defaultsort='disabled'></th>
                  <th class="pointer" nowrap data-dateformat="YYYY-MM-DD">Purchase Date&nbsp;<span class="glyphicon glyphicon-sort small" aria-hidden="true"></span></th>
                  <th data-defaultsort="disabled"></th>
                  <th class="pointer" nowrap>Store&nbsp;<span class="glyphicon glyphicon-sort small" aria-hidden="true"></span></th>
                  <th data-defaultsort="disabled"></th>
                  <th class="pointer" nowrap>Redeemed&nbsp;<span class="glyphicon glyphicon-sort small" aria-hidden="true"></span></th>
                  <th data-defaultsort="disabled"></th>
                  <th class="pointer" nowrap>Played&nbsp;<span class="glyphicon glyphicon-sort small" aria-hidden="true"></span></th>
                </tr>
              </thead>
              <?php
                foreach($game_list_rs as $games) {
                  echo "<tr>";
                    echo buildTableContentRow($games['game_name'], $games['id']);
                    echo buildTableContentRow($games['purchase_date']);
                    echo buildTableContentRow($games['store']);
                    echo buildTableContentRow($games['redeemed']);
                    echo buildTableContentRow($games['played'] ? 'Yes' : 'No');
                  echo "</tr>";
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/moment.min.js"></script>
    <script src="/games/js/bootstrap-sortable.js"></script>
    <script src="/games/js/games_functions.js"></script>
  </body>
</html>