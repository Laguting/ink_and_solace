<?php
require_once __DIR__ . "/bc_db_connect.php";

$author_search  = "";
$title_search   = "";
$insert_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $author_search = trim($_POST['author'] ?? "");
    $title_search  = trim($_POST['title'] ?? "");

    if (!empty($author_search) && !empty($title_search)) {

        /* ===========================
           1. SPLIT AUTHOR NAME
           =========================== */
        $name_parts = explode(" ", $author_search, 2);
        $au_fname = $name_parts[0];
        $au_lname = $name_parts[1] ?? "Unknown";

        /* ===========================
           2. CHECK IF AUTHOR EXISTS
           =========================== */
        $stmtCheckAuthor = $conn->prepare(
            "SELECT au_id FROM authors WHERE au_fname = ? AND au_lname = ?"
        );
        $stmtCheckAuthor->bind_param("ss", $au_fname, $au_lname);
        $stmtCheckAuthor->execute();
        $authorResult = $stmtCheckAuthor->get_result();

        if ($authorResult->num_rows === 1) {
            $authorRow = $authorResult->fetch_assoc();
            $au_id = $authorRow['au_id'];
        } else {

            /* ===========================
               3. GENERATE SEQUENTIAL au_id
               =========================== */
            $result = $conn->query("
                SELECT MAX(CAST(SUBSTRING(au_id, 3) AS UNSIGNED)) AS max_id
                FROM authors
            ");
            $row = $result->fetch_assoc();
            $nextNumber = ($row['max_id'] ?? 0) + 1;
            $au_id = 'AU' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $stmtInsertAuthor = $conn->prepare(
                "INSERT INTO authors (au_id, au_fname, au_lname)
                 VALUES (?, ?, ?)"
            );
            $stmtInsertAuthor->bind_param("sss", $au_id, $au_fname, $au_lname);
            $stmtInsertAuthor->execute();
            $stmtInsertAuthor->close();
        }
        $stmtCheckAuthor->close();

        /* ===========================
           4. GENERATE SEQUENTIAL title_id
           =========================== */
        $resultTitle = $conn->query("
            SELECT MAX(CAST(SUBSTRING(title_id, 2) AS UNSIGNED)) AS max_id
            FROM titles
        ");
        $rowTitle = $resultTitle->fetch_assoc();
        $nextTitleNum = ($rowTitle['max_id'] ?? 0) + 1;
        $title_id = 'T' . str_pad($nextTitleNum, 3, '0', STR_PAD_LEFT);

        /* ===========================
           5. INSERT TITLE
           =========================== */
        $pub_id = NULL;
        $price  = 0.0;

        $stmtInsertTitle = $conn->prepare(
            "INSERT INTO titles
             (title_id, title, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
             VALUES (?, ?, ?, ?, 0, 0, 0, '', CURDATE())"
        );
        $stmtInsertTitle->bind_param(
            "sssd",
            $title_id,
            $title_search,
            $pub_id,
            $price
        );
        $stmtInsertTitle->execute();
        $stmtInsertTitle->close();

        /* ===========================
           6. LINK AUTHOR TO TITLE
           =========================== */
        $au_ord = 1;
        $royaltyper = 10;

        $stmtLink = $conn->prepare(
            "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper)
             VALUES (?, ?, ?, ?)"
        );
        $stmtLink->bind_param("ssid", $au_id, $title_id, $au_ord, $royaltyper);
        $stmtLink->execute();
        $stmtLink->close();

        $insert_success = true;
        $author_search = "";
        $title_search  = "";
    }
}

?>