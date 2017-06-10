<?php

require_once getcwd() . '/games.config.php';

$db = getDBConnect(DSN, DB_USERNAME, DB_PASSWORD);

$isAdmin = getCurrentUser($db, $_SESSION['user_id'], TRUE);

$sql = "SELECT description, file_path FROM images ORDER BY description ASC";
$images_rs = dbGetRows($db, $sql);
closeDBConnection($db);

require_once getcwd() . '/include/global_nav_inc.html';

?>

    <div class="container-fluid">
      <div class="row">
        <div class="jumbotron text-center">
          <h1>Manage Images</h1>
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
          <div data-id="togglable-tabs">
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
              <li role="presentation" class="active">
                <a href="#add" id="add-tab" role="tab" data-toggle="tab" aria-controls="add" aria-expanded="true">Add Image</a>
              </li>
              <li role="presentation">
                <a href="#edit" role="tab" id="edit-tab" data-toggle="tab" aria-controls="edit">Edit Image</a>
              </li>
              <?php
              if ($isAdmin) {
                echo '
              <li role="presentation">
                <a href="#delete" role="tab" id="delete-tab" data-toggle="tab" aria-controls="delete">Delete Image</a>
              </li>';
              }
              ?>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade in active" role="tabpanel" id="add" aria-labelledby="add-tab">
                <br />
                <form name="addImage" action="game_image_manage_processing.php" method="POST" enctype="multipart/form-data" onsubmit="return validateAddForm(this)">
                  <input type="hidden" name="addSelected" id="addformid" value="1" />
                  <div class="form-group">
                    <label for="add_image_description">Image Description</label>
                    <input type="text" name="add_description" id="add_image_description" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label for="add_image_url">Add image by URL</label>
                    <input type="text" name="add_file_by_url" id="add_image_url" class="form-control" />
                  </div>
                  <p>Or</p>
                  <div class="form-group">
                    <label for="add_image_file">Upload image</label>
                    <input type="file" name="add_file_by_upload" id="add_image_file" class="form-control" />
                  </div>
                  <div class="form-group">
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/games/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Get Data</button>
                    </div>
                 </div>
                </form>
              </div>
              <div class="tab-pane fade" role="tabpanel" id="edit" aria-labelledby="edit-tab">
                <br />
                <form name="existingImages" action="game_image_manage_processing.php" method="POST" onsubmit="return validateEditForm(this)">
                  <input type="hidden" name="editSelected" id="editformid" value="0" />
                  <input type="hidden" name="edittedImage" id="edittedImageid" value="" />
                  <input type="hidden" name="edittedImagePathid" id="edittedImagePathid" value="" />
                 <div class="form-group">
                   <label for="imageid">Select Existing Image</label>
                   <select name="image" id="imageid" class="form-control" onChange="updateForm('imageid');">
                   <?php
                     echo buildSelectOption("", "&nbsp;");
                     foreach($images_rs as $image) {
                       echo buildSelectOption($image['description'], $image['file_path']);
                     }
                   ?>
                   </select>
                 </div>
                 <div class="form-group">
                   <label for="image_description">Image Description</label>
                   <input type="text" name="edit_description" id="image_description" class="form-control" />
                 </div>
                 <div class="form-group">
                   <label for="image_path">Image File Path</label>
                   <input type="text" name="edit_file_path" id="image_path" class="form-control" />
                 </div>
                  <div class="form-group">
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/games/">Cancel</a>
                      <button type="submit" class="btn btn-primary" id="submitForm">Save Edits</button>
                    </div>
                 </div>
                </form>
              </div>
              <?php
              if ($isAdmin) {
              ?>
              <div class="tab-pane fade in" role="tabpanel" id="delete" aria-labelledby="delete-tab">
                <br />
                <form name="deleteImage" action="game_image_manage_processing.php" method="POST" onsubmit="return confirm('Delete image?');">
                  <input type="hidden" name="deleteSelected" id="deleteformid" value="1" />
                  <div class="form-group">
                   <label for="imageid">Select Existing Image to Delete</label>
                   <select name="deleteimage" id="deleteimageid" class="form-control">
                   <?php
                     echo buildSelectOption("", "&nbsp;");
                     foreach($images_rs as $image) {
                       echo buildSelectOption($image['description'], $image['file_path']);
                     }
                   ?>
                   </select>
                  </div>
                  <div class="form-group">
                    <div class="btn-group btn-group pull-right" role="group">
                      <a class="btn btn-default" href="/games/">Cancel</a>
                      <button type="submit" class="btn btn-danger" id="submitForm">Delete Image</button>
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

    <script src="/games/js/jquery-3.1.1.min.js"></script>
    <script src="/games/js/bootstrap.min.js"></script>
    <script src="/games/js/games_functions.js"></script>
    <script>
      function validateAddForm(subForm) {
        clearValidationErrors();
        var violations = new Array();
        if (subForm.add_description.value.length <= 1) {
          violations.push(subForm.add_description.id);
        }
        if (subForm.add_file_by_url.value.length <= 1 && subForm.add_file_by_upload.value.length <= 1) {
          violations.push(subForm.add_file_by_url.id);
          violations.push(subForm.add_file_by_upload.id);
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
        if (subForm.edit_description.value.length <= 1) {
          violations.push(subForm.edit_description.id);
        }
        if (subForm.edit_file_path.value.length <= 1) {
          violations.push(subForm.edit_file_path.id);
        }
        if (violations.length > 0) {
          showValidationErrors(violations);
          return false;
        }
        else {
          return true;
        }
      }

      function updateForm(selObjID) {
        var selObj = document.getElementById(selObjID);
        document.getElementById('image_description').value = selObj.options[document.getElementById(selObjID).selectedIndex].text;
        document.getElementById('edittedImageid').value = selObj.options[document.getElementById(selObjID).selectedIndex].text;
        document.getElementById('image_path').value = selObj.value;
        document.getElementById('edittedImagePathid').value = selObj.value;
      }

      $('#myTabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        var selectedTab = this.id;
        <?php
        if ($isAdmin) {
        ?>
        if (selectedTab === "delete-tab") {
          document.getElementById("editformid").value = "0";
          document.getElementById("addformid").value = "0";
          if (document.getElementById("deleteformid") !== null) {
            document.getElementById("deleteformid").value = "1";
          }
          return;
        }
        <?php } ?>
        if (selectedTab === "add-tab") {
          document.getElementById("editformid").value = "0";
          document.getElementById("addformid").value = "1";
          if (document.getElementById("deleteformid") !== null) {
            document.getElementById("deleteformid").value = "0";
          }
          return;
        }
        if (selectedTab === "edit-tab") {
          document.getElementById("editformid").value = "1";
          document.getElementById("addformid").value = "0";
          if (document.getElementById("deleteformid") !== null) {
            document.getElementById("deleteformid").value = "0";
          }
          return;
        }
      });
    </script>
  </body>
</html>
