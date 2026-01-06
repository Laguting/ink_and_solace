<?php
// bc_view_au_title.php
require_once __DIR__ . "/bc_db_connect.php"; 

// 1. Initialize variables so they exist for the HTML file
$author_search = "";
$title_search  = "";
$has_results = false;
$show_no_data_modal = false;
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // --- UPDATE LOGIC (Updates 2 Tables) ---
    if ($_POST['action'] == 'update') {
        // 1. Update AUTHOR Table
        // Removed au_minit as it does not exist in your database schema
        $sql_author = "UPDATE authors SET 
                        au_lname=?, au_fname=?, phone=?, address=?, city=?, state=?, zip=?, contract=? 
                        WHERE au_id=?";
        $stmt1 = $conn->prepare($sql_author);
        
        // Type string: sssssssis (7 strings, 1 integer for contract, 1 string for au_id)
        $stmt1->bind_param("sssssssis", 
            $_POST['au_lname'], $_POST['au_fname'], $_POST['phone'], 
            $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['contract'], 
            $_POST['au_id']
        );
        $author_updated = $stmt1->execute();
        $stmt1->close();

        // 2. Update TITLE Table
        $sql_title = "UPDATE titles SET 
                      title=?, type=?, pub_id=?, price=?, advance=?, royalty=?, ytd_sales=?, notes=?, pubdate=? 
                      WHERE title_id=?";
        $stmt2 = $conn->prepare($sql_title);

        // FIXED: Changed bind_param types to ensure 'notes' (8th param) is a string 's'
        // Type string: sssddiisss
        // title(s), type(s), pub_id(s), price(d), advance(d), royalty(i), ytd_sales(i), notes(s), pubdate(s), title_id(s)
        $stmt2->bind_param("sssddiisss", 
            $_POST['title'], 
            $_POST['type'], 
            $_POST['pub_id'], 
            $_POST['price'], 
            $_POST['advance'], 
            $_POST['royalty'], 
            $_POST['ytd_sales'], 
            $_POST['notes'], 
            $_POST['pubdate'], 
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

    // --- DELETE LOGIC ---
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM authors WHERE au_id=?");
        $stmt->bind_param("s", $_POST['au_id']);
        
        if ($stmt->execute()) { echo json_encode(["status" => "success"]); } 
        else { echo json_encode(["status" => "error", "message" => $stmt->error]); }
        $stmt->close(); exit;
    }
}

// ==========================================================
// 3. SEARCH LOGIC (JOIN via titleauthor bridge table)
// ==========================================================
$author_search = "";
$title_search = "";
$has_results = false;
$show_no_data_modal = false;
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $author_search = trim($_POST['author_search'] ?? "");
    $title_search = trim($_POST['title_search'] ?? "");
    
    if(!empty($author_search) || !empty($title_search)){
        // Join via the junction table since au_id is not in titles
        $sql = "SELECT t.*, a.* FROM titles t 
                JOIN titleauthor ta ON t.title_id = ta.title_id 
                JOIN authors a ON ta.au_id = a.au_id 
                WHERE (
                    a.au_lname LIKE ? 
                    OR a.au_fname LIKE ? 
                    OR CONCAT(a.au_fname, ' ', a.au_lname) LIKE ?
                ) OR t.title LIKE ?";
        
        $stmt = $conn->prepare($sql);
        $term_a = "%" . $author_search . "%";
        $term_t = "%" . $title_search . "%";
        
        $param_a = empty($author_search) ? "NO_MATCH_XYZ" : $term_a;
        $param_t = empty($title_search) ? "NO_MATCH_XYZ" : $term_t;
        if(empty($author_search)) $param_a = $param_t; 
        if(empty($title_search)) $param_t = $param_a;

        $stmt->bind_param("ssss", $param_a, $param_a, $param_a, $param_t);
        
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $has_results = true;
            while($row = $res->fetch_assoc()) {
                $results_list[] = $row;
            }
        } else {
            $show_no_data_modal = true;
        }
        $stmt->close();
    }
}
?>