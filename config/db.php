<?php
$conn = new mysqli("localhost", "root", "", "women_safety");
if ($conn->connect_error) {
    die("Database Connection Failed");
}
session_start();
?>
