<?php

require_once getcwd() . '/games.config.php';

parse_str($_SERVER['QUERY_STRING']); //$id
$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$sql = "SELECT g.game_name, g.game_owner, g.purchase_date, g.store, g.game_key, g.redeemed, g.cost, g.purchase_currency, g.played, g.distribution_platform, g.store_id, g.notes, g.image, g.popular_tags, images.file_path FROM games AS g LEFT JOIN images ON g.image = images.description WHERE id = :id";

$statement = $db->prepare($sql);
$statement->bindParam(':id', $id, PDO::PARAM_STR, 37);
$statement->execute();
$game_detail_rs = $statement->fetch();

$sql = "SELECT platform FROM distplatform_lkup ORDER BY platform ASC";
$distplatform_rs = dbGetRows($db, $sql);

$sql = "SELECT value FROM redemption_lkup ORDER BY value ASC";
$redemption_rs = dbGetRows($db, $sql);

$sql = "SELECT store_name FROM store_lkup ORDER BY store_name ASC";
$store_rs = dbGetRows($db, $sql);

$sql = "SELECT description FROM images";
$images_rs = dbGetRows($db, $sql);

$sql = "SELECT id, display_name FROM users WHERE user_role <> '0' ORDER BY display_name ASC"; // don't grab disabled users
$users_rs = dbGetRows($db, $sql);

$sql = "SELECT currency FROM exchange_rate ORDER BY currency ASC";
$currency_rs = dbGetRows($db, $sql);
closeDBConnection($db, $statement);

require_once getcwd() . '/include/global_nav_inc.html';

?>

    <div class="container-fluid">
      <div class="row">
        <div class="jumbotron text-center">
          <?php
            if (sizeof($game_detail_rs['file_path']) === 0) {
              echo "<h1>" . $game_detail_rs['game_name'] . "</h1>";
            } else {
              echo "<p><img src='" . $game_detail_rs['file_path']. "' /></p>";
            }
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-danger hidden" id="errorFormSubmission">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Errors in form submission. Please verify the fields highlighted in red below.
            </div>
          </div>
          <form action="/games/game_edit_processing.php" method="POST" name="editGame" onsubmit="return validateForm(this)">
          <input hidden name="id" value="<?php echo $id; ?>">
          <div class="col-xs-6">
            <div class="form-group">
              <label for="gamename">Game Name</label>
              <input type="text" class="form-control" placeholder="Game Name" id="gamename" name="game_name" value="<?php echo $game_detail_rs['game_name']; ?>">
            </div>
            <div class="form-group">
              <label for="gameowner">Game Owner</label>
              <select name="game_owner" id="gameowner" class="form-control">
              <?php
              foreach($users_rs as $user) {
                echo buildSelectOption($user['display_name'], $user['id'], $game_detail_rs['game_owner']);
              }
              echo $game_detail_rs['game_owner'];
              ?>
              </select>
            </div>
            <div class="form-group">
              <label for="storepurchased">Store Purchased</label>
              <select name="store" id="storepurchased" class="form-control">
              <?php
              foreach($store_rs as $store) {
                echo buildSelectOption($store['store_name'], $store['store_name'], $game_detail_rs['store']);
              }
              ?>
              </select>
            </div>
            <div class="form-group">
              <label for="redeemedid">Redeemed</label>
              <select name="redeemed" id="redeemedid" class="form-control">
              <?php
              foreach($redemption_rs as $redemption) {
                echo buildSelectOption($redemption['value'], $redemption['value'], $game_detail_rs['redeemed']);
              }
              ?>
              </select>
            </div>
            <div class="form-group">
              <label for="playedid">Played</label>
              <select name="played" id="playedid" class="form-control">
                <option value="0" <?php echo $game_detail_rs['played'] == 0 ? 'selected' : ''; ?>>No</option>
                <option value="1" <?php echo $game_detail_rs['played'] == 1 ? 'selected' : ''; ?>>Yes</option>
              </select>
            </div>
            <div class="form-group">
              <label for="distributionplatform">Distribution Platform</label>
              <select name="distribution_platform" id="distributionplatform" class="form-control">
              <?php
              foreach($distplatform_rs as $distplatform) {
                echo buildSelectOption($distplatform['platform'], $distplatform['platform'], $game_detail_rs['distribution_platform']);
              }
              ?>
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label for="costid">Game Genre/Tags</label>
              <input type="text" class="form-control" id="populartags" name="popular_tags" placeholder="Game Genre/Tags" value="<?php echo $game_detail_rs['popular_tags'] ?>" />
            </div>
            <div class="form-group">
              <label for="gamekey">Game Key</label>
              <input type="<?php echo ($_SESSION['game_key_privacy'] === 0) ? 'text' : 'password'; ?>" class="form-control" id="gamekey" name="game_key" placeholder="Game Key" value="<?php echo $game_detail_rs['game_key'] ?>">
            </div>
            <div class="form-group">
              <label for="purchasedate">Purchase Date</label>
              <div class="input-group flatpickr">
                <input type="text" class="form-control" id="purchasedate" name="purchase_date" placeholder="Click for calendar..." value="<?php echo $game_detail_rs['purchase_date']; ?>" title="Click for calendar..." data-input />
                <span class="input-group-addon" id="basic-addon1" data-toggle><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></span>
              </div>
            </div>
            <div class="form-group">
              <label for="costid">Cost</label>
              <input type="text" class="form-control" id="costid" name="cost" placeholder="Game Cost" value="<?php echo number_format(floatval($game_detail_rs['cost']) , 2); ?>">
            </div>
            <div class="form-group">
              <label for="purchasecurrency">Purchase Currency</label>
              <select name="purchase_currency" id="purchasecurrency" class="form-control">
              <?php
              foreach($currency_rs as $currency) {
                echo buildSelectOption($currency['currency'], $currency['currency'], $game_detail_rs['purchase_currency']);
              }
              ?>
              </select>
            </div>
            <div class="form-group">
              <label for="storeid">Store ID</label>
              <input type="text" class="form-control" id="storeid" name="store_id" placeholder="Store ID" value="<?php echo $game_detail_rs['store_id']; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="imageid">Image</label>
            <select name="image" id="imageid" class="form-control">
            <?php
            echo buildSelectOption("No Image", "No Image", "No Image");
            foreach($images_rs as $image) {
              echo buildSelectOption($image['description'], $image['description'], $game_detail_rs['image']);
            }
            ?>
            </select>
          </div>
          <div class="form-group">
            <label for="notesid">Notes</label>
            <textarea class="form-control" rows="3" id="notesid" name="notes"><?php echo $game_detail_rs['notes']; ?></textarea>
          </div>
          <div class="form-group">
            <div class="btn-group btn-group pull-right" role="group">
              <button type="button" class="btn btn-default" id="backbutton">Cancel</button>
              <button type="submit" class="btn btn-primary" id="submitForm">Save Edits</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>

    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/flatpickr.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>
      $( document ).ready(function() {
        flatpickr(".flatpickr", {
          wrap: true,
          clickOpens: true
        });
        $('#backbutton').bind('click', function() {history.go(-1);});
        $(".panel").bind('click', function() {$(this).addClass('hidden');});
      });

      function validateForm(subForm) {
        clearValidationErrors();
        var violations = new Array();

        if (subForm.game_name.value.length <= 1) {
          violations.push(subForm.game_name.id);
        }
        if (subForm.game_key.value.length <= 1) {
          violations.push(subForm.game_key.id);
        }
        if (subForm.store.options[subForm.store.selectedIndex].value.length <= 1) {
          violations.push(subForm.store.id);
        }
        if (subForm.redeemed.options[subForm.redeemed.selectedIndex].value.length <= 1) {
          violations.push(subForm.redeemed.id);
        }
        if (subForm.played.options[subForm.played.selectedIndex].value == "") {
          violations.push(subForm.played.id);
        }
        if (subForm.distribution_platform.options[subForm.distribution_platform.selectedIndex].value.length <= 1) {
          violations.push(subForm.distribution_platform.id);
        }
        if (subForm.purchase_currency.options[subForm.purchase_currency.selectedIndex].value.length <= 1) {
          violations.push(subForm.purchase_currency.id);
        }

        if (violations.length > 0) {
          showValidationErrors(violations);
          $('#errorFormSubmission').removeClass('hidden');
          return false;
        }
        else {
          return true;
        }
      }

    </script>
  </body>
</html>
