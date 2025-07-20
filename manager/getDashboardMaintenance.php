<?php
include("../db.php");
header('Content-Type: application/json');

// Get all items needing repair
$repairItems = $conn->query("SELECT DISTINCT i.itemName 
                            FROM inventoryitem i
                            JOIN maintenanceitem m ON i.itemCode = m.itemCode
                            JOIN maintenancerecord r ON m.maintainedItemID = r.maintainedItemID
                            WHERE r.itemCondition = 'Needs Repair'");

// Get all items needing replacement
$replaceItems = $conn->query("SELECT DISTINCT i.itemName 
                             FROM inventoryitem i
                             JOIN maintenanceitem m ON i.itemCode = m.itemCode
                             JOIN maintenancerecord r ON m.maintainedItemID = r.maintainedItemID
                             WHERE r.itemCondition = 'Replace Soon'");

// Process results
$needsRepair = [];
$replaceSoon = [];

while($row = $repairItems->fetch_assoc()) {
    $needsRepair[] = $row['itemName'];
}

while($row = $replaceItems->fetch_assoc()) {
    $replaceSoon[] = $row['itemName'];
}

echo json_encode([
    'needsRepair' => $needsRepair,
    'repairCount' => count($needsRepair),
    'replaceSoon' => $replaceSoon,
    'replaceCount' => count($replaceSoon)
]);