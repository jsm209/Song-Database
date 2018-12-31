<?php
  include ("common.php");
  $db = get_PDO();
  $params_needed = ["song_name", "artist_name", "song_release_date",
                    "song_genre", "song_medium"];

  // Special case to handle missing album name (defaults as a single).
  if (!isset($_POST["album_name"])) {
    $album_name = "single";
  } else {
    $album_name = $_POST["album_name"];
  }

  // When we're inserting into the DB, we want to have the following parameters:
  // Song name, artist name, album name, song release date, song genre, song medium
  if (all_params_valid($params_needed)) {

    // Getting all given parameters
    $song_name = $_POST["song_name"];
    $artist_name = $_POST["artist_name"];
    $song_release_date = $_POST["song_release_date"];
    $song_genre = $_POST["song_genre"];
    $song_medium = $_POST["song_medium"];



    // Checking if song is already in the database.
    if (song_in_db($song_name, $db)) {
      error("Song called \"" . $song_name . "\" is already in the database.");
    }
    // Checks if artist/album already exists, if not add it in.
    validate_artist_name($artist_name, $db);
    validate_album_name($album_name, $artist_name, $db);
    // SLIGHT ISSUE: Albums need to have an artist associated with it.
    // Should make a new album entry only if the artist doesn't already have an
    // album also named the same thing. // FIXED.

    // ANOTHER SLIGHT ISSUE: The song might be a single. If so, the album field
    // should be allowed past the isset check. If that's the case, there is no need
    // to validate the album since there is no album. Make this an if statement.
    // That could be one fix, or during the POST request just have it submit "single"
    // by default if album isn't specified. // FIXED.

    // Another way to solve this ^^ is to ignore checking initially if album_name
    // isset, check later. If not set, then set album_name to single. // FIXED.

    function get_id($table_name, $name, $db) {
      try {
        $sql = "SELECT id FROM :table WHERE name=:name;";
        $stmt = $db->prepare($sql);
        $params = array("table" => $table_name,
                        "name" => $name);
        $stmt->execute($params);
      }
      catch (PDOException $ex) {
        catch_error("Failed to get " . $name . " from " . $table_name . ".", $ex);
      }
    }

    // Now that artist/album are in database, get their IDs.
    // $artist_ID = $db->query("SELECT id FROM artists WHERE name=$artist_name"); //not secure.
    // $album_ID = $db->query("SELECT id FROM albums WHERE name=$album_name"); //not secure.
    $artist_ID = get_id("artists", $artist_name, $db);
    $album_ID = get_id("albums", $album_name, $db);


    // *neither of these connections are secure, fix them later.

    // With these IDs, add song to database.
    insert_song($song_name, $artist_name, $song_release_date, $album_name,
                $song_genre, $song_medium, $db);

    // Adding a song to database:
    //  Create a new entry in the Songs table that has the song_name, song_release_date
    //  song_genre, and song_medium.
    //
    //  Before doing that however, check the database to see if the given artist_name
    //  is already in there, and if so, get its id, else, also enter in the artist into
    //  the Artists table, and also get that id.
    //
    //  Likewise, do the same for the album: check if the given album_name already exists
    //  in the Albums table, and if so, get its id; if not, add it to the table, then get id.
    //  So after you have the appropriate ids of the artist and album, the song can be
    //  added to the Songs table.
  } else {
    error("Missing some of the necessary parameters about the song.");
  }

  // Will check if all necessary parameters are set.
  /*
  function all_params_valid($params_needed) {
    foreach ($params_needed as $param) {
      if (!isset($_POST[(string)$param])) {
        error("Missing the " . $param . " variable in POST request.");
        return false;
      }
    }
    return true;
  }*/

  function all_params_valid($params_needed) {
    $given_params = array_keys($_POST);
    $missing_params = array_diff($params_needed, $given_params);
    if (!empty($missing_params)) {
      success(implode(", ", $given_params));
      error("Missing the " . implode(", ", $missing_params) . " parameter(s) in POST request.");
      return false;
    }
    success("all params are valid");
    return true;
  }

  function song_in_db($song_name, $db) {
    try {
      $sql = "SELECT * FROM Songs WHERE name=:name;";
      $stmt = $db->prepare($sql);
      $params = array("name" => $song_name);
      $stmt->execute($params);
      return (count($stmt->fetchAll()) > 0);
    }
    catch (PDOException $ex) {
      return false;
    }
  }

  // Given an artist name, checks if it already exists in the appropriate table
  // in the given database, and if not, add it in. This is so that the table
  // will always have the artist specified during the song entry.
  // Impossible to fail the artist validation.
  function validate_artist_name($artist_name, $db) {
    try {
      $sql = "SELECT * FROM artists WHERE name=:name;";
      $stmt = $db->prepare($sql);
      $params = array("name" => $artist_name);
      $stmt->execute($params);
      if (count($stmt->fetchAll()) <= 0) {
        // Artist name didn't exist, so add it in.
        $sql2 = "INSERT INTO artists (name) VALUES (:name);";
        $stmt2 = $db->prepare($sql);
        $params2 = array("name" => $artist_name);
        $stmt->execute($params);
        success("added " . $artist_name . " to artists table.");
      }
      success("artist name validated");
    }
    catch (PDOException $ex) {
      catch_error("The artist failed to validate.", $ex);
    }
  }

  // Given an album name, checks if it already exists in the appropriate table
  // in the given database, and if not, add it in. This is so that the table
  // won't have duplicate albums by the same artist. Possible to fail album validation
  // if given a duplicate album by the same artist.
  function validate_album_name($album_name, $artist_name, $db) {
    try {
      $sql = "SELECT * FROM albums WHERE name=:album_name AND albums.artist=:artist_name;";
      $stmt = $db->prepare($sql);
      $params = array("album_name" => $album_name,
                      "artist_name" => $artist_name);
      $stmt->execute($params);
      if (count($stmt->fetchAll()) <= 0) {
        // The artist doesn't have an album with that name already, so add it in.
        $sql2 = "INSERT INTO albums (name, artist) VALUES (:name, :artist);";
        $stmt2 = $db->prepare($sql);
        $params2 = array("name" => $album_name,
                         "artist" => $artist_name);
        $stmt->execute($params);
        success("added " . $album_name . " to album table.");
      } else {
        error("The artist already has an album with that name.");
      }
      success("album name validated");
    }
    catch (PDOException $ex) {
      catch_error("The album failed to validate.", $ex);
    }
  }

  // Given all the information about a song (song's name, artist's name, song's
  // release date, album's name that the song belongs to, the song's genre, and
  // the medium the song is on in that order! phew.) will enter the song into
  // the given database.
  function insert_song($song_name, $artist_name, $song_release_date, $album_name,
                       $song_genre, $song_medium, $db) {
    try {
      $sql = "INSERT INTO Songs (name, artist, release_date, album, genre, medium)
              VALUES (:name, :artist, :release_date, :album, :genre, :medium);";
      $stmt = $db->prepare($sql);
      $params = array("name" => $song_name,
                      "artist" => $artist_name,
                      "release_date" => $song_release_date,
                      "album" => $album_name,
                      "genre" => $song_genre,
                      "medium" => $song_medium);
      $stmt->execute($params);
      success("Added " . $song_name . " by " . $artist_name . " to the database.");
    }
    catch (PDOException $ex) {
      catch_error("There was an issue inserting the song into the database.", $ex);
    }
  }
?>
