<?php

require_once getcwd() . '/games.config.php';

parse_str($_SERVER['QUERY_STRING']); //$id
$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$sql = "SELECT platform FROM distplatform_lkup ORDER BY platform ASC";
$distplatform_rs = dbGetRows($db, $sql);

$sql = "SELECT value FROM redemption_lkup ORDER BY value ASC";
$redemption_rs = dbGetRows($db, $sql);

$sql = "SELECT store_name FROM store_lkup ORDER BY store_name ASC";
$store_rs = dbGetRows($db, $sql);

$sql = "SELECT description FROM images ORDER BY description ASC";
$images_rs = dbGetRows($db, $sql);

$sql = "SELECT currency FROM exchange_rate ORDER BY currency ASC";
$currency_rs = dbGetRows($db, $sql);

$user_rs = getCurrentUser($db, $_SESSION['user_id']);

$db = null;

require_once getcwd() . '/include/global_nav_inc.html';

?>

    <div class="container-fluid">

      <div class="row">
        <div class="jumbotron text-center">
          <h1>Add a New Game</h1>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-success hidden" id="successGetWebData">
            <div class="panel-heading">
              <h3 class="panel-title">
                Success!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Data scraped from Steam Store.
            </div>
          </div>
          <!-- AJAX ERRORS START -->
          <div class="panel panel-danger hidden" id="errorGetWebData-url">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Failed to scrape data from Steam Store. Invalid URL.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorGetWebData-agegate">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Failed to scrape data from Steam Store. Steam Age Check in place.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorGetWebData-dom">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Failed to scrape data from Steam Store. Invalid Page Data.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorGetWebData-getimage">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Failed to scrape data from Steam Store. Unable to retrieve header image.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorGetWebData-dbimage">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Failed to scrape data from Steam Store. Database Error saving Header Image.
            </div>
          </div>
          <!-- AJAX ERRORS END -->
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
          <div data-id="togglable-tabs">
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
              <li role="presentation" class="active">
                <a href="#add" id="add-tab" role="tab" data-toggle="tab" aria-controls="add" aria-expanded="true">Add Game</a>
              </li>
              <li role="presentation">
                <a href="#scrape" role="tab" id="scrape-tab" data-toggle="tab" aria-controls="scrape">Get Data from Steam</a>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade in active" role="tabpanel" id="add" aria-labelledby="add-tab">
                <form action="/games/game_add_processing.php" method="POST" name="addGame"  onsubmit="return validateForm(this)">
                <div class="col-xs-6">
                  <div class="form-group">
                    <label for="gamename">Game Name</label>
                    <input type="text" class="form-control" placeholder="Game Name" id="gamename" name="game_name" />
                  </div>
                  <div class="form-group">
                    <label for="gameowner">Game Owner</label>
                    <select name="game_owner" id="gameowner" class="form-control">
                    <?php
                      echo buildSelectOption($user_rs['display_name'], $user_rs['id']);
                    ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="storepurchased">Store Purchased</label>
                    <select name="store" id="storepurchased" class="form-control">
                    <?php
                      echo buildSelectOption("", "&nbsp;");
                    foreach($store_rs as $store) {
                      echo buildSelectOption($store['store_name'], $store['store_name']);
                    }
                    ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="redeemedid">Redeemed</label>
                    <select name="redeemed" id="redeemedid" class="form-control">
                    <?php
                      echo buildSelectOption("", "&nbsp;");
                    foreach($redemption_rs as $redemption) {
                      echo buildSelectOption($redemption['value'], $redemption['value']);
                    }
                    ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="playedid">Played</label>
                    <select name="played" id="playedid" class="form-control">
                      <option value="">&nbsp;</option>
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="distributionplatform">Distribution Platform</label>
                    <select name="distribution_platform" id="distributionplatform" class="form-control">
                    <?php
                      echo buildSelectOption("", "&nbsp;");
                    foreach($distplatform_rs as $distplatform) {
                      echo buildSelectOption($distplatform['platform'], $distplatform['platform']);
                    }
                    ?>
                    </select>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group">
                    <label for="costid">Game Genre/Tags</label>
                    <input type="text" class="form-control" id="populartags" name="popular_tags" placeholder="Game Genre/Tags" />
                  </div>
                  <div class="form-group">
                  <div class="form-group">
                    <label for="gamekey">Game Key</label>
                    <input type="<?php echo ($_SESSION['game_key_privacy'] === 0) ? 'text' : 'password'; ?>" class="form-control" id="gamekey" name="game_key" placeholder="Game Key" />
                  </div>
                    <label for="purchasedate">Purchase Date</label>
                    <div class="input-group flatpickr">
                      <input type="text" class="form-control" id="purchasedate" name="purchase_date" placeholder="Click for calendar..." data-input />
                      <span class="input-group-addon" id="basic-addon1" data-toggle><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="costid">Cost</label>
                    <input type="text" class="form-control" id="costid" name="cost" placeholder="Game Cost" />
                  </div>
                  <div class="form-group">
                    <label for="purchasecurrency">Purchase Currency</label>
                    <select name="purchase_currency" id="purchasecurrency" class="form-control">
                    <?php
                      echo buildSelectOption("", "&nbsp;");
                    foreach($currency_rs as $currency) {
                      echo buildSelectOption($currency['currency'], $currency['currency']);
                    }
                    ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="storeid">Store ID</label>
                    <input type="text" class="form-control" id="storeid" name="store_id" placeholder="Store ID" />
                  </div>
                </div>
                <div class="col-xs-12">
                  <div class="form-group">
                    <label for="imageid">Image</label>
                    <select name="image" id="imageid" class="form-control">
                    <?php
                      echo buildSelectOption("", "&nbsp;");
                      echo buildSelectOption("No Image", "No Image");
                    foreach($images_rs as $image) {
                      echo buildSelectOption($image['description'], $image['description']);
                    }
                    ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="notesid">Notes</label>
                    <textarea class="form-control" rows="3" id="notesid" name="notes"></textarea>
                  </div>
                  <div class="form-group">
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/games/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Add Game</button>
                    </div>
                 </div>
                </div>
                </form>
              </div>
              <div class="tab-pane fade in" role="tabpanel" id="scrape" aria-labelledby="scrape-tab">
                <br />
                <form name="scrape_web" id="scrape_web_id">
                  <div class="form-group">
                    <label for="storeid">Steam Store URL for Game</label>
                    <input type="text" class="form-control" id="steamstoreurlid" name="steam_store_url" placeholder="URL" />
                  </div>
                  <div class="form-group">
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/games/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Get Data</button>
                    </div>
                 </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        </div>
      </div>
    </div>

    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/flatpickr.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>

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
          return false;
        }
        else {
          return true;
        }
      }

      $( document ).ready(function() {
        flatpickr(".flatpickr", {
          wrap: true,
          clickOpens: true
        });

        $("#calIcon").bind('click', function() {
          let calendar = new Flatpickr(purchasedate, config);
          calendar.toggle();
        });

        $(".panel").bind('click', function() {
          $(this).addClass('hidden');
        });

      });

      $("#scrape_web_id").submit(function(e) {
        e.preventDefault();

        // setup some local variables
        var $form = $(this);

        // Let's select and cache all the fields
        var $inputs = $form.find("input");

        // Serialize the data in the form
        var serializedData = $form.serialize();

        console.log(serializedData);

        // Let's disable the inputs for the duration of the Ajax request.
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        // Fire off the request to ajax_game_web_scrape.php
        request = $.ajax({
            url: "/games/ajax_game_web_scrape.php",
            type: "post",
            data: serializedData
        });

        // Callback handler that will be called on success
        request.done(function (response){
            var results = JSON.parse(response);
            console.log(response);
            $("#populartags").val(results["popular_tags"]);
            $("#gamename").val(results["game_name"]);
            $("#imageid").append($('<option>', {value:results["description"], selected:true, text:results["description"]}));
            $("#storeid").val(results["store_id"]);
            document.querySelector('#distributionplatform [value="Steam"]').selected = true;
            displayMessage(results["display_message"]);
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
            $('.nav-tabs a:first').tab('show');
            document.getElementById("submitForm").innerHTML = "Add Game";
        });
      });

    </script>
  </body>
</html>
