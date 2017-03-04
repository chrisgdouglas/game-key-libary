<?php
require_once getcwd() . '/games.config.php';
$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$sql = "SELECT g.id, g.game_name, g.redeemed, g.played, g.notes, g.popular_tags, images.file_path FROM games AS g LEFT JOIN images ON g.image = images.description ORDER BY g.purchase_date DESC, g.game_name ASC LIMIT 12";
$statement = $db->prepare($sql);
$statement->execute();
$game_list_rs = $statement->fetchAll();

$sql = "SELECT COUNT(id) as count FROM games WHERE redeemed = 'Yes'";
$redeemed_count = getOne($db, $sql);
$sql = "SELECT COUNT(id) as count FROM games WHERE redeemed = 'No'";
$not_redeemed_count = getOne($db, $sql);
$sql = "SELECT COUNT(id) as count FROM games WHERE redeemed = 'Gifted'";
$gifted_count = getOne($db, $sql);
$sql = "SELECT COUNT(id) as count FROM games WHERE played = 1";
$played_count = getOne($db, $sql);
$sql = "SELECT ROUND(SUM(cost),2) as sum FROM games";
$total_cost = getOne($db, $sql);
$sql = "SELECT COUNT(id) count FROM games";
$number_of_rows = getOne($db, $sql);
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
          <a class="navbar-brand" href="#">Game Key Manager</a>
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
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a></li>
            <li><a href="game_add.php">Add Game</a></li>
            <li><a href="game_image_manage.php">Manage Images</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Overview</h1>
          <div class="row">
            <div class="col-xs-12">
              <div class="panel panel-success hidden" id="succesImage">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    Success!
                    <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
                  </h3>
                </div>
                <div class="panel-body">
                  Image operation completed successfully.
                </div>
              </div>
              <div class="panel panel-danger hidden" id="errorImage">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    Error!
                    <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
                  </h3>
                </div>
                <div class="panel-body">
                  Something went wrong while during the the image operation.
                </div>
              </div>
              <div class="panel panel-success hidden" id="successDeleteGame">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    Success!
                    <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
                  </h3>
                </div>
                <div class="panel-body">
                  Game Deleted Successfully
                </div>
              </div>
              <div class="panel panel-danger hidden" id="errorDeleteGame">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    Error!
                    <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
                  </h3>
                </div>
                <div class="panel-body">
                  Something went wrong while deleting the game.
                </div>
              </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">Redemption Stats</h3>
                </div>
                <div class="panel-body">
                  <ul class="list-group">
                    <li class="list-group-item"><a href="game_search.php?status=Yes">Redeemed:</a> <?php echo $redeemed_count['count']; ?></li>
                    <li class="list-group-item"><a href="game_search.php?status=No">Not Redeemed:</a> <?php echo $not_redeemed_count['count']; ?></li>
                    <li class="list-group-item"><a href="game_search.php?status=Gifted">Gifted:</a><?php echo $gifted_count['count']; ?></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">Total Cost</h3>
                </div>
                <div class="panel-body">
                  $<?php echo $total_cost['sum'] ?>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">Games Played</h3>
                </div>
                <div class="panel-body">
                  <a href="game_search.php?status=Played"><?php echo $played_count['count'] . '</a> of <a href="game_search.php?status=NotPlayed">' . $number_of_rows['count'] . "</a>"; ?>
                </div>
              </div>
            </div>
          </div>

          <h2 class="sub-header">Recently Added Games</h2>
          <div class="row">
            <?php
            foreach($game_list_rs as $games) {
            ?>
            <div class="col-md-4 cards">
                <div class="thumbnail">
                    <img class="img-responsive center-block" src="<?php echo $games['file_path']; ?>" />
                    <div class="caption">
                      <h4 class="cardTitle"><?php echo $games['game_name']; ?></h4>

                      <p class="cardGenre">Genre: <?php echo $games['popular_tags']; ?></p>
                      <p>Redeemed: <?php echo $games['redeemed'];  ?></p>
                      <p>Played: <?php echo $games['played'] ? 'Yes' : 'No'; ?></p>

                      <a href="game_edit.php?id=<?php echo $games['id']; ?>" title="Edit Game" class="btn btn-default btn-xs pull-right" role="button"><i class="glyphicon glyphicon-edit"></i></a>
                      <a href="game_details.php?id=<?php echo $games['id']; ?>" class="btn btn-default btn-xs" role="button">More Info</a>
                    </div>
                </div>
            </div>
            <?php } ?>
          </div>
          <div class="pull-right"><a class="btn btn-primary" href="game_search.php">View All Games</a></button>
        </div>
      </div>
    </div>
    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/moment.min.js"></script>
    <script src="/games/js/bootstrap-sortable.js"></script>
    <script src="/games/js/jQuery.succinct.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>
      $( document ).ready(function() {
        $(function(){
            $('.cardTitle').succinct({
                size: 33
            });
        });
        $(function(){
            $('.cardGenre').succinct({
                size: 40
            });
        });
      });
    </script>
  </body>
</html>
