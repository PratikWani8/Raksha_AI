<?php
include("../config/db.php"); 
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>alert('All fields are required');</script>";
    }

    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
    }

    else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {

            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];

                header("Location: ../user/dashboard.php");
                exit();

            } else {
                echo "<script>alert('Invalid password');</script>";
            }

        } else {
            echo "<script>alert('Email not registered');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login - Raksha</title>
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
    <style>
    .hero-container {
        text-align: center;
        color: black;
        padding: 5px;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        margin-top: 20px;
    }
    </style>
</head>
<body>
<header>
    <div class="header-container">
    <h2>🔐 User Login</h2>
    </div>
</header>
<div class="hero-container">
    <h2>Welcome Back!</h2>
    <p>Login to access your account and stay protected with Raksha.</p>
    </div>
<div class="card">
<form method="post" autocomplete="off">
    <label>Email</label>
    <input type="email" name="email" placeholder="Enter email" required>
    <label>Password</label>
    <input type="password" name="password" placeholder="Enter password" required>
    <button type="submit" name="login">Login</button>
    <p style="text-align:center; margin-top:15px;">
        New user? <a href="register.php" style="text-decoration: none;">Register here</a>
    </p>
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