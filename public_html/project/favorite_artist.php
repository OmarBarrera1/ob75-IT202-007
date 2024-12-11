<?php
require_once(__DIR__ . "/../../partials/nav.php");
// UCID - ob75 - 12/04/2024

//search before query
$artists = se($_GET, "query_name", "", false);
$webURL = se($_GET, "weburl", "", false);

$column = se($_GET, "column", "", false);
$order = se($_GET, "order", "", false);

$columns = ["query_name", "weburl"];
$columnMap = array_map(function ($v) {
    return [$v => $v];
}, $columns);

if (!in_array($column, $columns)) {
    $column = "query_name";
}
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc";
}

//UCID - ob75 - 12/10/2024
$params = [];

$params[":user_id"] = get_user_id();

$assoc_check = "";
// Append the user_id for a join if the user is logged in
if (is_logged_in()) {
    // return a 1 or 0 based on whether or not this guide is watched by this user
    $assoc_check = " (SELECT IFNULL(count(1), 0) FROM `UserArtists` 
    WHERE user_id = :user_id and artist_id = sa.id LIMIT 1) as is_watched,";
    $params[":user_id"] = get_user_id();
}

$sql = "SELECT sa.id, query_name, $assoc_check weburl FROM `Shazam-Artists` sa
JOIN `UserArtists` ua on sa.id = ua.artist_id";
$where = " WHERE ua.user_id = :user_id";



if (!empty($artists)) {
    $where .= " AND query_name like :query_name";
    $params[":query_name"] = "%$artists%";
}

if (!empty($webURL)) {
    $where .= " AND weburl like :weburl";
    $params[":weburl"] = "%$webURL%";
}

$limit = 10;
if (isset($_GET["limit"]) && !is_nan($_GET["limit"])) {
    $limit = (int)$_GET["limit"];
    if ($limit < 0 || $limit > 100) {
        $limit = 10;
    }
}
//$sql .= " GROUP BY Shazam-Artists";
$sql .= $where;
$sql .= " ORDER BY $column $order";

$sql .= " LIMIT $limit";
$db = getDB();
$results = [];
try {
    $stmt = $db->prepare($sql);
    error_log("sql: " . var_export($sql, true));
    error_log("params: " . var_export($params, true));
    $stmt->execute($params); //[":title" => "%$title%", ":type" => $type, ":domain"=>$provider, ":topic"=>$topic]);


    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (Exception $e) {
    error_log(var_export($e, true));
    error_log("fun happened");
    flash("Failed to fetch");
}

//UCID - ob75 - 12/10/2024
$total = 0;
$sql = "SELECT COUNT(DISTINCT sa.id) as c FROM `Shazam-Artists` as sa
JOIN `UserArtists` ua on ua.artist_id = sa.id
$where";

try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    /*if (isset($params[":user_id"])) {
        unset($params[":user_id"]);
    }
    */
    $stmt->execute($params);
    $r = $stmt->fetch();
    if ($r) {
        $total = (int)$r["c"]; // called my virtual/temp column "c" for count
    }
} catch (PDOException $e) {
    flash("Error fetching count", "danger");
    error_log("Error fetching count: " . var_export($e, true));
    error_log("Query: $sql");
    error_log("Params: " . var_export($params, true));
}

//error_log("Artists: " . var_export($artists, true));
//error_log("webUrl: " . var_export($webURL, true));
$ignore_columns = ["id", "created", "modified", "is_api"];
$table = [
    "data" => $results,
    //"artists" => "query_name",
    "ignored_columns" => $ignore_columns,
    "view_url" => get_url("view_artists.php"),
];


error_log("Artists: " . var_export($results, true));
?>

<div class="container-fluid">
    <h5>Favorite Artists</h5>
    <div>
        <form>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "query_name", "label" => "Artists", "value" => $artists]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "weburl", "label" => "WebURL", "value" => $webURL]); ?>
                </div>

            </div>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "column", "label" => "Sort", "value" => $column, "type" => "select", "options" => $columnMap]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "order", "label" => "Order", "value" => $order, "type" => "select", "options" => [["asc" => "asc"], ["desc" => "desc"]]]); ?>
                </div>
                <div class="col">
                    <?php render_input(["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]) ?>
                </div>
                <div class="col">
                    <?php render_button(["text" => "Search", "type" => "submit"]); ?>
                </div>

                <div class="col">
                    <a href="?" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col">
            Results <?php echo count($results) . "/" . $total; ?>
        </div>
    </div>
    <!--UCID - ob75 - 12/10/2024 -->
    <?php if (has_role("Admin")) : ?>
    <div class="row">
        <div class="col">
            <a class="btn btn-warning" href="api/clear_watched.php">Clear List</a>

        </div>
    </div>
    <?php endif; ?>
    <?php render_table($table); ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>