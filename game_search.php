<?php

require_once getcwd() . '/games.config.php';
if (isset($_GET)) {
  $game_name = array_key_exists('game_name', $_GET) ? safe($_GET['game_name']) : null;
  $genre = array_key_exists('genre', $_GET) ? safe($_GET['genre']) : null;
  $status = array_key_exists('status', $_GET) ? safe($_GET['status']) : null;
  $search = array_key_exists('search', $_POST) ? ltrim(safe($_POST['search'])) : null;
}
else {
  $id = null;
}

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
$search_term = "";

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
if (isset($status) && $status !== "Played" && $status !== "NotPlayed") {
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
if (isset($search)) {
  $search_term = $search;
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
if (isset($search)) {
  $statement->bindParam(':search', $search, PDO::PARAM_STR);
}

$statement->execute();
$game_list_rs = $statement->fetchAll();
$number_of_rows = $statement->rowCount();
closeDBConnection($db, $statement);

require_once getcwd() . '/include/global_nav_inc.html';

?>

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
                    echo buildTableContentRow(htmlspecialchars($games['game_name'], ENT_SUBSTITUTE), $games['id']);
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
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/bootstrap-sortable.js"></script>
    <script src="/js/games_functions.js"></script>
  </body>
</html>