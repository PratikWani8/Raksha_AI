<?php
include("../config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Status - Raksha</title>
    <link rel="stylesheet" href="../style.css">
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
</head>
<body>
<header>
    <div class="header-container">
    <h2>📄 Complaint Status</h2>
    </div>
</header>
<div class="card">
<?php
$res = $conn->query(
    "SELECT * FROM complaints WHERE user_id=".$_SESSION['user_id']
);
if($res->num_rows == 0){
    echo "<p>No complaints submitted yet.</p>";
}
while($row = $res->fetch_assoc()){
    echo "
    <p>
        <strong>{$row['incident_type']}</strong><br>
        Status: <b>{$row['status']}</b><br>
        Date: {$row['reported_at']}
    </p>
    <hr>";
}
?>
<a href="dashboard.php"><button>⬅ Back</button></a>
</div>
</body>
</html>
