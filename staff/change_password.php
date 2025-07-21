<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

include("../connection/db2.php");

$userID = $_SESSION['userID'];
$current = $_POST['current'] ?? '';
$new = $_POST['new'] ?? '';

// Validate input
if (empty($current) || empty($new)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields.']);
    exit;
}

// Fetch current password
$stmt = $conn->prepare("SELECT userPassword FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($storedPassword);
$stmt->fetch();
$stmt->close();

// Compare passwords (plain text)
if ($storedPassword !== $current) {
    echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
    exit;
}

if ($storedPassword === $new) {
    echo json_encode(['success' => false, 'message' => 'New password must be different.']);
    exit;
}

// Update password
$update = $conn->prepare("UPDATE users SET userPassword = ? WHERE userID = ?");
$update->bind_param("si", $new, $userID);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
}
$update->close();
?>
