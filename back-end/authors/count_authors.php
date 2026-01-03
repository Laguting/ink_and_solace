<?php
include 'config.php';

// Example: check authors table
$result = $conn->query("SELECT COUNT(*) AS total_authors FROM authors");
$row = $result->fetch_assoc();
echo "Total authors: " . $row['total_authors'];

$conn->close();
?>
