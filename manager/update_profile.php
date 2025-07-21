<?php
include("../connection.php");
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';

if (!$user_id || !$username || !$email) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET userName = ?, userEmail = ? WHERE userID = ?");
$stmt->bind_param("ssi", $username, $email, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
$stmt->close();
$conn->close();
