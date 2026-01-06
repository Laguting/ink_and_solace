<?php
require_once __DIR__ . "/bc_db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // --- 1. UPDATE LOGIC ---
    if ($_POST['action'] == 'update') {
        // Update AUTHOR (au_minit removed because it does not exist in your schema)
        $sql_author = "UPDATE authors SET 
                        au_lname=?, au_fname=?, phone=?, address=?, city=?, state=?, zip=?, contract=? 
                        WHERE au_id=?";
        $stmt1 = $conn->prepare($sql_author);
        $stmt1->bind_param("sssssssis", 
            $_POST['au_lname'], $_POST['au_fname'], $_POST['phone'], 
            $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['contract'], 
            $_POST['au_id']
        );
        $author_updated = $stmt1->execute();
        $stmt1->close();

        // Update TITLE (Notes set to 's' for string to allow text updates)
        $sql_title = "UPDATE titles SET 
                      title=?, type=?, pub_id=?, price=?, advance=?, royalty=?, ytd_sales=?, notes=?, pubdate=? 
                      WHERE title_id=?";
        $stmt2 = $conn->prepare($sql_title);
        // Corrected types: sssddiisss (notes is the 8th 's')
        $stmt2->bind_param("sssddiisss", 
            $_POST['title'], $_POST['type'], $_POST['pub_id'], $_POST['price'], 
            $_POST['advance'], $_POST['royalty'], $_POST['ytd_sales'], $_POST['notes'], $_POST['pubdate'], 
            $_POST['title_id']
        );
        $title_updated = $stmt2->execute();
        $stmt2->close();

        if ($author_updated && $title_updated) { 
            echo json_encode(["status" => "success"]); 
        } else { 
            echo json_encode(["status" => "error", "message" => $conn->error]); 
        }
        exit;
    }

    // --- 2. DELETE LOGIC ---
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM authors WHERE au_id=?");
        $stmt->bind_param("s", $_POST['au_id']);
        if ($stmt->execute()) { echo json_encode(["status" => "success"]); } 
        else { echo json_encode(["status" => "error", "message" => $stmt->error]); }
        $stmt->close(); exit;
    }
}

// --- 3. SEARCH LOGIC (FIXED JOIN) ---
$author_search = "";
$title_search = "";
$has_results = false;
$show_no_data_modal = false; 
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $author_search = trim($_POST['author'] ?? $_POST['author_search'] ?? "");
    $title_search = trim($_POST['title'] ?? $_POST['title_search'] ?? "");
    
    if(!empty($author_search) || !empty($title_search)){
        // FIXED: JOINing through titleauthor bridge table
        $sql = "SELECT t.*, a.* FROM titles t 
                JOIN titleauthor ta ON t.title_id = ta.title_id 
                JOIN authors a ON ta.au_id = a.au_id 
                WHERE (a.au_lname LIKE ? OR a.au_fname LIKE ?) OR t.title LIKE ?";
        
        $stmt = $conn->prepare($sql);
        $term_author = "%" . $author_search . "%";
        $term_title = "%" . $title_search . "%";
        
        // Ensure parameters are mapped correctly to the 3 "?" placeholders
        if(!empty($author_search)) {
            $stmt->bind_param("sss", $term_author, $term_author, $term_title); 
        } else {
            // If only title is searched, match author placeholders to something that won't fail
            $stmt->bind_param("sss", $term_title, $term_title, $term_title);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $has_results = true;
            while($row = $result->fetch_assoc()) {
                $results_list[] = $row;
            }
        } else {
            $show_no_data_modal = true;
        }
        $stmt->close();
    }
}
?>