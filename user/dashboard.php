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
    <link rel="stylesheet" href="dashboard.css">
       <!-- META TAGS -->
    <meta charset = "UTF-8" />
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
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />

</head>
<body>

    <header>
    <div class="header-container">
    <h2>👩 User Dashboard</h2>
    </div>
</header>
    <p style="color: black; text-align: center; margin-top:30px; margin-bottom: 30px; font-size: 19px;">
        Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>!  
        Choose an action below:
    </p>

<div class="glass-container">

<a href="report_complaint.php" class="glass-card">
    <i data-lucide="message-circle"></i>
    <div class="label">Complaint</div>
</a>

<a href="send_sos.php" class="glass-card danger">
    <i data-lucide="alert-triangle"></i>
    <div class="label">SOS</div>
</a>

<a href="view_status.php" class="glass-card">
    <i data-lucide="file-text"></i>
    <div class="label">Status</div>
</a>

<a href="../ai/ai_heatmap.php" class="glass-card">
    <i data-lucide="flame"></i>
    <div class="label">Heatmap</div>
</a>

<a href="../ai/safe_route.php" class="glass-card">
    <i data-lucide="map"></i>
    <div class="label">Route</div>
</a>

<a href="../ai/ai_fir_gen.php" class="glass-card">
    <i data-lucide="file-plus"></i>
    <div class="label">FIR</div>
</a>

<a href="../auth/logout.php" class="glass-card logout">
    <i data-lucide="log-out"></i>
    <div class="label">Logout</div>
</a>
</div>


<div id="chat-container" class="chatbot-wrapper">
    <div class="chat-header">
        <span>🤖 Raksha AI</span>
        
    </div>
    <div id="chat-box" class="chat-content" style="display: flex; flex-direction: column;">
        <div class="msg bot-msg">Hello! I am Raksha Your Personal Smart Safety Companion.</div>
    </div>
    <div class="chat-input-area">
    <input type="text" id="user-input" placeholder="Type here..." onkeypress="if(event.key==='Enter') sendMessage()">
    <button class="mic-btn" onclick="startVoiceInput()">
    <svg viewBox="0 0 24 24" width="22" height="22">
        <path fill="currentColor" d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 14 0h-2zm-5 8a7 7 0 0 0 7-7h-2a5 5 0 0 1-10 0H5a7 7 0 0 0 7 7z"/>
    </svg>
</button>
    <button class="send-btn" onclick="sendMessage()">
        <svg viewBox="0 0 24 24" width="24" height="24">
            <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
        </svg>
    </button>
</div>
</div>

<button class="chat-toggle-btn" onclick="toggleChat()">🤖</button>

<script src="../ai/chatbot.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>