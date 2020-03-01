<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);
if (isset($_GET)) {
  $id = array_key_exists('id', $_GET) ? safe($_GET['id']) : null;
}
else {
  $id = null;
}
if ($id !== null && strlen($id) === 0) {
  $id = null;
  $no_user = true;
}
else {
  $no_user = false;
}

if ($no_user === false) {
  $user_rs = getCurrentUser($db, $id);
  if ($user_rs === false || empty($user_rs)) {
    $no_user = true;
  }
  else {
    $sql = "SELECT game_name, id, purchase_date, store, redeemed, played FROM games WHERE game_owner = :user_id ORDER BY purchase_date DESC, game_name ASC";
    $statement = $db->prepare($sql);
    $statement->bindParam(':user_id', $user_rs['id'], PDO::PARAM_STR, 37);
    $statement->execute();
    $users_games_rs = $statement->fetchAll();

    $isAdmin = getCurrentUser($db, $_SESSION['user_id'], TRUE);

    if ($isAdmin) {
      $sql = "SELECT display_name, email FROM users";
      $statement = $db->prepare($sql);
      $statement->execute();
      $admin_all_users_rs = $statement->fetchAll();
    }
  }
  closeDBConnection($db, $statement);
}

require_once getcwd() . '/include/global_nav_inc.html';

?>

    <div class="container-fluid">

      <div class="row">
        <div class="jumbotron text-center">
          <?php if ($no_user === false) { echo "<h1>" . $user_rs['display_name'] . "'s Profile</h1>"; } ?>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-success hidden" id="successUpdate">
            <div class="panel-heading">
              <h3 class="panel-title">
                Success!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              Profile Updated.
            </div>
          </div>
          <div class="panel panel-success hidden" id="successDetails">
            <div class="panel-heading">
              <h3 class="panel-title">
                Success!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              User details updated.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorUpdate">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              An error occurred.
            </div>
          </div>
          <div class="panel panel-danger hidden" id="errorPermission">
            <div class="panel-heading">
              <h3 class="panel-title">
                Error!
                <span class="glyphicon glyphicon-remove-circle pull-right" aria-hidden="true"></span>
              </h3>
            </div>
            <div class="panel-body">
              You do not have adequate user permissions to complete that action.
            </div>
          </div>
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
          <?php
          if ($no_user) {
            echo "<h2>No user found.</h2>
            <p><a href='index.php'>Go back.</a></p>
            </div></div></div>";
          }
          else {
          ?>
          <div data-id="togglable-tabs">
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
              <li role="presentation" class="active">
                <a href="#edit" id="edit-tab" role="tab" data-toggle="tab" aria-controls="edit" aria-expanded="true">Edit Profile</a>
              </li>
              <li role="presentation">
                <a href="#viewgames" id="viewgames-tab" role="tab" data-toggle="tab" aria-controls="viewgames" aria-expanded="true">View Your Games</a>
              </li>
              <?php
                if ($isAdmin) {
                  echo '<li role="presentation">
                  <a href="#adduser" role="tab" id="adduser-tab" data-toggle="tab" aria-controls="adduser">Add User</a>
                </li>';
                  echo '<li role="presentation">
                  <a href="#deleteuser" role="tab" id="deleteuser-tab" data-toggle="tab" aria-controls="deleteuser">Delete User</a>
                </li>';
                }
              ?>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade in active" role="tabpanel" id="edit" aria-labelledby="edit-tab">
                <form action="/user_details_processing.php" method="POST" name="editUser" id="editUserID" onsubmit="return validateEditForm(this)">
                  <input type="hidden" name="formAction" value="editUser" />
                  <input type="hidden" name="old_email" id="oldemail" value="<?php echo $user_rs['email']; ?>" />
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
                  <?php
                  if ($isAdmin) {
                  echo '<div class=form-group">
                    <label for="userlist">All Users</label>
                    <select name="user_list" id="userlist" class="form-control">';
                      echo buildSelectOption("&nbsp;","&nbsp;");
                      foreach ($admin_all_users_rs as $user_detail) {
                        echo buildSelectOption($user_detail['display_name'] . " - " . $user_detail['email'], $user_detail['email'], $user_rs['email']);
                      }
                     echo '</select></div>';
                  }
                  ?>
                  <div class="form-group">
                    <label for="displayname">User Display Name</label>
                    <input type="text" class="form-control" id="displayname" name="new_display_name" value="<?php
                      if (!$isAdmin) { echo $user_rs['display_name']; } ?>" />
                  </div>
                  <div class="form-group">
                    <label for="emailid">email Address</label>
                    <input type="text" class="form-control" id="emailid" name="new_email" value="<?php
                      if (!$isAdmin) { echo $user_rs['email']; } ?>" />
                  </div>
                  <div class="form-group">
                    <label for="pwd1id">Change Password</label>
                    <input type="text" class="form-control" id="pwd1id" name="password" />
                  </div>
                  <div class="form-group">
                    <label for="pwd2id">Verify Password</label>
                    <input type="text" class="form-control" id="pwd2id" name="password_verify" />
                  </div>
                  <div class="form-group" title="If checked, the game key will be obscured when displayed.">
                    <input type="checkbox" name="game_key_privacy" id="gkp"
                    <?php
                      if ($_SESSION['game_key_privacy'] === 1) {
                        echo 'checked value="true"';
                      }
                      else {
                        echo 'value="false"';
                      }
                     ?>
                    >
                    <label for="gkp">Game Key Privacy</label>&nbsp;<span class="glyphicon glyphicon-question-sign" aria-hidden=true></span>
                  </div>
                  <?php
                    if ($isAdmin) {
                    echo '<div class=form-group">
                      <label for="userrole">Role</label>
                      <select name="user_role" id="userrole" class="form-control">';
                      echo buildSelectOption("&nbsp;","&nbsp;");
                      echo buildSelectOption("Disabled", "0");
                      echo buildSelectOption("User", "1", $isAdmin ? null : "1");
                      echo buildSelectOption("Admin", "2", $isAdmin ? "2" : null);
                     echo '</select></div>';
                    }
                  ?>
                  <div class="form-group">
                    <br />
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Save User Edits</button>
                    </div>
                 </div>
                </form>
              </div>
              <div class="tab-pane fade in" role="tabpanel" id="viewgames" aria-labelledby="viewgames-tab">
                <div class="table-responsive">
                  <table class="table table-striped sortable">
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
                    <tbody>
                    <?php
                      foreach($users_games_rs as $games) {
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
             <?php if ($isAdmin) { ?>
              <div class="tab-pane fade in" role="tabpanel" id="adduser" aria-labelledby="adduser-tab">
                <br />
                <form action="/user_details_processing.php" method="POST" name="addUser" id="addUserId" onsubmit="return validateAddForm(this)">
                  <input type="hidden" name="formAction" value="addUser" />
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
                  <div class="form-group">
                    <label for="gamename">User Display Name</label>
                    <input type="text" class="form-control" id="displayname2" name="display_name" placeholder="User Display Name" />
                  </div>
                  <div class="form-group">
                    <label for="costid">email Address</label>
                    <input type="text" class="form-control" id="emailid2" name="email" placeholder="email Address" />
                  </div>
                  <div class="form-group">
                    <label for="addpwd1id">Password</label>
                    <input type="text" class="form-control" id="addpwd1id2" name="password" />
                  </div>
                  <div class="form-group">
                    <label for="addpwd2id">Verify Password</label>
                    <input type="text" class="form-control" id="addpwd2id2" name="password_verify" />
                  </div>
                  <div class="form-group">
                    <label for="userrole2">Role</label>
                    <select name="user_role" id="userrole2" class="form-control">
                      <?php
                      echo buildSelectOption("&nbsp;","");
                      echo buildSelectOption("Disabled", "0");
                      echo buildSelectOption("User", "1");
                      echo buildSelectOption("Admin", "2");
                      ?>
                     </select>
                  </div>
                  <div class="form-group">
                    <br />
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Add User</button>
                    </div>
                 </div>
                </form>
              </div>
              <div class="tab-pane fade in" role="tabpanel" id="deleteuser" aria-labelledby="deleteuser-tab">
                <form action="/user_details_processing.php" method="POST" name="deleteUser" id="deleteUserID" onsubmit="return verifyDelete();">
                  <input type="hidden" name="formAction" value="deleteUser" />
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
                  <div class="form-group">
                    <label for="userlist2">User List</label>
                    <select name="user_list" id="userlist2" class="form-control">
                      <?php
                      echo buildSelectOption("&nbsp;","&nbsp;");
                      foreach ($admin_all_users_rs as $user_detail) {
                        echo buildSelectOption($user_detail['display_name'] . " - " . $user_detail['email'], $user_detail['email']);
                      }
                      ?>
                     </select>
                  </div>
                  <div class="form-group">
                    <br />
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/">Cancel</a>
                      <button type="submit" class="btn btn-danger" id="submitForm">Delete User</button>
                    </div>
                 </div>
                </form>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>

        </div>
      </div>
    </div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/bootstrap-sortable.js"></script>
    <script src="/js/games_functions.js"></script>
    <script>
      var isAdmin = <?php echo $isAdmin ? 'true': 'false'; ?>;
      $( document ).ready(function() {

        $("#myTabs a").click(function (e) {
          e.preventDefault();
          $(this).tab("show");
        });

        if (document.getElementById('userlist') !== null) {
          $("#userlist").on("change", function() {
              updateUser($("#userlist").val());
              var selObj = document.getElementById("userlist");
              document.getElementById('oldemail').value = selObj.options[selObj.selectedIndex].value;
          });
        }

        $(".panel").bind("click", function() {
          $(this).addClass("hidden");
        });
        if (isAdmin) {
          updateUser(document.getElementById('editUserID').user_list.options[document.getElementById('editUserID').user_list.selectedIndex].value);
        }

      });

      function verifyDelete() {
        if (confirm("Are you sure you wish to delete this user?")) {
          return true;
        }
        else {
          return false;
        }
      }

      function validateAddForm(subForm) {
        clearValidationErrors();
        var violations = new Array();

        if (subForm.display_name.value.length <= 1) {
          violations.push(subForm.display_name.id);
        }
        if (subForm.email.value.length <= 1 && !validateEmail(subForm.email.value)) {
          violations.push(subForm.email.id);
        }
        if (subForm.password.value.length <= 1) {
          violations.push(subForm.password.id);
        }
        if (subForm.password.value !== subForm.password_verify.value) {
          violations.push(subForm.password_verify.id);
        }
        if (typeof subForm.user_role !== 'undefined') {
          if (subForm.user_role.options[subForm.user_role.selectedIndex].value == "") {
            violations.push(subForm.user_role.id);
          }
        }

        if (violations.length > 0) {
          showValidationErrors(violations);
          return false;
        }
        else {
          return true;
        }
      }

      function validateEditForm(subForm) {
        clearValidationErrors();
        var violations = new Array();

        if (subForm.new_display_name.value.length <= 1) {
          violations.push(subForm.new_display_name.id);
        }
        if (subForm.new_email.value.length <= 1 && !validateEmail(subForm.email.value)) {
          violations.push(subForm.new_email.id);
        }
        if (subForm.password.value.length > 0 && subForm.password.value !== subForm.password_verify.value) {
          violations.push(subForm.password_verify.id);
        }
        if (typeof subForm.user_role !== 'undefined') {
          if (subForm.user_role.options[subForm.user_role.selectedIndex].value == "") {
            violations.push(subForm.user_role.id);
          }
        }

        if (violations.length > 0) {
          showValidationErrors(violations);
          return false;
        }
        else {
          return true;
        }
      }

      function updateUser(passedVal) {
        var serializedData = "user_list=" + passedVal;

        request = $.ajax({
            url: "/ajax_user_get_details.php",
            type: "post",
            data: serializedData
        });

        request.done(function (response){
            var results = JSON.parse(response);
            $("#displayname").val(results["display_name"]);
            $("#emailid").val(results["email"]);
            if (document.getElementById('userrole') !== null) {
              document.getElementById('userrole').selectedIndex = parseInt(results["user_role"]) + 1;
            }
            if (document.getElementById(results["display_message"]) !== null) {
              displayMessage(results["display_message"]);
            }
        });

        // Callback handler that will be called on failure
        request.fail(function (response){
          var results = JSON.parse(response);
          displayMessage(results["display_message"]);
        });
      }

      function validateEmail(email) {
        if (typeof email == 'undefined') {
          return false;
        }
        else {
          var pattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
          result = pattern.test(email);
          return result;
        }
      }
    </script>
    <?php } ?>
  </body>
</html>
