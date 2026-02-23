<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

$name  = $data['name'];
$email = $data['email'];

$stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {

    $user = $res->fetch_assoc();
    $_SESSION['user_id'] = $user['user_id'];

} else {

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, '')");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();

    $_SESSION['user_id'] = $stmt->insert_id;
}

echo "success";
?>