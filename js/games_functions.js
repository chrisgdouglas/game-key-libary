$( document ).ready(function() {
  displayActionMessages();

  // BEGIN - Ajax Search Results
  $("#searchField").on("keyup",function() {
    var searchFieldLength = $("#searchField").val().length;
    if (searchFieldLength < 3) { // under 3 characters in search, hide the dropdown
      if ($("#dropDownParent").hasClass("hidden") === false) {
        $("#dropDownParent").addClass("hidden");
      }
    }
    if (searchFieldLength >= 3) { // 3 or more characters in search, fire off the AJAX calls.
      var serializedData = "game_name=" + $("#searchField").val().toString();
      request = $.ajax({
          url: "/games/ajax_game_titlesearch.php",
          type: "post",
          data: serializedData
      });
      request.done(function (response){
          var results = JSON.parse(response).map(function(i) { return i.name; });
          if (results.length !== 0) {
            buildDropDownItems(results, 'dropDownParent');
            $("#dropDownParent").removeClass("hidden");
            $('.dropdown-toggle').dropdown();
          }
      });
    }
  });
  // END - Ajax Search Results
  $("#searchButton").bind('click', function(){
    document.forms.searchFormid.submit();
  });
});


function displayActionMessages() {
  if (location.search) {
    locsearchArray = getUrlVars();
    if (locsearchArray["actionMsg"] !== undefined) {
      document.getElementById(locsearchArray["actionMsg"]).classList.remove("hidden");
      $(".panel").bind('click', function() {
        $(this).addClass('hidden');
      });
    }
  }
}

function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function displayMessage(panelId) {
  document.getElementById(panelId).classList.remove("hidden");
}

function buildDropDownItems(itemArray, parentId) {
  var dropdownUlNode = document.getElementById(parentId);
  removeChildNodes(parentId);
  for (i=0; i<itemArray.length; i++) {
    if (itemArray[i].length>32) {
      itemText = itemArray[i].substring(0, 30) + "...";
    }
    else {
      itemText = itemArray[i]
    }
    var li = document.createElement("li");
    var anchor = document.createElement("a")
      var anchorText = document.createTextNode(itemText);
      var href = document.createAttribute("href");
        href.value = "/games/game_details.php?game_name=" + itemArray[i];
      anchor.setAttributeNode(href);
      anchor.appendChild(anchorText);
    li.appendChild(anchor);
    dropdownUlNode.appendChild(li);
  }
}

function removeChildNodes(elemID) {
  var parentNode = document.getElementById(elemID)
  if (parentNode.firstChild !== null) {
    while (parentNode.firstChild) {
      parentNode.removeChild(parentNode.firstChild);
    }
  }
}

function clearValidationErrors() {
  $("label").each(function() {
    $(this).removeClass('text-danger');
  });
  $('#errorFormSubmission').addClass('hidden');
}

function showValidationErrors(violations) {
  for (i=0; i<violations.length; i++) {
    var labelEl = document.getElementById(violations[i]).previousElementSibling;
    $(labelEl).addClass('text-danger');
  }
  $('#errorFormSubmission').removeClass('hidden');
}