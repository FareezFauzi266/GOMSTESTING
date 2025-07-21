<?php
include("../db.php");
header('Content-Type: application/json');

// Fetch TOTAL profit (all time)
$totalProfitQuery = $conn->query("SELECT SUM(paymentAmount) AS totalProfit FROM logpayment");
$totalProfit = $totalProfitQuery->fetch_assoc()['totalProfit'] ?? 0;

// Fetch THIS MONTH'S profit
$monthlyProfitQuery = $conn->query("
    SELECT SUM(paymentAmount) AS monthlyProfit 
    FROM logpayment 
    WHERE MONTH(createdAt) = MONTH(CURRENT_DATE()) 
    AND YEAR(createdAt) = YEAR(CURRENT_DATE())
");
$monthlyProfit = $monthlyProfitQuery->fetch_assoc()['monthlyProfit'] ?? 0;

$conn->close();

// Return data as JSON
echo json_encode([
    "totalProfit" => $totalProfit,
    "monthlyProfit" => $monthlyProfit
]);
?>