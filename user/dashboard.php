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

<style>

.chat-toggle-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 50%;
    width: 65px;
    height: 65px;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);
    z-index: 9999;
    transition: transform 0.3s;
}

.chat-toggle-btn:hover { transform: scale(1.1); }

.chatbot-wrapper {
    display: none;
    position: fixed;
    bottom: 95px;
    right: 20px; 
    width: 320px;
    height: 450px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
    animation: slideUp 0.3s ease-out;
    font-family: sans-serif;
}

.chat-header {
    background: #e91e63;
    color: white;
    padding: 15px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
}

.chat-content {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #fdf2f5;
}

.msg {
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    max-width: 80%;
    font-size: 14px;
    animation: messageFade 0.3s ease-on;
}

.bot-msg { background: #fff; border: 1px solid #eee; align-self: flex-start; }
.user-msg { background: #e91e63; color: white; align-self: flex-end; margin-left: auto; }

.chat-input-area {
    display: flex;
    align-items: center; 
    padding: 12px;
    background: white;
    border-top: 1px solid #eee;
    gap: 8px; 
}

.chat-input-area input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 25px; 
    outline: none;
    font-size: 14px;
    transition: border 0.3s;
}

.chat-input-area input:focus {
    border-color: #e91e63;
}

.send-btn {
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 50%; 
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(233, 30, 99, 0.3);
    transition: background 0.3s, transform 0.2s;
}

.send-btn:hover {
    background: #d81b60;
    transform: scale(1.05);
}

.send-btn svg {
    margin-left: 3px; 
}

.mic-btn {
    background: #fff;
    color: #e91e63;
    border: 1px solid #ddd;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.3s;
}

.mic-btn:hover {
    background: #fce4ec;
    transform: scale(1.05);
}

/* 🎤 Listening animation */
.mic-btn.listening {
    background: #e91e63;
    color: white;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

/* 📱 Responsive Chat */
@media (max-width: 480px) {
    .chatbot-wrapper {
        width: 95%;
        height: 80%;
        right: 2.5%;
        bottom: 80px;
    }

    .chat-input-area {
        padding: 8px;
    }

    .chat-input-area input {
        font-size: 13px;
        padding: 8px 12px;
    }

    .send-btn, .mic-btn {
        width: 36px;
        height: 36px;
    }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes messageFade {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}

</style>

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
    <a href="../ai/ai_heatmap.php"><button>🔥 AI Heatmap</button></a>
    <a href="../ai/safe_route.php"><button>🛣 Safe Route Navigation</button></a>
    <a href="../auth/logout.php"><button>🚪 Logout</button></a>
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

</body>
</html>