<?php
  include ("common.php");
  $db = get_PDO();
  if (isset($_POST["album_name"], $_POST["artist_name"])) {
    $artist_name = $_POST["artist_name"];
    $album_name = $_POST["album_name"];

    // If there isn't already an artist with this name by this artist, add it.
    if (!artist_in_table($artist_name, $db)) {
      try {
        insert_artist($artist_name, $db);
        success("Added " . $artist_name . " to artists table.");
      }
      catch(PDOException $ex) {
        catch_error("The artist failed to be inserted.", $ex);
      }
    }

    // Attempts to insert the album, first checking for conflicts.
    $artist_id = get_artist_id($artist_name, $db);
    if (album_in_table($album_name, $artist_id, $db)) {
      error("Album called " . $album_name . " by " . $artist_name . " is already in the albums table.");
    } else {
      try {
        insert_album($album_name, $artist_id, $db);
        success("Added " . $album_name . " by " . $artist_name . " to the albums table.");
      }
      catch(PDOException $ex) {
        catch_error("The album failed to be inserted.", $ex);
      }
    }
  } else {
    error("Missing either the album name or artist name parameters.");
  }
?>
