<?php
  include ("common.php");
  $db = get_PDO();
  /*
  if (isset($_GET["column"], $_GET["term"])) {
    $column = $_GET["column"];
    $term = $_GET["term"];
    search($column, $term, $db);
  } else {
    error("Missing either a column or search term.");
  }
  */
  search("genre", "electropop", $db);
  function search($column, $term, $db) {
    try {
      /*
      $rows = $db->query("SELECT * FROM Songs");
      $test = $rows->fetchALL(PDO::FETCH_ASSOC);
      header("Content-Type: application/json");
      print (json_encode($test));
      */

      $sql = "SELECT * FROM Songs WHERE :column=:term;";
      $stmt = $db->prepare($sql);
      $params = array("column" => $column,
                      "term" => $term);
      $stmt->execute($params);
      $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);
      print (json_encode($arr));
    }
    catch(PDOException $ex) {
      catch_error("The search failed.", $ex);
    }
  }

  function build_array_from_rows($rows) {
    $output = array();
    foreach ($rows as $row) {
      $content = array();
      $content["name"] = $row["name"];
      $content["artist"] = $row["artist"]; // get artist name with function later...
      $content["release_date"] = $row["release_date"];
      $content["album"] = $row["album"]; // get album name with function later...
      $content["genre"] = $row["genre"];
      $content["medium"] = $row["medium"];
      array_push($output, $content);
    }
  }
?>
