<?php
include("../db.php");
header('Content-Type: application/json');

function getWeekDates($anyDate = null) {
    $date = $anyDate ? strtotime($anyDate) : strtotime('today');
    $monday = strtotime('monday this week', $date);
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $dates[] = date('Y-m-d', strtotime("+{$i} day", $monday));
    }
    return $dates;
}

$scheduleID = isset($_GET['scheduleID']) ? $conn->real_escape_string($_GET['scheduleID']) : '';
$weekOf = isset($_GET['weekOf']) ? $_GET['weekOf'] : null;
if (!$scheduleID) {
    echo json_encode(['error' => 'Missing scheduleID']);
    exit;
}

$weekDates = getWeekDates($weekOf);

// Get items for this schedule
$items = [];
$itemQ = $conn->query("SELECT mi.*, ii.itemName, mi.daysOfWeek FROM maintenanceitem mi JOIN inventoryitem ii ON mi.itemCode = ii.itemCode WHERE mi.scheduleID = '$scheduleID'");

while ($item = $itemQ->fetch_assoc()) {
    $itemRow = [
        'maintainedItemID' => $item['maintainedItemID'],
        'itemName' => $item['itemName'],
        'daysOfWeek' => $item['daysOfWeek'],
        'matrix' => []
    ];
    // Get all records for this item for the week
    $records = [];
    $recQ = $conn->query("SELECT * FROM maintenancerecord WHERE maintainedItemID = '".$item['maintainedItemID']."' AND maintenanceDate BETWEEN '".$weekDates[0]."' AND '".$weekDates[6]."'");
    while ($rec = $recQ->fetch_assoc()) {
        $records[$rec['maintenanceDate']] = $rec;
    }
    // Find the last maintenance date before this week
    $lastRecQ = $conn->query("SELECT maintenanceDate FROM maintenancerecord WHERE maintainedItemID = '".$item['maintainedItemID']."' AND maintenanceDate < '".$weekDates[0]."' ORDER BY maintenanceDate DESC LIMIT 1");
    $lastDate = null;
    if ($lastRecQ && $lastRecQ->num_rows > 0) {
        $lastDate = $lastRecQ->fetch_assoc()['maintenanceDate'];
    }
    // For each day, determine if due and if record exists
    $lastDone = $lastDate;
    foreach ($weekDates as $date) {
        $due = false;
        if (!$lastDone) {
            $due = true; // never done before, so due
        } else {
            $daysSince = (strtotime($date) - strtotime($lastDone)) / 86400;
            $due = $daysSince >= 1; // frequency is now per selected day, so always due if selected
        }
        $hasRecord = isset($records[$date]);
        if ($hasRecord) $lastDone = $date;
        $itemRow['matrix'][] = [
            'date' => $date,
            'due' => $due,
            'record' => $hasRecord ? $records[$date] : null
        ];
    }
    $items[] = $itemRow;
}

echo json_encode([
    'weekDates' => $weekDates,
    'items' => $items
]); 