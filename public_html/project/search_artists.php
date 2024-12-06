<?php
require_once(__DIR__ . "/../../partials/nav.php");
// UCID - ob75 - 12/04/2024

//search before query
$artists = se($_GET, "query_name", "", false);
$webURL= se($_GET, "weburl", "", false);

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

$sql = "SELECT id, query_name, weburl FROM `Shazam-Artists` WHERE 1=1";
$params = [];

if (!empty($artists)) {
    $sql .= " AND query_name like :query_name";
    $params[":query_name"] = "%$artists%";
}

if (!empty($webURL)) {
    $sql .= " AND weburl like :weburl";
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

//error_log("Artists: " . var_export($artists, true));
//error_log("webUrl: " . var_export($webURL, true));
$ignore_columns = ["id", "created", "modified", "is_api"];
$table = [
    "data" => $results,
    //"artists" => "query_name",
    "ignored_columns" => $ignore_columns,
    "view_url" => get_url("view_artists.php")
];
error_log("Artists: " . var_export($results, true));
?>

<div class="container-fluid">
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
                    <?php render_button(["text" => "Search", "type" => "submit"]); ?>
                </div>
                <div class="col">
                    <a href="?" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <?php render_table($table); ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>