<?php
// ==========================================
// CENTRALIZED DATABASE CONNECTION
// ==========================================

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "ink_and_solace";
$DB_PORT = 3307;

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

// Stop execution if connection fails
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: enforce UTF-8
$conn->set_charset("utf8mb4");
?>
