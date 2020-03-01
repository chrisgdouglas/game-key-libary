<?php
require_once getcwd() . '/games.config.php';
if (isset($_GET)) {
  $id = array_key_exists('id', $_GET) ? safe($_GET['id']) : null;
  $game_name = array_key_exists('game_name', $_GET) ? str_replace("%20", " ", safe($_GET['game_name'])) : null;
  $actionMsg = array_key_exists('actionMsg', $_GET) ? safe($_GET['actionMsg']) : null;
}
else {
  $id = null;
}

if ($id !== null && strlen($id) === 0) {
  $id = null;
  $no_game = true;
}
else if ($game_name !== null && strlen($game_name) === 0) {
  $game_name = null;
  $no_game = true;
}
else {
  $no_game = false;
}

if ($no_game === false) {
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
  $game_detail_rs = $statement->fetch();
  if ($game_detail_rs === false || empty($game_detail_rs)) {
    $no_game = true;
  }
  else {
    $id = is_null($id) ? $game_detail_rs['id'] : $id;
  }

  $sql = "SELECT external_site, external_url FROM external_urls WHERE external_site = 'Steam' OR external_site = 'SteamDB' OR external_site = 'Metacritic'";
  foreach ($db->query($sql) as $row) {
  	switch ($row['external_site']) {
  		case 'Steam':
  			$steamLink = $row['external_url'] . $game_detail_rs['store_id'];
  			break;
  		case 'SteamDB':
  			$steamDBLink = $row['external_url'] . $game_detail_rs['store_id'];
  			break;
  		case 'Metacritic':
  			$game_name = str_replace("’", "'", $game_detail_rs['game_name']); // replace any games names that contain &raquo with ' for proper html escaping
  			$metacriticLink = str_replace("?", rawurlencode(strtolower($game_name)), $row['external_url']);
  			break;
  	}
  }

  closeDBConnection($db, $statement);
}

require_once getcwd() . '/include/global_nav_inc.html';

?>

    <div class="container-fluid">
      <div class="row">
        <div class="jumbotron text-center">
          <?php
            $file_name_tmp = GAMES_PATH . $game_detail_rs['file_path'];
            if (strlen($game_detail_rs['file_path']) === 0 || !file_exists($file_name_tmp)) {
              echo "<h1>" . $game_detail_rs['game_name'] . "</h1>";
            } else {
              echo "<p><img class='img-responsive center-block' src='" . $game_detail_rs['file_path']. "' width='460' height='215' alt='" . $game_detail_rs['game_name'] . "' title='". $game_detail_rs['game_name'] ."' /></p>";
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
            if ($no_game === false) {
              echo buildTableContent('Game Name: ', htmlspecialchars($game_detail_rs['game_name'], ENT_SUBSTITUTE), "game_name");
              echo buildTableContent('Genre Tags: ', $game_detail_rs['popular_tags'], "popular_tags");
              echo buildTableContent('Purchase Date: ', $game_detail_rs['purchase_date']);
              echo buildTableContent('Store: ', $game_detail_rs['store']);
              echo buildTableContent('Game Key: ', ($_SESSION['game_key_privacy'] === 0 ? $game_detail_rs['game_key'] : "*****-*****-*****"), "game_key");
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
            <form name="deleteGame" action="/game_delete_game_processing.php" method="post" onsubmit="return confirm('Delete game?');">
              <input type="hidden" name="id" value="<?php echo $id ?>" />
              <?php
              if ($no_game === false) {
                echo '<button class="btn btn-danger" role="button" type="submit">Delete Game</button>';
              } ?>
            </form>
          </div>
        </div>
        <div class="col-xs-11">
          <div class="btn-group btn-group pull-right" role="group">
            <a class="btn btn-default" href="/" role="button">Back</a>
            <?php
            if ($no_game === false) {
              echo '<a class="btn btn-primary" href="game_edit.php?id=' . $id . '">Edit Game</a>';
            } ?>
          </div>
        </div>
      </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/games_functions.js"></script>
    <script>
      // add link buttons to "Game Name" field
      var steamLink = "<?php echo $steamLink ?>";
      var SteamDBLink = "<?php echo $steamDBLink ?>";
      var metacriticLink = "<?php echo $metacriticLink ?>";

      if (!steamLink.endsWith("/") && !steamLink.endsWith("/")) {
        var htmlString = "&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + steamLink +"'>View on Steam</a>&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + SteamDBLink +"'>View on SteamDB</a>&nbsp;<a class='btn btn-primary btn-xs' target='_blank' href='" + metacriticLink +"'>Search on Metacritic</a>";
        $("#game_name").append(htmlString);
      }

      //add links to search page with genre input within the "Genre Tags" field
      var rawData = "<?php echo $game_detail_rs['popular_tags'] ?>";
      var genreTags = rawData.split(", ");
      var genreHTML = new Array();
      for (i=0; genreTags.length>i; i++) {
        if (i!==0) {
          space = "&nbsp;";
        } else {
          space = "";
        }
        genreHREF = escape(genreTags[i])
        genreHTML[i] = space + "<a href='game_search.php?genre=" + genreHREF.trim() + "'>" + genreTags[i].trim() + "</a>";
      }
      $("#popular_tags").html(genreHTML.join());

      var key_val = $("#game_key").html();
      if (key_val === "*****-*****-*****") {
        $("#game_key").html("");
        var newHTML = '<span title="Click to reveal Game Key.">' + key_val + '</span>';
        $("#game_key").append(newHTML);
        $("#game_key span").addClass('game_key_link');
      }

      $("#game_key").bind('click', function() {
        var key_val = $("#game_key span").html();
        if (key_val !== "*****-*****-*****") {
          var newHTML = "*****-*****-*****";
          $("#game_key span").html(newHTML);
          $("#game_key span").attr('title', 'Click to reveal Game Key.');
        } else {
          var serializedData = "id=<?php echo $game_detail_rs['id'] ?>";

          request = $.ajax({
              url: "/ajax_game_get_key.php",
              type: "post",
              data: serializedData
          });

          request.done(function (response) {
              $("#game_key span").html(JSON.parse(response));
              $("#game_key span").attr('title', 'Click to hide Game Key.');
          });
        }
      });
    </script>
  </body>
</html>
<?php
