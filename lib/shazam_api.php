<?php
    //UCID - ob75 - 11/25/2024
function fetch_artist($artName)
{
    $data = ["query" => $artName];
    $endpoint = "https://shazam-api6.p.rapidapi.com/shazam/search_artist/";
    $isRapidAPI = true;
    $rapidAPIHost = "shazam-api6.p.rapidapi.com";
    $result = get($endpoint, "SHAZAM_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
        //["status" => 200, "response" => '{"status":true,"result":{"artists":{"hits":[{"artist":{"avatar":"https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/6d/ad/28/6dad2828-52c4-01dc-8e33-3ad3c05b73fd/pr_source.png/800x800bb.jpg","name":"Bruno Mars","verified":false,"weburl":"https://music.apple.com/gb/artist/bruno-mars/278873078","adamid":"278873078"}},{"artist":{"avatar":"https://is1-ssl.mzstatic.com/image/thumb/Music115/v4/c5/1b/c2/c51bc234-a103-daa8-a6ab-d9ea05f9596d/57ffbb43-678a-400c-989b-199dfb49ebbb.jpg/800x800ac.jpg","name":"Berklee Bruno Mars Ensemble","verified":false,"weburl":"https://music.apple.com/gb/artist/berklee-bruno-mars-ensemble/1385890103","adamid":"1385890103"}},{"artist":{"avatar":"https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/1d/56/60/1d566036-a87d-b85c-fd82-0e6fcfae7abf/cover.jpg/800x800ac.jpg","name":"Mars","verified":false,"weburl":"https://music.apple.com/gb/artist/mars/1714459546","adamid":"1714459546"}},{"artist":{"avatar":"https://is1-ssl.mzstatic.com/image/thumb/Features125/v4/ff/2c/00/ff2c00aa-51ec-62f4-a4fc-c3149ff4e511/mzl.midlqcut.jpg/800x800bb.jpg","name":"Silk Sonic","verified":false,"weburl":"https://music.apple.com/gb/artist/silk-sonic/1556097160","adamid":"1556097160"}}]}}}'];

    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

 
    error_log("Data before being inserted" . var_export($result, true));

    if(isset($result["result"]["artists"]["hits"])){
        foreach ($result["result"]["artists"]["hits"] as $ar) {
            $artists[] = [  
            'query_name' => $ar["artist"]["name"], 
            'weburl' => $ar["artist"]["weburl"]
            
        ];
        
    }
    $result = $artists;
}
    return $result;

}

