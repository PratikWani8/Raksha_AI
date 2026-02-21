<?php
$conn = new mysqli("localhost", "root", "", "raksha_ai");
if ($conn->connect_error) {
    die("Database Connection Failed");
}
session_start();
?>
