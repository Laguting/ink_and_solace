<?php
require_once __DIR__ . "/bc_db_connect.php";

$author_search = $_GET['author_id'] ?? null;
$title_search = $_GET['title_id'] ?? null;
$search_query = "";
$grouped_results = []; 
$has_results = false;

// Handle Search Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $search_query = trim($_POST['search_query'] ?? "");
    $has_searched = true;

    // LEFT JOIN ensures all publishers show up even if they have 0 books
    // Using || as a separator for GROUP_CONCAT to handle titles with commas safely
    $sql = "SELECT 
                p.pub_id AS id,
                p.pub_name AS publisher, 
                COUNT(t.title_id) AS count, 
                IFNULL(GROUP_CONCAT(t.title SEPARATOR '||'), '') AS books
             FROM publishers p
             LEFT JOIN titles t ON p.pub_id = t.pub_id";

    if (!empty($search_query)) {
        $sql .= " WHERE p.pub_name LIKE ? OR t.title LIKE ?";
    }

    // Grouping strictly by publisher ID to combine all their books into one row
    $sql .= " GROUP BY p.pub_id ORDER BY p.pub_name ASC";

    $stmt = $conn->prepare($sql);
    
    if (!empty($search_query)) {
        $term = "%" . $search_query . "%";
        $stmt->bind_param("ss", $term, $term);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $pubName = trim($row['publisher']);
            $grouped_results[$pubName] = $row;
        }
    }
    $stmt->close();
}
?>