<?php
if(isset($_GET['place'])){
    $place = urlencode($_GET['place']);
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=$place";

    $opts = [
        "http" => [
            "header" => "User-Agent: RakshaApp\r\n"
        ]
    ];

    echo file_get_contents($url, false, stream_context_create($opts));
}