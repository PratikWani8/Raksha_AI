<?php
if(isset($_GET['lat']) && isset($_GET['lon'])){

    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon";

    $opts = [
        "http" => [
            "header" => "User-Agent: RakshaAI/1.0\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    echo $response;
}
?>