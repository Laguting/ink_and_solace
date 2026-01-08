<?php
require_once "bc_db_connect.php";

// ==========================================================
// 1. ADDING A TITLE (T001 Format)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_title') {
    
    $pub_id = trim($_POST['pub_id']); 
    $title  = trim($_POST['title']);
    $au_id  = trim($_POST['au_id']);

    // --- A. PREVENT DUPLICATE TITLES ---
    // Checks if this title already exists for this specific publisher
    $check_title = $conn->prepare("SELECT title_id FROM titles WHERE title = ? AND pub_id = ?");
    $check_title->bind_param("ss", $title, $pub_id);
    $check_title->execute();
    if ($check_title->get_result()->num_rows > 0) {
        die("<script>alert('Error: This title already exists for this publisher.'); window.history.back();</script>");
    }

    // --- B. GENERATE SEQUENTIAL TITLE ID (T001) ---
    // Substring(title_id, 2) skips the 'T' and CAST turns '001' into the integer 1
    $res = $conn->query("SELECT title_id FROM titles WHERE title_id LIKE 'T%' 
                         ORDER BY CAST(SUBSTRING(title_id, 2) AS UNSIGNED) DESC LIMIT 1");
    
    if ($res && $res->num_rows > 0) {
        $last_id = $res->fetch_assoc()['title_id']; 
        $num = (int)substr($last_id, 1);           
        $gen_title_id = "T" . str_pad($num + 1, 3, "0", STR_PAD_LEFT); 
    } else {
        $gen_title_id = "T001"; 
    }

    // --- C. INSERT TITLE ---
    $sql_title = "INSERT INTO titles (title_id, title, pub_id, type, price, pubdate) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_title);
    
    $type = $_POST['type'] ?? 'General';
    $price = $_POST['price'] ?? 0.00;
    $pubdate = $_POST['pubdate'] ?? date('Y-m-d');

    $stmt->bind_param("ssssds", $gen_title_id, $title, $pub_id, $type, $price, $pubdate);
    
    if ($stmt->execute()) {
        // Link to author in titleauthor table
        $stmt_link = $conn->prepare("INSERT INTO titleauthor (au_id, title_id, au_ord) VALUES (?, ?, 1)");
        $stmt_link->bind_param("ss", $au_id, $gen_title_id);
        $stmt_link->execute();
        
        echo "<script>alert('Success! Title $gen_title_id added.'); window.location.href='index.php';</script>";
    }
}

// ==========================================================
// 2. ADDING AN AUTHOR (A001 Format via AJAX)
// ==========================================================
if(isset($_GET['ajax_add_author'])) {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    
    $f_name = trim($data['au_fname']);
    $l_name = trim($data['au_lname']);

    // --- A. PREVENT DUPLICATE AUTHORS ---
    $check_au = $conn->prepare("SELECT au_id FROM authors WHERE au_fname = ? AND au_lname = ?");
    $check_au->bind_param("ss", $f_name, $l_name);
    $check_au->execute();
    $res = $check_au->get_result();

    if ($res->num_rows > 0) {
        // Author already exists, return their current ID
        echo json_encode(["status" => "success", "au_id" => $res->fetch_assoc()['au_id']]);
        exit;
    }

    // --- B. GENERATE SEQUENTIAL AUTHOR ID (A001) ---
    $res_id = $conn->query("SELECT au_id FROM authors WHERE au_id LIKE 'A%' 
                            ORDER BY CAST(SUBSTRING(au_id, 2) AS UNSIGNED) DESC LIMIT 1");
    
    if ($res_id && $res_id->num_rows > 0) {
        $last_id = $res_id->fetch_assoc()['au_id']; 
        $num = (int)substr($last_id, 1);
        $gen_au_id = "A" . str_pad($num + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $gen_au_id = "A001";
    }

    // --- C. INSERT AUTHOR ---
    $ins_au = $conn->prepare("INSERT INTO authors (au_id, au_fname, au_lname, contract) VALUES (?, ?, ?, 1)");
    $ins_au->bind_param("sss", $gen_au_id, $f_name, $l_name);

    if ($ins_au->execute()) {
        echo json_encode(["status" => "success", "au_id" => $gen_au_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit;
}
?>