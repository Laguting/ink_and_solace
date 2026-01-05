<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher = trim($_POST['publisher'] ?? "");
$title     = trim($_POST['title'] ?? "");
$insert_success = false;

if ($publisher && $title) {

    /* ============================
       1. GET OR CREATE PUBLISHER
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
        // Generate SEQUENTIAL pub_id
        $res = $conn->query("
            SELECT MAX(CAST(SUBSTRING(pub_id, 2) AS UNSIGNED)) AS max_id
            FROM publishers
        ");
        $row = $res->fetch_assoc();
        $nextPub = ($row['max_id'] ?? 0) + 1;
        $pub_id = 'P' . str_pad($nextPub, 3, '0', STR_PAD_LEFT);

        $stmtInsert = $conn->prepare(
            "INSERT INTO publishers (pub_id, pub_name)
             VALUES (?, ?)"
        );
        $stmtInsert->bind_param("ss", $pub_id, $publisher);
        $stmtInsert->execute();
        $stmtInsert->close();
    }
    $stmt->close();

    /* ============================
       2. GENERATE SEQUENTIAL title_id
       ============================ */
    $resTitle = $conn->query("
        SELECT MAX(CAST(SUBSTRING(title_id, 2) AS UNSIGNED)) AS max_id
        FROM titles
    ");
    $rowTitle = $resTitle->fetch_assoc();
    $nextTitle = ($rowTitle['max_id'] ?? 0) + 1;
    $title_id = 'T' . str_pad($nextTitle, 3, '0', STR_PAD_LEFT);

    /* ============================
       3. INSERT TITLE
       ============================ */
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
