<?php
require_once getcwd() . '/games.config.php';
if (isset($_GET)) {
  $id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
  $game_name = array_key_exists('game_name', $_GET) ? str_replace("%20", " ", $_GET['game_name']) : null;
  $actionMsg = array_key_exists('actionMsg', $_GET) ? $_GET['actionMsg'] : null;
}
else {
  $id = null;
}

if ($id !== null || $game_name !== null) {
  $db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

   $sql = "SELECT g.id, g.game_name, g.purchase_date, g.store, g.game_key, g.redeemed, g.cost, g.played, g.distribution_platform, g.notes, g.store_id, g.popular_tags, exchange_rate.currency_symbol, images.file_path FROM games AS g LEFT JOIN images ON g.image = images.description LEFT JOIN exchange_rate ON g.purchase_currency = exchange_rate.currency";
  if ($id) {
    $sql = $sql . " WHERE g.id = :id";
    $statement = $db->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_STR, 37);
  }
  if ($game_name) {
    $sql = $sql . " WHERE g.game_name = :game_name";
    $statement = $db->prepare($sql);
    $statement->bindParam(':game_name', $game_name, PDO::PARAM_STR, 255);
  }
  $statement->execute();
  // var_dump($statement->errorInfo());
  // var_dump($sql);
  $game_detail_rs = $statement->fetch();

  $id = is_null($id) ? $game_detail_rs['id'] : $id;

  $sql = "SELECT external_url FROM external_urls WHERE external_site = 'Steam'";
  $statement = $db->prepare($sql);
  $statement->execute();
  $steam_link_rs = $statement->fetch();
  $steamLink = $steam_link_rs['external_url'] . $game_detail_rs['store_id'];

  $sql = "SELECT external_url FROM external_urls WHERE external_site = 'SteamDB'";
  $statement = $db->prepare($sql);
  $statement->execute();

  $steamDBLink_rs = $statement->fetch();
  $steamDBLink = $steamDBLink_rs['external_url'] . $game_detail_rs['store_id'];

  $sql = "SELECT external_url FROM external_urls WHERE external_site = 'Metacritic'";
  $statement = $db->prepare($sql);
  $statement->execute();

  $metacriticLink_rs = $statement->fetch();
  $metacriticLink = str_replace("?", strtolower($game_detail_rs['game_name']), $metacriticLink_rs['external_url']);

  closeDBConnection($db, $statement);
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
          <?php
            if (sizeof($game_detail_rs['file_path']) === 0) {
              echo "<h1>" . $game_detail_rs['game_name'] . "</h1>";
            } else {
              echo "<p><img class='img-responsive center-block' src='" . $game_detail_rs['file_path']. "' /></p>";
            }
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-success hidden" id="succesEdit">
            <div class="panel-heading">
              <h3 class="panel-title">
                Success!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Game updated successfully.
            </div>
          </div>
          <div class="panel panel-success hidden" id="succesAdd">
            <div class="panel-heading">
              <h3 class="panel-title">
                Success!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Game added successfully.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorDisplay">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Something went wrong while updating or adding a game.
            </div>
          </div>
          <table class="table table-condensed">
          <?php
            if ($game_detail_rs !== FALSE) {
            echo buildTableContent('Game Name: ', $game_detail_rs['game_name']);
            echo buildTableContent('Genre Tags: ', $game_detail_rs['popular_tags']);
            echo buildTableContent('Purchase Date: ', $game_detail_rs['purchase_date']);
            echo buildTableContent('Store: ', $game_detail_rs['store']);
            echo buildTableContent('Game Key: ', $game_detail_rs['game_key']);
            echo buildTableContent('Cost: ', ($game_detail_rs['cost'] == 0 ? 'Free' : $game_detail_rs['currency_symbol'] . $game_detail_rs['cost']));
            echo buildTableContent('Redeemed: ', $game_detail_rs['redeemed']);
            echo buildTableContent('Played: ', ($game_detail_rs['played'] ? 'Yes' : 'No'));
            echo buildTableContent('Distribution Platform: ', $game_detail_rs['distribution_platform']);
            echo buildTableContent('Notes: ', $game_detail_rs['notes']);
            }
            else {
              echo "<caption>No game data found.</caption>";
            }
          ?>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-1">
          <div class="pull-left" role="group">
            <form name="deleteGame" action="/games/game_delete_game_processing.php" method="post" onsubmit="return confirm('Delete game?');">
              <input type="hidden" name="id" value="<?php echo $id ?>" />
              <button class="btn btn-danger" role="button" type="submit">Delete Game</button>
            </form>
          </div>
        </div>
        <div class="col-xs-11">
          <div class="btn-group btn-group pull-right" role="group">
            <a class="btn btn-default" href="/games/" role="button">Back</a>
            <a class="btn btn-primary" href="game_edit.php?id=<?php echo $id ?>">Edit Game</a></button>
          </div>
        </div>
      </div>
    </div>
    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>
      // add link buttons to "Game Name" field
      var steamLink = "<?php echo $steamLink ?>";
      var SteamDBLink = "<?php echo $steamDBLink ?>";
      var metacriticLink = "<?php echo $metacriticLink ?>";

      if (!steamLink.endsWith("/") && !steamLink.endsWith("/")) {
        var htmlString = "&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + steamLink +"'>View on Steam</a>&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + SteamDBLink +"'>View on SteamDB</a>&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + metacriticLink +"'>Search on Metacritic</a>";
        $("body > div > div:nth-child(2) > div > table > tbody > tr:nth-child(1) > td:nth-child(2)").append(htmlString);
      }

      //add links to search page with genre input within the "Genre Tags" field
      var rawData = "<?php echo $game_detail_rs['popular_tags'] ?>";
      var genreTags = rawData.split(",");
      var genreHTML = new Array();
      for (i=0; genreTags.length>i; i++) {
        if (i!==0) {
          space = "&nbsp;";
        } else {
          space = "";
        }
        genreHTML[i] = space + "<a href='game_search.php?genre=" + genreTags[i].trim() + "'>" + genreTags[i].trim() + "</a>";
      }
      $("body > div > div:nth-child(2) > div > table > tbody > tr:nth-child(2) > td:nth-child(2)").html(genreHTML.join());
    </script>
  </body>
</html>
<?php
