<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  date_default_timezone_set("America/Los_Angeles");

  /*
   * Returns a PDO object connected to the bmstore database. Throws
   * a PDOException if an error occurs when connecting to database.
   * @return {PDO}
   */
  function get_PDO() {
    $host = "localhost";
    $port = "3307";
    $user = "root";
    $password = "root";
    $dbname = "rdr_song_db";
    $ds = "mysql:host={$host}:{$port};dbname={$dbname};charset=utf8";
    try {
      $db = new PDO($ds, $user, $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $db;
    }
    catch(PDOException $ex) {
      handle_error("There was a problem connecting to the database!", $ex);
    }
  }

  /////////////////////////////////////
  // FUNCTIONS FOR INSERTIMG ARTISTS //
  /////////////////////////////////////
  // Used in the following files:
  // insert_artist.php
  // insert_album.php

  /*
   * Inserts the given artist into the artists table.
   * @param $name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function insert_artist($name, $db) {
    $sql = "INSERT INTO Artists (name) VALUES (:name);";
    $stmt = $db->prepare($sql);
    $params = array("name" => $name);
    $stmt->execute($params);
  }

  /*
   * Checks if the name of the given artist exists in the given database.
   * @param $name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function artist_in_table($name, $db) {
      try {
          $sql = "SELECT * FROM Artists WHERE name=:name;";
          $stmt = $db->prepare($sql);
          $params = array("name" => $name);
          $stmt->execute($params);
          return (count($stmt->fetchAll()) > 0);
      }
      catch(PDOException $ex) {
          return false;
      }
  }

  ////////////////////////////////////
  // FUNCTIONS FOR INSERTIMG ALBUMS //
  ////////////////////////////////////
  // Used in the following files:
  // insert_album.php

  /*
   * Inserts the given album into the albums table.
   * @param $album_name {String} - The name of the album.
   * @param $artist_name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function insert_album($album_name, $artist_id, $db) {
    $sql = "INSERT INTO Albums (name, artist) VALUES (:name, :artist);";
    $stmt = $db->prepare($sql);
    $params = array("name" => $album_name,
                    "artist" => $artist_id);
    $stmt->execute($params);
  }

  /*
   * Checks if the name of the given album by a specific artist exists in the
   * given database.
   * @param $album_name {String} - The name of the album.
   * @param $artist_id {String} - The id of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function album_in_table($album_name, $artist_id, $db) {
      try {
          $sql = "SELECT * FROM Albums WHERE name=:album_name AND artist=:artist_id;";
          $stmt = $db->prepare($sql);
          $params = array("album_name" => $album_name,
                          "artist_id" => $artist_id);
          $stmt->execute($params);
          return (count($stmt->fetchAll()) > 0);
      }
      catch(PDOException $ex) {
          return false;
      }
  }

  /*
   * Gets the artist id that corresponds to the given artist name.
   * @param $artist_name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_artist_id($artist_name, $db) {
    try {
      $sql = "SELECT * FROM Artists WHERE name=:name;";
      $stmt = $db->prepare($sql);
      $params = array("name" => $artist_name);
      $stmt->execute($params);
      $arr = $stmt->fetchAll();
      // $arr is an array with row data inside a reponse array.
      return $arr[0]["id"];
    }
    catch(PDOException $ex) {
      catch_error("The artist ID for that artist failed to be found", $ex);
    }
  }

  ///////////////////////////////////
  // FUNCTIONS FOR INSERTIMG SONGS //
  ///////////////////////////////////
  // Used in the following files:
  // insert_song.php

  /*
   * Inserts the given song into the albums table.
   * @param $album_name {String} - The name of the album.
   * @param $artist_name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function insert_song($song_name, $artist_id, $release_date, $album_id, $genre, $medium, $db) {
    $sql = "INSERT INTO Songs (name, artist, release_date, album, genre, medium) VALUES (:name, :artist, :release_date, :album, :genre, :medium);";
    $stmt = $db->prepare($sql);
    $params = array("name" => $song_name,
                    "artist" => $artist_id,
                    "release_date" => $release_date,
                    "album" => $album_id,
                    "genre" => $genre,
                    "medium" => $medium);
    $stmt->execute($params);
  }

  /*
   * Checks if the name of the given song by a specific artist on a specific album
   * exists in the given database.
   * @param $song_name {String} - The name of the song.
   * @param $artist_id {String} - The id of the artist.
   * @param $album_id {String} - The id of the album.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function song_in_table($song_name, $artist_id, $album_id, $db) {
      try {
          $sql = "SELECT * FROM Songs WHERE name=:song_name AND artist=:artist_id AND album=:album_id;";
          $stmt = $db->prepare($sql);
          $params = array("song_name" => $song_name,
                          "artist_id" => $artist_id,
                          "album_id" => $album_id);
          $stmt->execute($params);
          return (count($stmt->fetchAll()) > 0);
      }
      catch(PDOException $ex) {
          return false;
      }
  }

  /*
   * Gets the album id that corresponds to the given album name by a particular artist.
   * @param $album_name {String} - The name of the album.
   * @param $artist_id {String} - The id of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_album_id($album_name, $artist_id, $db) {
    try {
      $sql = "SELECT * FROM Albums WHERE name=:album_name AND artist=:artist_id;";
      $stmt = $db->prepare($sql);
      $params = array("album_name" => $album_name,
                      "artist_id" => $artist_id);
      $stmt->execute($params);
      $arr = $stmt->fetchAll();
      // $arr is an array with row data inside a reponse array.
      return $arr[0]["id"];
    }
    catch(PDOException $ex) {
      catch_error("The album ID for that artist failed to be found", $ex);
    }
  }

  ///////////////////////////////
  // GENERAL OUTPUT STATEMENTS //
  ///////////////////////////////

  /*
   * Gives a 503 error output in the form of a plain text response containing a
   * particular error message along with the PDOException.
   * @param $msg {String} - The given custom text to display in error response.
   * @param $ex {String} - The given exception from the catch error.
   */
  function catch_error($msg, $ex) {
    header("HTTP/1.1 503 Internal Database Error");
    header("Content-Type: text/plain");
    die($msg . " Interal Error: " . $ex);
  }

  /*
   * Gives a success output in the form of a JSON response containing an associative
   * array of a single entry labeled "success" which corresponds to the success message.
   * @param $text {String} - The given text to display in success response.
   */
  function success($text) {
    header("Content-type: application/json");
    print (json_encode(Array("success" => $text)));
  }

  /*
   * Gives a 404 error output in the form of a JSON response containing an associative
   * array of a single entry labeled "error" which corresponds to the error message.
   * @param $text {String} - The given text to display in error response.
   */
  function error($text) {
    header("HTTP/1.1 400 Invalid Request");
    header("Content-type: application/json");
    print (json_encode(Array("error" => $text)));
    die();
  }
?>
