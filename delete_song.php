<?php
    include ("common.php");
    $db = get_PDO();
    if (isset($_POST["id"])) {
      $id = $_POST["id"];
    } else {
      error("Please give a song's ID to delete it.");
    }

    try {
      delete_song($id, $db);
    }
    catch(PDOException $ex) {
      catch_error("The song failed to be deleted.", $ex);
    }

    /*
     * Deletes a song based on the given ID.
     * @param $album_name {String} - The name of the album.
     * @param $artist_name {String} - The name of the artist.
     * @param $db {PDO Object} - The PDO object for the referenced database.
    */
    function delete_song($id, $db) {
      $sql = "DELETE FROM Songs WHERE id=:id";
      $stmt = $db->prepare($sql);
      $params = array("id" => $id);
      $stmt->execute($params);
    }

?>
