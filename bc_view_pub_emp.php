<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_input = trim($_POST['publisher'] ?? "");
$employee_input  = trim($_POST['employee'] ?? "");

$found_employees = [];
$show_results_modal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conditions = [];
    $params = [];
    $types = "";

    /* =========================
       Publisher filter
       ========================= */
    if ($publisher_input !== "") {
        $conditions[] = "p.pub_name LIKE ?";
        $params[] = "%{$publisher_input}%";
        $types .= "s";
    }

    /* =========================
       Employee name filter
       (fname, lname, FULL NAME)
       ========================= */
    if ($employee_input !== "") {
        $conditions[] = "
            CONCAT(e.fname, ' ', e.lname) LIKE ?
            OR e.fname LIKE ?
            OR e.lname LIKE ?
        ";
        $params[] = "%{$employee_input}%";
        $params[] = "%{$employee_input}%";
        $params[] = "%{$employee_input}%";
        $types .= "sss";
    }

    /* =========================
       Base SQL
       ========================= */
    $sql = "
        SELECT 
            e.emp_id,
            e.fname,
            e.lname,
            j.job_desc,
            p.pub_id,
            p.pub_name
        FROM employee e
        INNER JOIN publishers p ON e.pub_id = p.pub_id
        LEFT JOIN jobs j ON e.job_id = j.job_id
    ";

    /* =========================
       Apply filters
       ========================= */
    if (!empty($conditions)) {
        $sql .= " WHERE (" . implode(" OR ", $conditions) . ")";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $found_employees[] = [
            "emp_id"    => $row['emp_id'],
            "pub_id"    => $row['pub_id'],
            "publisher" => $row['pub_name'],
            "name"      => $row['fname'] . " " . $row['lname'],
            "job"       => $row['job_desc'] ?? "N/A"
        ];
    }

    $stmt->close();
    $show_results_modal = true;
}
?>
