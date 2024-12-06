<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

?>

<?php
$id = se($_GET, "id", -1, false);

//UCID - ob75 - 12/05/2024

$artist = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT id, query_name, weburl, note, created, modified FROM `Shazam-Artists` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $artist = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("search_artists.php")));
}
foreach ($artist as $key => $value) {
    if (is_null($value)) {
        $artist[$key] = "N/A";
    }
}

//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Artist: <?php se($artist, "query_name", "Unknown"); ?></h3>
    <div>
    <?php if (has_role("Admin")) : ?>
        <a href="<?php echo get_url("admin/list_artists.php"); ?>" class="btn btn-secondary">Back</a>
        <a href="<?php echo get_url("admin/edit_artist.php?id=$id&"); ?>" class="btn btn-secondary">Edit</a>
        <a href="<?php echo get_url("admin/delete_artists.php?id=$id&"); ?>" class="btn btn-secondary">Delete</a>
    <?php else : ?>
    <a href="<?php echo get_url("search_artists.php"); ?>" class="btn btn-secondary">Back</a>
    <?php endif ; ?>
    </div>
    <!-- https://i.kym-cdn.com/entries/icons/original/000/029/959/Screen_Shot_2019-06-05_at_1.26.32_PM.jpg -->
    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?php se($artist, "Unknown"); ?> (<?php se($artist, "query_name"); ?>)</h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">ID: <?php se($artist, "id", "Unknown"); ?></li>
                    <li class="list-group-item">Artist Name: <?php se($artist, "query_name", "Unknown"); ?></li>
                    <li class="list-group-item">Web URL: <?php se($artist, "weburl", "Unknown"); ?></li>
                    <li class="list-group-item">Note: <?php se($artist, "note", "Unknown"); ?></li>
                </ul>

            </div>
        </div>
    </div>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>