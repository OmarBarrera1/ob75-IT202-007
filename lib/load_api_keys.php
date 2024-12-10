<?php
// string array containing env keys to lookup (this allows usage of multiple APIs)
$env_keys = ["SHAZAM_API_KEY"];
$ini = @parse_ini_file(".env");

$API_KEYS = [];
foreach ($env_keys as $key) {
    if ($ini && isset($ini[$key])) {
        //load local .env file
        $SHAZAM_API_KEY = $ini[$key];
        $API_KEYS[$key] = $SHAZAM_API_KEY;
    } else {
        //load from heroku env variables
        $SHAZAM_API_KEY = getenv($key);
        $API_KEYS[$key] = $SHAZAM_API_KEY;
    }
    if (!isset($API_KEYS[$key]) || !$API_KEYS[$key]) {
        error_log("Failed to load api key for env key $key");
    }
    unset($SHAZAM_API_KEY);
}