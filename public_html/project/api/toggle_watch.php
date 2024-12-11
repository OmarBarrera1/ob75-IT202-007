<?php
session_start();
require(__DIR__ . "/../../../lib/functions.php");

//UCID - ob75 - 12/10/2024
if (isset($_POST["toggleWatched"])) {
    $artistId = se($_POST, "artistId", -1, false);
    $userId = get_user_id();
    if ($userId) {
        $db = getDB();
        $params = [":artist_id" => $artistId, ":user_id" => $userId];
        $needsDelete = false;
        try {
            $stmt = $db->prepare("INSERT INTO `UserArtists`(artist_id, user_id)
            VALUES (:artist_id, :user_id)");
            $stmt->execute($params);
            flash("Added to watch list", "success");
        } catch (PDOException $e) {
            // use duplicate error as a delete trigger
            if ($e->errorInfo[1] == 1062) {
                $needsDelete = true;
            } else {
                flash("Error adding item to watch list", "danger");
                error_log("Error adding watch: " . var_export($e, true));
            }
        }
        if ($needsDelete) {
            try {
                $stmt = $db->prepare("DELETE FROM `UserArtists` WHERE artist_id = :artist_id AND user_id = :user_id");
                $stmt->execute($params);
                flash("Removed from watch list", "success");
            } catch (PDOException $e) {
                flash("Error removing item from watch list", "danger");
                error_log("Error removing watch: " . var_export($e, true));
            }
        }
    } else {
        flash("You must be logged in to do this action", "warning");
    }
    // Note: chose not to use $_SERVER["HTTP_REFERER"] as it may not always be accurate
    die(header("Location: " . $_POST["route"]));
}
flash("Error toggling watched", "danger");
die(header("Location: " . get_url("home.php")));