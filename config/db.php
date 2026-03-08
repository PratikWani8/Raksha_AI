<?php
session_start();

$host="raksha_db";
$user="raksha_user";
$pass="raksha_pass";
$db="raksha_ai";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Kolkata');
?>