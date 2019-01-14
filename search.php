<?php
  include ("common.php");
  $db = get_PDO();
  if (isset($_GET["column"], $_GET["term"])) {
    $column = $_GET["column"];
    $term = $_GET["term"];
    search($column, $term, $db);
  } else {
    error("Missing either a column or search term.");
  }

  function search($column, $term, $db) {
    try {
      $rows = $db->query("SELECT * FROM Songs");
      header("Content-Type: application/json");
      print (json_encode($rows));

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
