<?php
include("../db.php");
header('Content-Type: application/json');

// Get well-stocked items (quantity >= 50)
$wellStockItemsQuery = $conn->query("
    SELECT 
        itemName, 
        itemQuantity,
        itemPrice,
        itemCategory,
        (itemQuantity * itemPrice) as itemValue
    FROM inventoryitem 
    WHERE itemQuantity >= 50
    ORDER BY itemQuantity DESC
");

// Get low stock items (quantity < 50)
$lowStockQuery = $conn->query("
    SELECT 
        itemName, 
        itemQuantity,
        itemPrice,
        itemCategory,
        (itemQuantity * itemPrice) as itemValue
    FROM inventoryitem 
    WHERE itemQuantity < 50
    ORDER BY itemQuantity ASC
");

// Get inventory summary by category
$categoryQuery = $conn->query("
    SELECT 
        itemCategory, 
        SUM(itemQuantity) as totalQuantity,
        SUM(itemQuantity * itemPrice) as categoryValue
    FROM inventoryitem 
    GROUP BY itemCategory
");

// Get total stock
$totalStockValQuery = $conn->query("
    SELECT 
        SUM(itemQuantity * itemPrice) as stockValue
    FROM inventoryitem 
");


// Process data
$totalInventoryVal = $totalStockValQuery->fetch_assoc();
$lowStockItems = [];
$categories = [];
$quantities = [];
$values = [];

// Process well-stocked items
$wellStockItems = [];
while($row = $wellStockItemsQuery->fetch_assoc()) {
    $wellStockItems[] = $row;
}


// Process low stock items
while($row = $lowStockQuery->fetch_assoc()) {
    $lowStockItems[] = $row;
}

// Process category data
while($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row['itemCategory'];
    $quantities[] = (int)$row['totalQuantity'];
    $values[] = (float)$row['categoryValue'];
}

// convert totalValue to number 
// $wellStockedValue = $wellStocked['totalValue'] ?? 0;

// convert stockValue to number 
$totalStockValue = $totalInventoryVal['stockValue'] ?? 0;

echo json_encode([
    'categories' => $categories,
    'quantities' => $quantities,
    'values' => $values,
    'totalValue' => $totalStockValue,
    'totalItems' => array_sum($quantities),
    'wellStocked' => [
        'count' => count($wellStockItems),
        'items' => $wellStockItems
    ],
    'lowStockCount' => count($lowStockItems),
    'lowStockItems' => $lowStockItems
]);