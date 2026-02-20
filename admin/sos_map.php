<?php
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$data = [];

$query = "

SELECT 
    s.location,
    s.message,
    s.sent_at
FROM emergency_sos s

UNION ALL

SELECT 
    n.location,
    n.message,
    n.sent_at
FROM non_reg_sos n

ORDER BY sent_at DESC

";

$res = $conn->query($query);

while ($row = $res->fetch_assoc()) {

    if ($row['location']) {

        [$lat, $lng] = explode(",", $row['location']);

        $data[] = [
            "lat"  => (float)$lat,
            "lng"  => (float)$lng,
            "msg"  => htmlspecialchars($row['message']),
            "time" => $row['sent_at']
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SOS Map View - Raksha</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />

    <!-- META TAGS -->
    <meta name="title" content="Raksha - Women Safety & Emergency Protection System">
    <meta name="description" content="Raksha is a smart women safety platform for SOS alerts, emergency support, live location sharing, and nearby police assistance. Stay safe, stay empowered.">

    <meta name="keywords" content="women safety, SOS alert system, emergency help for women, Raksha safety app, women security platform">

    <meta name="author" content="Raksha Team">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Raksha - Women Safety & Emergency Protection System">
    <meta property="og:description" content="Smart platform for women's safety with instant SOS alerts, live tracking, and police support.">

    <meta name="theme-color" content="#e91e63">

    <link rel="stylesheet" href="../style.css?v=6">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <style>
        #map {
            height: 350px;
            width: 100%;
            border-radius: 12px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<header>
    <div class="header-container">
        <h2>🗺 SOS Location Map</h2>
    </div>
</header>

<div class="card">

    <div id="map"></div>

    <a href="sos_panel.php">
        <button>⬅ Back to SOS Panel</button>
    </a>

</div>


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

const sosData = <?php echo json_encode($data); ?>;

const TEN_MINUTES = 10 * 60 * 1000;
const now = new Date().getTime();

const map = L.map('map').setView([20.5937, 78.9629], 5);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

sosData.forEach(s => {

    const sosTime = new Date(s.time).getTime();
    const age = now - sosTime;

    if (age > TEN_MINUTES) return;

    L.marker([s.lat, s.lng]).addTo(map)
        .bindPopup(`
            <b>🚨 SOS Alert</b><br>
            ${s.msg}<br>
            <small>${s.time}</small>
        `);

});

setTimeout(() => location.reload(), 30000);

</script>

</body>
</html>