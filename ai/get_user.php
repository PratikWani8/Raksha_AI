<?php

header('Content-Type: application/json');

include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["name"=>"","phone"=>""]);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT name, phone FROM users WHERE user_id='$user_id'";

$result = $conn->query($sql);

if($result && $row = $result->fetch_assoc()){
    echo json_encode([
        "name"=>$row['name'],
        "phone"=>$row['phone']
    ]);
} else {
    echo json_encode(["name"=>"","phone"=>""]);
}
?>