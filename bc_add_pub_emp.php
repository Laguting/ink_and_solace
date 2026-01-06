<?php
require_once "bc_db_connect.php";

$show_modal = false;
$success_message = "";

/* ==========================================================
   HELPER: GET NEXT SEQUENTIAL ID
========================================================== */
function getNextId($conn, $table, $column, $prefix = "") {
    if ($prefix !== "") {
        $sql = "SELECT MAX(CAST(SUBSTRING($column, 2) AS UNSIGNED)) AS max_id FROM $table";
    } else {
        $sql = "SELECT MAX($column) AS max_id FROM $table";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $next = ($row['max_id'] ?? 0) + 1;

    if ($prefix !== "") {
        return $prefix . str_pad($next, 8, "0", STR_PAD_LEFT);
    }

    return $next;
}

/* ==========================================================
   ADD EMPLOYEE (SEQUENTIAL + NO DUPLICATES)
========================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['action'])
    && $_POST['action'] === 'add_employee') {

    // Check duplicate employee (same name + publisher)
    $dup = $conn->prepare(
        "SELECT emp_id FROM employee 
         WHERE fname=? AND minit=? AND lname=? AND pub_id=?"
    );
    $dup->bind_param(
        "ssss",
        $_POST['fname'],
        $_POST['minit'],
        $_POST['lname'],
        $_POST['pub_id']
    );
    $dup->execute();
    $dup->store_result();

    if ($dup->num_rows > 0) {
        die("<script>alert('Employee already exists under this publisher.');</script>");
    }
    $dup->close();

    // Generate sequential Emp ID
    $gen_emp_id = getNextId($conn, "employee", "emp_id", "E");

    $sql = "INSERT INTO employee 
            (emp_id, fname, minit, lname, job_id, job_lvl, pub_id, hire_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $job_id = 1;
    $stmt->bind_param(
        "ssssiiss",
        $gen_emp_id,
        $_POST['fname'],
        $_POST['minit'],
        $_POST['lname'],
        $job_id,
        $_POST['job_lvl'],
        $_POST['pub_id'],
        $_POST['hire_date']
    );

    if ($stmt->execute()) {
        $show_modal = true;
        $success_message = "Employee Successfully Added!";
    } else {
        echo "<script>alert('Insert Error: {$stmt->error}');</script>";
    }
    $stmt->close();
}

/* ==========================================================
   AJAX: ADD OR FIND PUBLISHER (SEQUENTIAL + NO DUPLICATES)
========================================================== */
if (isset($_GET['ajax_add_publisher'])) {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) exit;

    $pub_name = trim($data['pub_name']);

    // Check existing publisher
    $check = $conn->prepare(
        "SELECT pub_id FROM publishers WHERE pub_name = ?"
    );
    $check->bind_param("s", $pub_name);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        echo json_encode([
            "status" => "success",
            "pub_id" => $res->fetch_assoc()['pub_id']
        ]);
        exit;
    }
    $check->close();

    // Generate sequential Pub ID
    $gen_pub_id = getNextId($conn, "publishers", "pub_id");

    $stmt = $conn->prepare(
        "INSERT INTO publishers (pub_id, pub_name, city, state, country)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "issss",
        $gen_pub_id,
        $pub_name,
        $data['city'],
        $data['state'],
        $data['country']
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "pub_id" => $gen_pub_id
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }
    exit;
}
?>
