<?php

  // IMPORTANT SIDENOTE: Database is vulnerable to SQL injection when searching for
  // either an artist or an album only.

  // IMPLEMENTATION SIDNEOTE:
  // If you haven't already looked at how the database is set up:
  // The database is a multitable database, and the main "songs" table only
  // references artists and albums by an id number. There are 4 functions here
  // to switch back and forth between id and name of artist or album.

  // ** A better way to do this would be to get the entire row and store its name
  // and id instead of constantly making queries.

  include ("common.php");
  header("Access-Control-Allow-Origin: *");
  $db = get_PDO();
  if (isset($_GET["column"], $_GET["term"])) {
    $column = $_GET["column"];
    $term = "";
    if ($column == "artist") {
      $term = get_artist_id_from_name("'" . $_GET["term"] . "'", $db);
    } else if ($column == "album") {
      $term = get_album_id_from_name("'" . $_GET["term"] . "'", $db);
    } else {
      $term = $_GET["term"];
    }
    search($column, $term, $db);
  } else {
    error("Missing either a column or search term.");
  }

  /*
   * Searches the database for entries by searching the given column by the given term.
   * Will return the songs in an array of arrays, sorted alphabetically by song name.
   * @param $column {String} - The column of the database to search by.
   * @param $term {String} - The term to search the column for.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function search($column, $term, $db) {
    try {
      // "name" column refers to song name in SQL database
      $sql = "SELECT * FROM Songs WHERE $column=:term ORDER BY name;";
      /*    SELECT S.name as Song, AR.name as Artist, S.release_date, A.name as Album, S.genre, S.medium
            FROM Songs as S
            JOIN Albums as A ON S.album = A.id
            JOIN Artists as AR ON S.artist = AR.id
            WHERE ;"
      */
      //
      $stmt = $db->prepare($sql);
      $params = array(":term" => $term);
      $stmt->execute($params);
      $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);
      $content = build_array_from_rows($arr, $db);
      echo json_encode($content);
    }
    catch(PDOException $ex) {
      catch_error("The search failed.", $ex);
    }
  }

  /*
   * Given the row returned by a SQL query, will return formatted rows which contain
   * relevant information to display on the front-end. Intended to be encoded
   * later into JSON.
   * @param $row {Array} - The rows returned by an SQL query.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function build_array_from_rows($rows, $db) {
    $output = array();
    foreach ($rows as $row) {
      $content = array();
      $content["name"] = $row["name"];
      $content["artist"] = get_artist_name_from_id($row["artist"], $db);
      $content["release_date"] = $row["release_date"];
      $content["album"] = get_album_name_from_id($row["album"], $db);
      $content["genre"] = $row["genre"];
      $content["medium"] = $row["medium"];
      array_push($output, $content);
    }
    return $output;
  }

  /*
   * Given the id of an artist, will will query the database to look up the
   * corresponding artist name, and return it as a string.
   * @param $id {String} - The id of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_artist_name_from_id($id, $db) {
    $sql = "SELECT name FROM Artists WHERE id=$id";
    $rows = $db->query($sql);
    $result = $rows->fetchALL(PDO::FETCH_ASSOC);
    // This is because "results" ends up being an array of one object with a
    // single property called "name".
    return $result[0]["name"];
  }

  /*
   * Given the name of an artist, will will query the database to look up the
   * corresponding artist id, and return it as a string.
   * @param $name {String} - The name of the artist.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_artist_id_from_name($name, $db) {
    $sql = "SELECT id FROM Artists WHERE name=$name";
    $rows = $db->query($sql);
    $result = $rows->fetchALL(PDO::FETCH_ASSOC);
    // This is because "results" ends up being an array of one object with a
    // single property called "id".
    return $result[0]["id"];
  }

  /*
   * Given the id of an album, will will query the database to look up the
   * corresponding album name, and return it as a string.
   * @param $id {String} - The id of the album.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_album_name_from_id($id, $db) {
    $sql = "SELECT name FROM Albums WHERE id=$id";
    $rows = $db->query($sql);
    $result = $rows->fetchALL(PDO::FETCH_ASSOC);
    // This is because "results" ends up being an array of one object with a
    // single property called "name".
    return $result[0]["name"];
  }

  /*
   * Given the name of an album, will will query the database to look up the
   * corresponding album id, and return it as a string.
   * @param $name {String} - The name of the album.
   * @param $db {PDO Object} - The PDO object for the referenced database.
  */
  function get_album_id_from_name($name, $db) {
    $sql = "SELECT id FROM Albums WHERE name=$name";
    $rows = $db->query($sql);
    $result = $rows->fetchALL(PDO::FETCH_ASSOC);
    // This is because "results" ends up being an array of one object with a
    // single property called "id".
    return $result[0]["id"];
  }
?>
