<?php
include("../db.php");
header('Content-Type: application/json');

// Get all schedules
$schedules = [];
$scheduleQ = $conn->query("SELECT ms.*, u.userName as createdByName FROM maintenanceschedule ms JOIN users u ON ms.createdBy = u.userID ORDER BY ms.scheduleID DESC");
while ($sch = $scheduleQ->fetch_assoc()) {
    $sch['items'] = [];
    // Get items for this schedule
    $itemQ = $conn->query("SELECT mi.*, ii.itemName FROM maintenanceitem mi JOIN inventoryitem ii ON mi.itemCode = ii.itemCode WHERE mi.scheduleID = '".$conn->real_escape_string($sch['scheduleID'])."'");
    while ($item = $itemQ->fetch_assoc()) {
        $item['records'] = [];
        // Get records for this item
        $recQ = $conn->query("SELECT mr.*, u.userName FROM maintenancerecord mr JOIN users u ON mr.userID = u.userID WHERE mr.maintainedItemID = '".$conn->real_escape_string($item['maintainedItemID'])."' ORDER BY mr.maintenanceDate DESC");
        while ($rec = $recQ->fetch_assoc()) {
            $item['records'][] = $rec;
        }
        $sch['items'][] = $item;
    }
    $schedules[] = $sch;
}
echo json_encode($schedules); 