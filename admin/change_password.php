<?php
include("../config/db.php");
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
$message = "";
if (isset($_POST['change'])) {
    $current = $_POST['current'];
    $new = $_POST['new'];
    $confirm = $_POST['confirm'];
    $res = $conn->query("SELECT password FROM admin WHERE username='admin'");
    $admin = $res->fetch_assoc();
    if (!password_verify($current, $admin['password'])) {
        $message = "❌ Current password is incorrect";
    }
    elseif ($new !== $confirm) {
        $message = "❌ New passwords do not match";
    }
    elseif (strlen($new) < 6) {
        $message = "❌ Password must be at least 6 characters";
    }
    else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin SET password=? WHERE username='admin'");
        $stmt->bind_param("s", $newHash);
        $stmt->execute();
        $message = "✅ Password changed successfully";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Admin Password - Raksha</title>
    <link rel="stylesheet" href="../style.css?v=3">

    <!-- META TAGS -->

    <meta charset = "UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
        #change-btn {
  width: 100%;
  padding: 12px;
  margin-top: 15px;
  border: none;
  background: var(--danger);
  color: white;
  font-size: 16px;
  border-radius: 25px;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.3s;
}

#change-btn:hover {
  background: #28a745;
  transform: scale(1.03);
}
</style>
</head>
<body>
<header>
    <div class="header-container">
    <h2>🔐 Change Admin Password</h2>
    </div>
</header>
<div class="card">
<form method="post">
    <?php if($message): ?>
        <p style="text-align:center;"><b><?php echo $message; ?></b></p>
    <?php endif; ?>
    <label>Current Password</label>
    <input type="password" name="current" required>
    <label>New Password</label>
    <input type="password" name="new" required>
    <label>Confirm New Password</label>
    <input type="password" name="confirm" required>
    <button name="change" id="change-btn">Change Password</button>
    <a href="dashboard.php">
        <button type="button">⬅ Back to Dashboard</button>
    </a>
</form>
</div>
</body>
</html>
