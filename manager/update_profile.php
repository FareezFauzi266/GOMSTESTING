<?php
include("../connection.php"); // DB connection
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"];
    $username = $_POST["username"];
    $email = $_POST["email"];

    if (empty($user_id) || empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("sss", $username, $email, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed.']);
    }

    $stmt->close();
    $conn->close();
}
?>
