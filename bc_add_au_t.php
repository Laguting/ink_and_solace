<?php
require_once __DIR__ . "/bc_db_connect.php";

$author_search = "";
$title_search  = "";
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

        // ===========================
        // 2. INSERT AUTHOR (if new)
        // ===========================
        $stmtCheckAuthor = $conn->prepare(
            "SELECT au_id FROM authors WHERE au_fname = ? AND au_lname = ?"
        );
        $stmtCheckAuthor->bind_param("ss", $au_fname, $au_lname);
        $stmtCheckAuthor->execute();
        $authorResult = $stmtCheckAuthor->get_result();

        if ($authorResult->num_rows === 1) {
            $authorRow = $authorResult->fetch_assoc();
            $au_id = $authorRow['au_id']; // Existing author
        } else {
            // New author
            do {
                $au_id = "AU" . uniqid(rand(), true);
                $check = $conn->prepare("SELECT 1 FROM authors WHERE au_id = ?");
                $check->bind_param("s", $au_id);
                $check->execute();
                $check->store_result();
                $exists = $check->num_rows > 0;
                $check->close();
            } while ($exists);

            $stmtInsertAuthor = $conn->prepare(
                "INSERT INTO authors (au_id, au_fname, au_lname) VALUES (?, ?, ?)"
            );
            $stmtInsertAuthor->bind_param("sss", $au_id, $au_fname, $au_lname);
            $stmtInsertAuthor->execute();
            $stmtInsertAuthor->close();
        }
        $stmtCheckAuthor->close();

        // ===========================
        // 3. INSERT TITLE
        // ===========================
        do {
            $title_id = "T" . uniqid(rand(), true);
            $checkTitle = $conn->prepare("SELECT 1 FROM titles WHERE title_id = ?");
            $checkTitle->bind_param("s", $title_id);
            $checkTitle->execute();
            $checkTitle->store_result();
            $existsTitle = $checkTitle->num_rows > 0;
            $checkTitle->close();
        } while ($existsTitle);

        $pub_id = NULL; // Can be updated later
        $price  = 0.0;

        $stmtInsertTitle = $conn->prepare(
            "INSERT INTO titles (title_id, title, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
             VALUES (?, ?, ?, ?, 0, 0, 0, '', CURDATE())"
        );
        $stmtInsertTitle->bind_param("sssd", $title_id, $title_search, $pub_id, $price);
        $stmtInsertTitle->execute();
        $stmtInsertTitle->close();

        // ===========================
        // 4. LINK AUTHOR TO TITLE
        // ===========================
        $au_ord = 1;      // Order of the author
        $royaltyper = 10; // Default royalty %
        $stmtLink = $conn->prepare(
            "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper) VALUES (?, ?, ?, ?)"
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