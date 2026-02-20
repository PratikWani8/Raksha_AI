<?php
include("../config/db.php");
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit;
}
$id = $_GET['id'];
if(isset($_POST['update'])){
    $status = $_POST['status'];
    $conn->query(
        "UPDATE complaints SET status='$status' WHERE complaint_id=$id"
    );
    $res = $conn->query(
        "SELECT u.phone FROM users u 
         JOIN complaints c ON u.user_id=c.user_id 
         WHERE c.complaint_id=$id"
    );
    $user = $res->fetch_assoc();
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Status - Raksha</title>
    <link rel="stylesheet" href="../style.css?v=3">
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
    <h2>🔄 Update Complaint Status</h2>
    </div>
</header>
<div class="card">
<form method="post">
    <label>Status</label>
    <select name="status">
        <option>Pending</option>
        <option>In Progress</option>
        <option>Resolved</option>
    </select>
    <button name="update">Update</button>
</form>
</div>
</body>
</html>
