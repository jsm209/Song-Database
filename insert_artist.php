<?php
  include ("common.php");
  $db = get_PDO();
  if (isset($_POST["artist_name"])) {
    $artist_name = $_POST["artist_name"];
    if (artist_in_table($artist_name, $db)) {
      error("Artist named " . $artist_name . " already in artists table.");
    }
    try {
      insert_artist($artist_name, $db);
      success("Added " . $artist_name . " to artists table.");
    }
    catch(PDOException $ex) {
      catch_error("The artist failed to be inserted.", $ex);
    }
  } else {
    error("Missing 'name' parameter.");
  }
?>
