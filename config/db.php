<?php
session_start();

$host = "raksha_db";       // Docker container name
$user = "raksha_user";     // from docker-compose
$pass = "raksha_pass";     // from docker-compose
$db   = "raksha_ai";       // from docker-compose

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Kolkata');
?>