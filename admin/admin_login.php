<?php
include("../config/db.php"); 
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    if (empty($u) || empty($p)) {
        echo "<script>alert('All fields are required');</script>";
    }
    else {
        $res = $conn->query("SELECT * FROM admin WHERE username='$u'");

        if ($res && $res->num_rows === 1) {
            $a = $res->fetch_assoc();

            if (password_verify($p, $a['password'])) {
                $_SESSION['admin'] = true;
                header("Location: dashboard.php");
                exit;
            } else {
                echo "<script>alert('Invalid password');</script>";
            }
        } else {
            echo "<script>alert('Invalid admin login');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Raksha</title>
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
    <h2>👮 Admin Login</h2>
    </div>
</header>
<div class="card">
<form method="post" autocomplete="off">
    <label>Username</label>
    <input type="text" name="username" placeholder="Enter username" required>
    <label>Password</label>
    <input type="password" name="password" placeholder="Enter password" required>
    <button class="danger" type="submit" name="login">Login</button>
    <p style="color: grey; text-align: center;">Authorized Entries Only.</p>
</form>
</div>
<script>
document.querySelector("form").addEventListener("submit", () => {
    const btn = document.querySelector("button");
    btn.innerText = "Logging in...";
    btn.style.opacity = "0.8";
});
</script>
</body>
</html>
