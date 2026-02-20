<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
$username = $user['name'];
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Raksha</title>
    <link rel="stylesheet" href="../style.css?v=4">
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
    <h2>👩 User Dashboard</h2>
    </div>
</header>
<div class="card">
    <p>
        Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>!  
        Choose an action below:
    </p>
    <a href="report_complaint.php"><button>🚨 Report Complaint</button></a>
    <a href="send_sos.php"><button class="danger">📲 Send SOS</button></a>
    <a href="view_status.php"><button>📄 View Complaint Status</button></a>
    <a href="../auth/logout.php"><button>🚪 Logout</button></a>
</div>
</body>
</html>