<?php
require_once __DIR__ . "/bc_db_connect.php";

$search_query = trim($_POST['search_query'] ?? "");
$search_results = [];
$has_searched = ($_SERVER["REQUEST_METHOD"] == "POST");

if ($has_searched) {

    /**
     * SQL CHANGE:
     * Changed LEFT JOIN to INNER JOIN. 
     * This ensures that if a title doesn't have a publisher, 
     * or a title doesn't have an author, it is excluded from results.
     */
    $sql = "SELECT 
                t.title_id, t.title, 
                p.pub_name, 
                a.au_fname, a.au_lname,
                ta.au_ord
            FROM titles t
            INNER JOIN publishers p ON t.pub_id = p.pub_id
            INNER JOIN titleauthor ta ON t.title_id = ta.title_id
            INNER JOIN authors a ON ta.au_id = a.au_id";

    $params = [];
    $types = "";
    
    if (!empty($search_query)) {
        // Search publisher OR author
        $sql .= " WHERE p.pub_name LIKE ? OR a.au_fname LIKE ? OR a.au_lname LIKE ?";
        $param = "%" . $search_query . "%";
        $params = [$param, $param, $param];
        $types = "sss";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $search_results[] = [
                "id"        => $row['title_id'],
                "publisher" => $row['pub_name'], // No longer need ?? "N/A" because of INNER JOIN
                "author"    => trim($row['au_fname'] . " " . $row['au_lname']),
                "title"     => $row['title'],
                "count"     => $row['au_ord'] ?? 0
            ];
        }
        $stmt->close();
    } else {
        die("SQL Prepare Error: " . $conn->error);
    }
}
?>