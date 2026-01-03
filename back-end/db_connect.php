<?php
$host = 'localhost';
$db   = 'ink_and_solace';
$user = 'root';     // your MySQL username
$pass = '';         // your MySQL password
$charset = 'utf8mb4'
$port = 3307; // default MySQL port

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
