<?php
include("../config/db.php");

$success = false;

if (isset($_POST['send'])) {

    $location = $_POST['location'];
    $message  = $_POST['msg'];

    if (!empty($location)) {

        $stmt = $conn->prepare(
            "INSERT INTO non_reg_sos (location, message)
             VALUES (?, ?)"
        );

        $stmt->bind_param("ss", $location, $message);
        $stmt->execute();

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Emergency SOS (Guest) - Raksha</title>

    <link rel="stylesheet" href="../style.css">

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
        .pulse {
        height: 100px;
        width: 100px;
        border-radius: 50%;
        margin: 40px auto;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;    
        animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,77,77,0.6); }
            70% { box-shadow: 0 0 0 15px rgba(255,77,77,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,77,77,0); }
        }
    </style>
</head>

<body>

<header>
<div class="header-container">
<h2>🚨 Emergency SOS (Without Login)</h2>
</div>
</header>


<div class="card">

<?php if ($success): ?>
<script>
alert("🚨 SOS ALERT SENT!\nAuthorities have been notified through the system.");
</script>
<p style="text-align:center; font-weight:bold; color:#d32f2f;">
        🚨 SOS Sent Successfully!
    </p>
<?php endif; ?>

<form method="post" id="sosForm">

<h4>Send SOS Alert by just shaking your phone.</h4>

<input type="hidden" name="location" id="location">

<label>Emergency Message</label>

<textarea name="msg" required>Help! I am in danger.</textarea>

<button type="button" class="danger pulse" onclick="sendSOS()">
SEND SOS
</button>

<input type="hidden" name="send" id="sendBtn">

</form>

</div>

<script>

function sendSOS() {

    if (!navigator.geolocation) {
        alert("❌ Location not supported");
        return;
    }

    navigator.geolocation.getCurrentPosition(

        function (pos) {

            document.getElementById("location").value =
                pos.coords.latitude + "," + pos.coords.longitude;

            document.getElementById("sendBtn").value = "1";

            document.getElementById("sosForm").submit();
        },

        function () {
            alert("❌ Please enable location access");
        }
    );
}

// Shake sos detection

let lastX = 0, lastY = 0, lastZ = 0;
let shakeThreshold = 15;   // Sensitivity
let lastShake = 0;

window.addEventListener("devicemotion", function (event) {

    let acc = event.accelerationIncludingGravity;

    if (!acc) return;

    let diffX = Math.abs(acc.x - lastX);
    let diffY = Math.abs(acc.y - lastY);
    let diffZ = Math.abs(acc.z - lastZ);

    if (diffX + diffY + diffZ > shakeThreshold) {

        let now = new Date().getTime();

        // Prevent multiple SOS
        if (now - lastShake > 5000) {

            lastShake = now;

            alert("📳 Shake detected! Sending SOS...");

            sendSOS();
        }
    }

    lastX = acc.x;
    lastY = acc.y;
    lastZ = acc.z;

});
</script>

</body>
</html>
