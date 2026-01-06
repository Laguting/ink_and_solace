<?php
require_once "bc_db_connect.php";

$show_modal = false;
$success_message = "";

// ==========================================================
// 1. ADD EMPLOYEE (STEP 2 FORM SUBMIT)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['action'])
    && $_POST['action'] === 'add_employee'
) {

    $conn->begin_transaction();

    try {
        /* ------------------------------------------------------
           A. GENERATE emp_id (SEQUENTIAL)
        ------------------------------------------------------ */
        $res = $conn->query("SELECT emp_id FROM employees ORDER BY emp_id DESC LIMIT 1");
        if ($row = $res->fetch_assoc()) {
            $num = intval(substr($row['emp_id'], 1)) + 1;
        } else {
            $num = 1;
        }
        $emp_id = "E" . str_pad($num, 3, "0", STR_PAD_LEFT);

        /* ------------------------------------------------------
           B. INSERT EMPLOYEE
        ------------------------------------------------------ */
        $stmt = $conn->prepare("
            INSERT INTO employees
            (emp_id, fname, minit, lname, job_id, job_lvl, pub_id, hire_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $job_id = 1; // default job

        $stmt->bind_param(
            "ssssiiss",
            $emp_id,
            $_POST['fname'],
            $_POST['minit'],
            $_POST['lname'],
            $job_id,
            $_POST['job_lvl'],
            $_POST['pub_id'],
            $_POST['hire_date']
        );

        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $show_modal = true;
        $success_message = "Publisher and Employee successfully added.";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('".$e->getMessage()."');</script>";
    }
}

// ==========================================================
// 2. AJAX — ADD OR FIND PUBLISHER (NO DUPLICATES)
// ==========================================================
if (isset($_GET['ajax_add_publisher'])) {

    header("Content-Type: application/json");
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) exit;

    $pub_name = trim($data['pub_name']);

    /* ------------------------------------------------------
       A. CHECK EXISTING PUBLISHER
    ------------------------------------------------------ */
    $chk = $conn->prepare("
        SELECT pub_id FROM publishers
        WHERE pub_name = ?
    ");
    $chk->bind_param("s", $pub_name);
    $chk->execute();
    $res = $chk->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "pub_id" => $row['pub_id'],
            "message"=> "Existing publisher reused."
        ]);
        exit;
    }
    $chk->close();

    /* ------------------------------------------------------
       B. GENERATE pub_id (SEQUENTIAL)
    ------------------------------------------------------ */
    $res = $conn->query("SELECT pub_id FROM publishers ORDER BY pub_id DESC LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $num = intval($row['pub_id']) + 1;
    } else {
        $num = 1000;
    }
    $pub_id = (string)$num;

    /* ------------------------------------------------------
       C. INSERT PUBLISHER
    ------------------------------------------------------ */
    $stmt = $conn->prepare("
        INSERT INTO publishers
        (pub_id, pub_name, city, state, country)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssss",
        $pub_id,
        $pub_name,
        $data['city'],
        $data['state'],
        $data['country']
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "pub_id" => $pub_id
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
