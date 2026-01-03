<?php
require 'db.php';

$sql = "SELECT t.title_id, t.title, t.type, p.pub_name 
        FROM titles t
        LEFT JOIN publishers p ON t.pub_id = p.pub_id";

$stmt = $pdo->query($sql);
$titles = $stmt->fetchAll();

echo json_encode($titles, JSON_PRETTY_PRINT);
?>
