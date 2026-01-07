<?php
require_once __DIR__ . "/bc_db_connect.php";

$show_modal = false;
$success_message = "";

// --- Helper Function: Get Sequential Title ID (e.g., T4921 -> T4922) ---
function getNextTitleId($conn) {
    // Finds the highest numeric part after 'T'
    $res = $conn->query("SELECT title_id FROM titles WHERE title_id LIKE 'T%' ORDER BY CAST(SUBSTRING(title_id, 2) AS UNSIGNED) DESC LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $last_id = $res->fetch_assoc()['title_id'];
        $num = (int)substr($last_id, 1);
        return "T" . ($num + 1);
    }
    return "T1001"; // Default start
}

// --- Helper Function: Get Sequential Pub ID (e.g., P999 -> P1000) ---
function getNextPubId($conn) {
    // Finds the highest numeric part after 'P'
    $res = $conn->query("SELECT pub_id FROM publishers WHERE pub_id LIKE 'P%' ORDER BY CAST(SUBSTRING(pub_id, 2) AS UNSIGNED) DESC LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $last_id = $res->fetch_assoc()['pub_id'];
        $num = (int)substr($last_id, 1);
        return "P" . ($num + 1);
    }
    return "P101"; // Default start
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- STEP 2: ADD TITLE ---
    if (isset($_POST['action']) && $_POST['action'] == 'add_title') {
        
        // A. Check for Duplicate Title for this specific Publisher
        $check = $conn->prepare("SELECT title_id FROM titles WHERE title = ? AND pub_id = ?");
        $check->bind_param("ss", $_POST['title'], $_POST['pub_id']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo "<script>alert('Error: This title already exists for this publisher.');</script>";
        } else {
            // B. Generate Sequential ID
            $gen_title_id = getNextTitleId($conn);

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
        $check->close();
    }
}

// ==========================================================
// 3. AJAX HELPER: ADD OR FIND PUBLISHER (STEP 1)
// ==========================================================
if(isset($_GET['ajax_add_publisher'])) {
    // Use ob_clean to ensure no accidental whitespace breaks the JSON
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $p_name = trim($data['pub_name']);

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
        exit; 
    }

    // --- B. NOT FOUND: INSERT NEW SEQUENTIAL ---
    $gen_id = getNextPubId($conn);
    
    $stmt = $conn->prepare("INSERT INTO publishers (pub_id, pub_name, city, state, country) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", 
        $gen_id, $p_name, $data['city'], $data['state'], $data['country']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "pub_id" => $gen_id]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}
?>