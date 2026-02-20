<?php
include("../config/db.php");
$total = $conn->query("SELECT COUNT(*) total FROM complaints")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) total FROM complaints WHERE status='Pending'")->fetch_assoc()['total'];
$progress = $conn->query("SELECT COUNT(*) total FROM complaints WHERE status='In Progress'")->fetch_assoc()['total'];
$resolved = $conn->query("SELECT COUNT(*) total FROM complaints WHERE status='Resolved'")->fetch_assoc()['total'];
echo json_encode([
    "total" => $total,
    "pending" => $pending,
    "progress" => $progress,
    "resolved" => $resolved
]);
