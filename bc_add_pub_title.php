<?php
require_once __DIR__ . "/bc_db_connect.php";


$show_modal = false;
$success_message = "";

$show_modal = false;
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
    // --- STEP 2: ADD TITLE ---
    if (isset($_POST['action']) && $_POST['action'] == 'add_title') {
        $gen_title_id = "T" . rand(1000, 9999);

        $stmt = $conn->prepare("INSERT INTO titles (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, notes, pubdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
        $stmt->bind_param("ssssddiiss", 
            $gen_title_id, $_POST['title'], $_POST['type'], $_POST['pub_id'], 
            $_POST['price'], $_POST['advance'], $_POST['royalty'], $_POST['ytd_sales'], 
            $_POST['notes'], $_POST['pubdate']
        );

        if ($stmt->execute()) {
            $show_modal = true;
            $success_message = "Publisher & Title Successfully Added!";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// ==========================================================
// 3. AJAX HELPER: ADD OR FIND PUBLISHER (STEP 1)
// ==========================================================
if(isset($_GET['ajax_add_publisher'])) {
    error_reporting(0);
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);     $p_name = $data['pub_name'];

    // --- A. CHECK IF PUBLISHER EXISTS ---
    $check_stmt = $conn->prepare("SELECT pub_id FROM publishers WHERE pub_name = ?");
    $check_stmt->bind_param("s", $p_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "status" => "success", 
            "pub_id" => $row['pub_id'], 
            "message" => "Existing publisher found."
        ]);
        $check_stmt->close();
        exit; 
    }
     $check_stmt->close();

    // --- B. NOT FOUND: INSERT NEW ---
    $gen_id = "P" . rand(100, 999);
        
    $stmt = $conn->prepare("INSERT INTO publishers (pub_id, pub_name, city, state, country) VALUES (?, ?, ?, ?, ?)");
        
    $stmt->bind_param("sssss", 
        $gen_id, $data['pub_name'], $data['city'], $data['state'], $data['country']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "pub_id" => $gen_id]);
        } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    exit;
}
?>