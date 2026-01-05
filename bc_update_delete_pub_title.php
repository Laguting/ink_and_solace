<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/bc_db_connect.php";
header("Content-Type: application/json");

$action   = $_POST['action'] ?? '';
$title_id = $_POST['title_id'] ?? '';

if ($action !== 'update' || $title_id === '') {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$title   = $_POST['title'] ?? '';
$pubdate = $_POST['pubdate'] ?? '';

$stmt = $conn->prepare(
    "UPDATE titles SET title = ?, pubdate = ? WHERE title_id = ?"
);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

$stmt->bind_param("sss", $title, $pubdate, $title_id);
$success = $stmt->execute();

echo json_encode([
    "success" => $success,
    "affected_rows" => $stmt->affected_rows
]);

$stmt->close();
