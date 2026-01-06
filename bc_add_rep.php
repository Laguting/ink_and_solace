<?php
require_once __DIR__ . "/bc_db_connect.php";

$insert_success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $publisher_name = trim($_POST['publisher'] ?? ""); // publisher name from form
    $author_name    = trim($_POST['author'] ?? "");
    $book           = trim($_POST['books'] ?? "");
    $count          = intval($_POST['count'] ?? 0);

    if ($publisher_name && $book) {

        // ================= GET PUBLISHER ID =================
        $stmt = $conn->prepare("SELECT pub_id FROM publishers WHERE pub_name = ?");
        $stmt->bind_param("s", $publisher_name);
        $stmt->execute();
        $stmt->bind_result($pub_id);
        $stmt->fetch();
        $stmt->close();

        // If publisher doesn't exist, insert new publisher
        if (!$pub_id) {
            $pub_id = uniqid("P");
            $stmt = $conn->prepare("INSERT INTO publishers (pub_id, pub_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $pub_id, $publisher_name);
            $stmt->execute();
            $stmt->close();
        }

        // ================= INSERT TITLE =================
        $title_id = uniqid("T");
        $stmt = $conn->prepare(
            "INSERT INTO titles (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, pubdate)
             VALUES (?, ?, 'Tech', ?, 0, 0, 0, 0, CURDATE())"
        );
        $stmt->bind_param("sss", $title_id, $book, $pub_id);
        $stmt->execute();
        $stmt->close();

        // ================= INSERT AUTHOR =================
        if ($author_name) {
            $au_id = uniqid("AU");
            $name_parts = explode(" ", $author_name, 2);
            $au_fname = $name_parts[0];
            $au_lname = $name_parts[1] ?? "";

            // Insert author
            $stmt = $conn->prepare(
                "INSERT INTO authors (au_id, au_fname, au_lname) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $au_id, $au_fname, $au_lname);
            $stmt->execute();
            $stmt->close();

            // Link author to title with au_ord from form
            $stmt = $conn->prepare(
                "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper)
                VALUES (?, ?, ?, 10)"
            );
            $stmt->bind_param("ssi", $au_id, $title_id, $count);
            $stmt->execute();
            $stmt->close();
        }


        $insert_success = true;
    }
}

?>