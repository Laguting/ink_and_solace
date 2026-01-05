<?php
require_once "bc_db_connect.php";

$show_modal = false;
$success_message = "";

// ==========================================================
// 1. ADD TITLE (NO DUPLICATES)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_title') {

    $conn->begin_transaction();

    try {
        /* ------------------------------------------------------
           A. CHECK DUPLICATE TITLE
        ------------------------------------------------------ */
        $chkTitle = $conn->prepare("SELECT title_id FROM titles WHERE title = ?");
        $chkTitle->bind_param("s", $_POST['title']);
        $chkTitle->execute();
        $res = $chkTitle->get_result();

        if ($res->num_rows > 0) {
            throw new Exception("Duplicate title already exists.");
        }
        $chkTitle->close();

        /* ------------------------------------------------------
           B. GENERATE title_id (SEQUENTIAL, SAFE)
        ------------------------------------------------------ */
        $res = $conn->query("SELECT title_id FROM titles ORDER BY title_id DESC LIMIT 1");
        if ($row = $res->fetch_assoc()) {
            $num = intval(substr($row['title_id'], 1)) + 1;
        } else {
            $num = 1;
        }
        $title_id = "T" . str_pad($num, 3, "0", STR_PAD_LEFT);

        /* ------------------------------------------------------
           C. INSERT TITLE
        ------------------------------------------------------ */
        $stmt = $conn->prepare("
            INSERT INTO titles
            (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssdiiisss",
            $title_id,
            $_POST['title'],
            $_POST['type'],
            $_POST['pub_id'],
            $_POST['price'],
            $_POST['advance'],
            $_POST['royalty'],
            $_POST['ytd_sales'],
            $_POST['notes'],
            $_POST['pubdate']
        );
        $stmt->execute();
        $stmt->close();

        /* ------------------------------------------------------
           D. LINK AUTHOR & TITLE (NO DUPLICATES)
        ------------------------------------------------------ */
        $chkLink = $conn->prepare("
            SELECT 1 FROM titleauthor WHERE au_id = ? AND title_id = ?
        ");
        $chkLink->bind_param("ss", $_POST['au_id'], $title_id);
        $chkLink->execute();
        if ($chkLink->get_result()->num_rows === 0) {

            $link = $conn->prepare("
                INSERT INTO titleauthor (au_id, title_id)
                VALUES (?, ?)
            ");
            $link->bind_param("ss", $_POST['au_id'], $title_id);
            $link->execute();
            $link->close();
        }
        $chkLink->close();

        $conn->commit();
        $show_modal = true;
        $success_message = "Complete entry successfully added.";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('".$e->getMessage()."');</script>";
    }
}

// ==========================================================
// 2. AJAX — ADD OR FIND AUTHOR (NO DUPLICATES)
// ==========================================================
if (isset($_GET['ajax_add_author'])) {

    header("Content-Type: application/json");
    $data = json_decode(file_get_contents("php://input"), true);

    $fname = trim($data['au_fname']);
    $lname = trim($data['au_lname']);

    /* ------------------------------------------------------
       A. CHECK EXISTING AUTHOR
    ------------------------------------------------------ */
    $chk = $conn->prepare("
        SELECT au_id FROM authors
        WHERE au_fname = ? AND au_lname = ?
    ");
    $chk->bind_param("ss", $fname, $lname);
    $chk->execute();
    $res = $chk->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "au_id"  => $row['au_id'],
            "message"=> "Existing author reused."
        ]);
        exit;
    }
    $chk->close();

    /* ------------------------------------------------------
       B. GENERATE au_id (SEQUENTIAL)
    ------------------------------------------------------ */
    $res = $conn->query("SELECT au_id FROM authors ORDER BY au_id DESC LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $num = intval(substr($row['au_id'], 1)) + 1;
    } else {
        $num = 1;
    }
    $au_id = "A" . str_pad($num, 3, "0", STR_PAD_LEFT);

    /* ------------------------------------------------------
       C. INSERT AUTHOR
    ------------------------------------------------------ */
    $stmt = $conn->prepare("
        INSERT INTO authors
        (au_id, au_lname, au_fname, phone, address, city, state, zip, contract)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssssis",
        $au_id,
        $lname,
        $fname,
        $data['phone'] ?? null,
        $data['address'] ?? null,
        $data['city'] ?? null,
        $data['state'] ?? null,
        $data['zip'] ?? null,
        $data['contract'] ?? 0
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "au_id" => $au_id]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}
?>
