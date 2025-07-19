<?php
include("../db.php");
header('Content-Type: application/json');

$recordID = isset($_GET['recordID']) ? $conn->real_escape_string($_GET['recordID']) : '';
if (!$recordID) {
    echo json_encode(['error' => 'Missing recordID']);
    exit;
}

$q = $conn->query("SELECT mr.*, u.userName, ii.itemName FROM maintenancerecord mr JOIN users u ON mr.userID = u.userID JOIN maintenanceitem mi ON mr.maintainedItemID = mi.maintainedItemID JOIN inventoryitem ii ON mi.itemCode = ii.itemCode WHERE mr.recordID = '$recordID' LIMIT 1");
if ($rec = $q->fetch_assoc()) {
    echo json_encode($rec);
} else {
    echo json_encode(['error' => 'Record not found']);
} 