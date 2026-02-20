<?php
include("../config/db.php");
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SOS Alerts - Raksha</title>
    <link rel="stylesheet" href="../style.css?v=6">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />
    <style>
        .sos-alert {
            border-left: 6px solid #d32f2f;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            50% { background: #fff1f1; }
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
    <h2>🚨 SOS Alert Panel (Live)</h2>
    </div>
</header>
<div class="card">
    <h3>Active SOS Alerts</h3>
    <div id="sosContainer">
        <p>Loading SOS alerts...</p>
    </div>
    <a href="sos_map.php">
        <button class="danger">🗺 View SOS Map</button>
    </a>
    <a href="dashboard.php">
        <button>⬅ Back to Dashboard</button>
    </a>
</div>
<audio id="sosSound">
    <source src="../assets/alert.mp3" type="audio/mpeg">
</audio>
<script>
let lastCount = 0;
function isWithinOneHour(sentTime) {
    const sentDate = new Date(sentTime);
    const now = new Date();
    const diffMinutes = (now - sentDate) / (1000 * 60);
    return diffMinutes <= 10;
}
function loadSOS() {
    fetch("fetch_sos.php")
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("sosContainer");
            container.innerHTML = "";
            if (data.length > lastCount && lastCount !== 0) {
                alert("🚨 NEW SOS ALERT RECEIVED!");
                document.getElementById("sosSound").play();
            }
            lastCount = data.length;
            let visibleCount = 0;
            data.forEach(s => {
                if (!isWithinOneHour(s.sent_at)) return;
                visibleCount++;

        let userInfo = "";

if (s.type === "Registered") {
    userInfo = `
        <b>User:</b> ${s.name}<br>
        <b>Phone:</b> ${s.phone}<br>
    `;
} else {
    userInfo = `
        <b>User:</b> Guest User<br>
    `;
}

container.innerHTML += `
<div class="card sos-alert">
<p>
<b>SOS ID:</b> ${s.sos_id}<br>
${userInfo}
<b>Message:</b> ${s.message}<br>
<b>Location:</b> ${s.location}<br>
<small>${s.sent_at}</small>
</p>
</div>
`;
            });
            if (visibleCount === 0) {
                container.innerHTML = "<p>No active SOS alerts (older than 10 minutes).</p>";
            }
        });
}
loadSOS();
setInterval(loadSOS, 5000);
</script>
</body>
</html>