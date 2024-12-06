<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>
<?php

//UCID - ob75 - 12/04/2024

$id = se($_GET, "id", -1, false);
if (isset($_POST["query_name"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["query_name", "weburl", "note"])) {
            unset($_POST[$k]);
        }
        $artName = $_POST;
        error_log("Cleaned up POST: " . var_export($artName, true));
    }
    //insert data
    $db = getDB();
    $query = "UPDATE `Shazam-Artists` SET ";

    $params = [];
    //per record
    foreach ($artName as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$artist = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT id, query_name, weburl, note FROM `Shazam-Artists` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $artist= $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_artists.php")));
}
if ($artist) {
    $form = [
        ["type" => "text", "name" => "query_name", "placeholder" => "Artist Name", "label" => "Artist Name", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "weburl", "placeholder" => "Web URL", "label" => "Web URL", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "note", "placeholder" => "Note", "label" => "Note", "rules" => ["required" => "required"]],
    ];
    $keys = array_keys($artist);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $artist[$v["name"]];
        }
    }
}

?>
<div class="container-fluid">
    <h3>Edit Artists</h3>
    <div>
        <a href="<?php echo get_url("admin/list_artists.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>