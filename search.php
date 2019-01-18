<?php
  include ("common.php");
  header("Access-Control-Allow-Origin: *");
  $db = get_PDO();
  search("test", "ok", $db);
  // if (isset($_GET["column"], $_GET["term"])) {
  //   $column = $_GET["column"];
  //   $term = $_GET["term"];
  //   search($column, $term, $db);
  // } else {
  //   error("Missing either a column or search term.");
  // }

  function search($column, $term, $db) {
    try {
      $rows = $db->query("
      SELECT S.name as Song, AR.name as Artist, S.release_date, A.name as Album, S.genre, S.medium
      FROM Songs as S
      JOIN Albums as A ON S.album = A.id 
      JOIN Artists as AR ON S.artist = AR.id
      WHERE ;");
      $result = $rows->fetchAll(PDO::FETCH_ASSOC);
      header("Content-Type: application/json");
      print (json_encode($result));

      /*
      $sql = "SELECT * FROM Songs WHERE :column=:term;";
      $stmt = $db->prepare($sql);
      $params = array("column" => $column,
                      "term" => $term);
      $stmt->execute($params);
      $arr = $stmt->fetchAll();
      return $arr;
      */
    }
    catch(PDOException $ex) {
      catch_error("The search failed.");
    }
  }
?>
