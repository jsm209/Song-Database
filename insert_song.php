<?php
  include ("common.php");
  header("Access-Control-Allow-Origin: *");
  $db = get_PDO();
  $params_needed = ["song_name", "artist_name", "song_release_date",
                    "album_name", "song_genre", "song_medium"];
  if (isset($_POST["song_name"], $_POST["artist_name"], $_POST["song_release_date"],
            $_POST["album_name"], $_POST["song_genre"], $_POST["song_medium"])) {
    $song_name = $_POST["song_name"];
    $artist_name = $_POST["artist_name"];
    $song_release_date = $_POST["song_release_date"];
    $album_name = $_POST["album_name"];
    $song_genre = $_POST["song_genre"];
    $song_medium = $_POST["song_medium"];

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

    // If there isn't already an album with this name by this artist, add it.
    $artist_id = get_artist_id($artist_name, $db);
    if (!album_in_table($album_name, $artist_id, $db)) {
      try {
        insert_album($album_name, $artist_id, $db);
        success("Added " . $album_name . " by " . $artist_name . " to the albums table.");
      }
      catch(PDOException $ex) {
        catch_error("The album failed to be inserted.", $ex);
      }
    }

    // If there isn't already a song by this artist on the same album, then add it.
    $album_id = get_album_id($album_name, $artist_id, $db);
    if (song_in_table($song_name, $artist_id, $album_id, $db)) {
      error("The song called " . $song_name . " by " . $artist_name . " on the album " . $album_name . " already exists.");
    } else {
      try {
        insert_song($song_name, $artist_id, $song_release_date, $album_id, $song_genre, $song_medium, $db);
        success("Added " . $song_name . " by " . $artist_name . " to the songs table.");
      }
      catch(PDOException $ex) {
        catch_error("The song failed to be inserted.");
      }
    }
  } else {
    $given_params = array_keys($_POST);
    $missing_params = array_diff($params_needed, $given_params);
    error("Missing the " . implode(", ", $missing_params) . " parameter(s) in POST request.");
  }
?>
