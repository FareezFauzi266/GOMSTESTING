<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "goms";

$conn = new mysqli($host, $username, $password, $database,3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
