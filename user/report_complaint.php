<?php
include("../config/db.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['submit'])) {

    $type = $_POST['type'];
    $desc = $_POST['desc'];
    $location = $_POST['location'];
    $fileName = "";

    if (!empty($_FILES['evidence']['name'])) {

        $allowedTypes = ['image/jpeg', 'image/png', 'audio/mpeg'];
        $maxSize = 2 * 1024 * 1024;

        if ($_FILES['evidence']['size'] > $maxSize) {
            die("❌ File size exceeds 2MB limit");
        }

        if (!in_array($_FILES['evidence']['type'], $allowedTypes)) {
            die("❌ Invalid file type");
        }

        $ext = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
        $fileName = bin2hex(random_bytes(10)) . "." . $ext;

        move_uploaded_file(
            $_FILES['evidence']['tmp_name'],
            "../uploads/" . $fileName
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO complaints 
        (user_id, incident_type, description, location, evidence)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issss",
        $_SESSION['user_id'],
        $type,
        $desc,
        $location,
        $fileName
    );

    $stmt->execute();

    $sheetURL = "https://script.google.com/macros/s/AKfycbxQPNz0qWhOBUMl9131kklTQUAB3p9JH6-uH4oQCcaSundLVjzhBKHJdmxMGE6ogZC2/exec";

    $sheetData = [
        "user_id"  => $_SESSION['user_id'],
        "type"     => $type,
        "desc"     => $desc,
        "location" => $location,
        "evidence" => $fileName
    ];

    $ch = curl_init($sheetURL);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sheetData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_exec($ch);
    curl_close($ch);

    echo "<script>alert('✅ Complaint submitted successfully');</script>";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Report Complaint - Raksha</title>
    <meta charset = "UTF-8" />
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
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <div class="header-container">
    <h2>🚨 Report Safety Complaint</h2>
    </div>
</header>
<div class="card">
<form method="post" enctype="multipart/form-data">
    <label>Incident Type</label>
    <input type="text" name="type" placeholder="Harassment / Threat / Abuse" required>
    <label>Description</label>
    <textarea name="desc" rows="4" placeholder="Describe the incident" required></textarea>
    <label>Live Location</label>
    <input type="hidden" name="location" id="location">
    <button type="button" onclick="getLocation()">📍 Capture Live Location</button>
    <label>Upload Evidence (Image / Audio)</label>
    <input type="file" name="evidence" accept="image/*,audio/*">
    <button type="submit" name="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 25px;">Submit Complaint</button>
</form>
<a href="dashboard.php"><button>⬅ Back to Dashboard</button></a>
</div>
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                document.getElementById("location").value =
                    pos.coords.latitude + "," + pos.coords.longitude;
                alert("📍 Location captured successfully");
            },
            function() {
                alert("❌ Location permission denied");
            }
        );
    } else {
        alert("Geolocation not supported");
    }
}
</script>
</body>
</html>