<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
// UCID - ob75 - 11/25/2024

if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $artists = [];
    
        if ($action === "fetch") {
            $artName = se($_POST, "artists", "", false); 
            if ($artName) {
            $result = fetch_artist($artName); 
            error_log("Data from API" . var_export($result, true));
            if ($result){
                $artists = array_map(function ($item) {
                    $item["is_api"] = 1;
                    return $item;
                }, $result);
            }
        }
        }else if ($action === "create") {
            $necessaryColumns = ["query_name", "weburl"];
            $artists = $_POST; 
            
           
            foreach ($artists as $k => $v) {
                
                if (!in_array($k, $necessaryColumns)) {
                    unset($artists[$k]);
                    
                }
            }
            $artists = [$artists];
            
            error_log("Cleaned up POST: " . var_export($artists, true)); 
        
     } else {
        flash("You must provide a name", "warning");
        
     }

    try {
        $result = insert("Shazam-Artists", $artists, ["update_duplicate" => true]);

        if (!$result) {
            flash("Unhandled Error", "warning");
        } else {
            flash("Successfully Inserted", "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this name already exists", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Create or Fetch Artists</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            
            <?php render_input(["type" => "search", "name" => "artists", "placeholder" => "Artist Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
        
            <?php render_input(["type" => "text", "name" => "query_name", "placeholder" => "Artist Name", "label" => "Artist Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "weburl", "placeholder" => "WebURL", "label" => "webURL", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>