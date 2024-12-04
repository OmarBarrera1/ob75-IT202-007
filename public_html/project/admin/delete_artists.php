<?php
require(__DIR__ . "/../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
session_start();
//UCID - ob75 - 12/04/2024
$id = se($_GET, "id", -1, false);

error_log("id: " . var_export($id, true));

if ($id > 0) {
    $db = getDB();
    try {
        // if there are relationships, delete from child tables first
        // alternatively, during FOREIGN KEY creation would could have used cascade delete
        $stmt = $db->prepare("DELETE FROM `Shazam-Artists` where id = :id");
        error_log("stmt: " . var_export($stmt, true));
        
        $stmt->execute([":id" => $id]);

        flash("Delete successful", "success");

    } catch (PDOException $e) {
        error_log("Error deleting: " . var_export($e, true));
        flash("There was an error deleting the record", "danger");
    }
}
unset($_GET["id"]);
$loc = get_url("search_artists.php")."?" . http_build_query($_GET);
error_log("Location: $loc");
die(header("Location: $loc"));