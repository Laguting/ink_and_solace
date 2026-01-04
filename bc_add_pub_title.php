<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher = trim($_POST['publisher'] ?? "");
$title     = trim($_POST['title'] ?? "");
$insert_success = false;

if ($publisher && $title) {

    /* ============================
       1. CHECK IF PUBLISHER EXISTS
    ============================ */
    $stmt = $conn->prepare(
        "SELECT pub_id FROM publishers WHERE pub_name = ?"
    );
    $stmt->bind_param("s", $publisher);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Existing publisher
        $row = $result->fetch_assoc();
        $pub_id = $row['pub_id'];
    } else {
        // New publisher — generate UNIQUE pub_id
        do {
            $pub_id = "P" . rand(1000, 9999);
            $check = $conn->prepare(
                "SELECT 1 FROM publishers WHERE pub_id = ?"
            );
            $check->bind_param("s", $pub_id);
            $check->execute();
            $check->store_result();
            $exists = $check->num_rows > 0;
            $check->close();
        } while ($exists);

        $stmtInsert = $conn->prepare(
            "INSERT INTO publishers (pub_id, pub_name) VALUES (?, ?)"
        );
        $stmtInsert->bind_param("ss", $pub_id, $publisher);
        $stmtInsert->execute();
        $stmtInsert->close();
    }
    $stmt->close();

    /* ============================
       2. INSERT TITLE
    ============================ */
    do {
        $title_id = "T" . rand(10000, 99999);
        $check = $conn->prepare(
            "SELECT 1 FROM titles WHERE title_id = ?"
        );
        $check->bind_param("s", $title_id);
        $check->execute();
        $check->store_result();
        $exists = $check->num_rows > 0;
        $check->close();
    } while ($exists);

    $stmtTitle = $conn->prepare(
        "INSERT INTO titles
         (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, pubdate)
         VALUES (?, ?, 'Tech', ?, 0, 0, 0, 0, CURDATE())"
    );
    $stmtTitle->bind_param("sss", $title_id, $title, $pub_id);
    $stmtTitle->execute();
    $stmtTitle->close();

    $insert_success = true;
}

?>