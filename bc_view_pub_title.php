<?php
require_once __DIR__ . "/bc_db_connect.php";

$pub_search = "";
$title_search = "";
$has_results = false;
$no_results = false;
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- UPDATE ACTION ---
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $sql_pub = "UPDATE publishers SET pub_name=?, city=?, state=?, country=? WHERE pub_id=?";
        $stmt1 = $conn->prepare($sql_pub);
        $stmt1->bind_param("sssss", $_POST['pub_name'], $_POST['city'], $_POST['state'], $_POST['country'], $_POST['pub_id']);
        $p_upd = $stmt1->execute();
        $stmt1->close();

        $sql_title = "UPDATE titles SET title=?, type=?, price=?, advance=?, royalty=?, ytd_sales=?, notes=?, pubdate=? WHERE title_id=?";
        $stmt2 = $conn->prepare($sql_title);
        $stmt2->bind_param("ssddiiiss", $_POST['title'], $_POST['type'], $_POST['price'], $_POST['advance'], $_POST['royalty'], $_POST['ytd_sales'], $_POST['notes'], $_POST['pubdate'], $_POST['title_id']);
        $t_upd = $stmt2->execute();
        $stmt2->close();

        echo json_encode(["status" => ($p_upd && $t_upd) ? "success" : "error", "message" => "ENTRY SUCCESSFULLY EDITED."]);
        exit;
    }

    // --- DELETE ACTION ---
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM titles WHERE title_id = ?");
        $stmt->bind_param("s", $_POST['title_id']);
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(["status" => $success ? "success" : "error", "message" => "ENTRY SUCCESSFULLY DELETED."]);
        exit;
    }

    // --- SEARCH LOGIC ---
    if (isset($_POST['pub_search']) || isset($_POST['title_search'])) {
        $pub_search = trim($_POST['pub_search'] ?? "");
        $title_search = trim($_POST['title_search'] ?? "");

        $conditions = [];
        $params = [];
        $types = "";

        $query = "SELECT t.*, p.pub_name, p.city, p.state, p.country 
                  FROM titles t 
                  LEFT JOIN publishers p ON t.pub_id = p.pub_id";

        if (!empty($pub_search)) {
            $conditions[] = "p.pub_name LIKE ?";
            $params[] = "%$pub_search%";
            $types .= "s";
        }
        if (!empty($title_search)) {
            $conditions[] = "t.title LIKE ?";
            $params[] = "%$title_search%";
            $types .= "s";
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($res->num_rows > 0) {
                $has_results = true;
                while($row = $res->fetch_assoc()) { $results_list[] = $row; }
            } else {
                $no_results = true;
            }
            $stmt->close();
        }
    }
}
?>

