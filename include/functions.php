<?php

function buildSelectOption($display, $value, $selected_value=null) {
  $dom = new DOMDocument();
  $dom->formatOutput = true;
  $option = $dom->createElement('option', $display);
  $optvalue = $dom->createAttribute('value');
  $optvalue->value = $value;
  $option->appendChild($optvalue);

  if ($selected_value !== null && $value === $selected_value) {
    $optselected = $dom->createAttribute('selected');
    $optselected->value = "selected";
    $option->appendChild($optselected);
  }

  $dom->appendChild($option);

  return $dom->saveHtml();
}

function buildTableContent($content_label, $content_data, $html_id=null) {
  $dom = new DOMDocument();
  $dom->formatOutput = true;
  $tr = $dom->createElement('tr');
  $td_label = $dom->createElement('td', $content_label);
  $td_content = $dom->createElement('td', $content_data);
  $td_label->setAttribute('nowrap', "");
  if ($html_id !== null) {
    $td_content->setAttribute('id', $html_id);
  }

  $tr->appendChild($td_label);
  $tr->appendChild($td_content);
  $dom->appendChild($tr);

  return $dom->saveHtml();
}

function buildTableContentRow($content_data, $href=null) {
  $dom = new DOMDocument();
  $dom->formatOutput = true;
  $td = $dom->createElement('td');
  if ($href) {
    $anchor = $dom->createElement('a', $content_data);
    $anchor->setAttribute('href', "game_details.php?id=" . $href);
    $td_content = $dom->createElement('td');
    $td_content->appendChild($anchor);
  } else {
    $td_content = $dom->createElement('td', $content_data);
  }

  $td->appendChild($td_content);
  $dom->appendChild($td);

  return $dom->saveHtml();
}

function getCurrentUser($dbh, $sessions_id, $admin_flag=null) {
  $sql = "SELECT * FROM users WHERE id = :id";
  $statement = $dbh->prepare($sql);
  $statement->bindParam(':id', $sessions_id, PDO::PARAM_STR, 37);
  $statement->execute();
  $current_user_rs = $statement->fetch();
  $statement->closeCursor();
  if ($current_user_rs === false || empty($current_user_rs)) {
    return false;
  }
  else if ($admin_flag !== null) {
    return $current_user_rs['user_role'] == 2 ? TRUE : FALSE;
  }
  else {
    return $current_user_rs;
  }
}

function debug_to_console( $data ) {
    if ( is_array( $data ) )
      $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
    else
      $output = "<script>console.log( 'Debug Objects: " . $data . "');</script>";
    echo $output;
}

function wsafe(&$value,$key) {
  return safe($value);
}

function safe($value) {
  if(is_array($value)) {
    foreach($value as $key=>$val) {
      $value[safe($key)] = safe($val);
     }
  }
  else {
    $value = trim(htmlentities(strip_tags($value)));
  }
  return $value;
}

class UUID {
  public static function v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }
}