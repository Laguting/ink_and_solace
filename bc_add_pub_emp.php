<?php
require_once __DIR__ . "/bc_db_connect.php";

// Initialize variables
$publisher_search = "";
$employee_search  = "";
$insert_success   = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $publisher_search = trim($_POST['publisher'] ?? "");
    $employee_search  = trim($_POST['employee'] ?? "");

    if (!empty($publisher_search) && !empty($employee_search)) {

        /* ================================
           1. GET OR CREATE PUBLISHER
           ================================ */
        $stmtPub = $conn->prepare(
            "SELECT pub_id FROM publishers WHERE pub_name = ?"
        );
        $stmtPub->bind_param("s", $publisher_search);
        $stmtPub->execute();
        $pubResult = $stmtPub->get_result();

        if ($pubResult->num_rows === 1) {
            $pubRow = $pubResult->fetch_assoc();
            $pub_id = $pubRow['pub_id'];
        } else {

            /* ================================
               GENERATE SEQUENTIAL pub_id
               ================================ */
            $resultPub = $conn->query("
                SELECT MAX(CAST(SUBSTRING(pub_id, 2) AS UNSIGNED)) AS max_id
                FROM publishers
            ");
            $rowPub = $resultPub->fetch_assoc();
            $nextPubNum = ($rowPub['max_id'] ?? 0) + 1;
            $pub_id = 'P' . str_pad($nextPubNum, 3, '0', STR_PAD_LEFT);

            $stmtInsertPub = $conn->prepare(
                "INSERT INTO publishers (pub_id, pub_name)
                 VALUES (?, ?)"
            );
            $stmtInsertPub->bind_param("ss", $pub_id, $publisher_search);
            $stmtInsertPub->execute();
            $stmtInsertPub->close();
        }
        $stmtPub->close();

        /* ================================
           2. GET OR CREATE EMPLOYEE
           ================================ */
        $nameParts = explode(" ", $employee_search, 2);

        if (count($nameParts) === 2) {
            [$fname, $lname] = $nameParts;

            $stmtEmp = $conn->prepare(
                "SELECT emp_id FROM employee WHERE fname = ? AND lname = ?"
            );
            $stmtEmp->bind_param("ss", $fname, $lname);
            $stmtEmp->execute();
            $empResult = $stmtEmp->get_result();

            if ($empResult->num_rows === 1) {
                $empRow = $empResult->fetch_assoc();
                $emp_id = $empRow['emp_id'];
            } else {

                /* ================================
                   GENERATE SEQUENTIAL emp_id
                   ================================ */
                $resultEmp = $conn->query("
                    SELECT MAX(CAST(SUBSTRING(emp_id, 2) AS UNSIGNED)) AS max_id
                    FROM employee
                ");
                $rowEmp = $resultEmp->fetch_assoc();
                $nextEmpNum = ($rowEmp['max_id'] ?? 0) + 1;
                $emp_id = 'E' . str_pad($nextEmpNum, 3, '0', STR_PAD_LEFT);

                $stmtInsertEmp = $conn->prepare(
                    "INSERT INTO employee (emp_id, fname, lname, pub_id)
                     VALUES (?, ?, ?, ?)"
                );
                $stmtInsertEmp->bind_param(
                    "ssss",
                    $emp_id,
                    $fname,
                    $lname,
                    $pub_id
                );
                $stmtInsertEmp->execute();
                $stmtInsertEmp->close();
            }

            /* ================================
               3. LINK EMPLOYEE TO PUBLISHER
               ================================ */
            $stmtUpdate = $conn->prepare(
                "UPDATE employee SET pub_id = ? WHERE emp_id = ?"
            );
            $stmtUpdate->bind_param("ss", $pub_id, $emp_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            $insert_success = true;
            $publisher_search = "";
            $employee_search  = "";

        } else {
            echo "<script>alert('Please enter employee as: Firstname Lastname');</script>";
        }
    }
}

?>
