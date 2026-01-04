<?php
require_once __DIR__ . "/bc_db_connect.php";

$search_query = "";
$search_results = [];
$has_searched = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = trim($_POST['search_query'] ?? "");
    $has_searched = true;

    // ==========================================================
    // 3. SQL QUERY - JOIN titles, authors, publishers, titleauthor
    // ==========================================================
    if (empty($search_query)) {
        $sql = "SELECT t.title AS book_title, 
                       p.pub_name AS publisher_name, 
                       CONCAT(a.au_fname, ' ', a.au_lname) AS author_name
                FROM titles t
                LEFT JOIN publishers p ON t.pub_id = p.pub_id
                LEFT JOIN titleauthor ta ON t.title_id = ta.title_id
                LEFT JOIN authors a ON ta.au_id = a.au_id
                ORDER BY t.title";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT t.title AS book_title, 
                       p.pub_name AS publisher_name, 
                       CONCAT(a.au_fname, ' ', a.au_lname) AS author_name
                FROM titles t
                LEFT JOIN publishers p ON t.pub_id = p.pub_id
                LEFT JOIN titleauthor ta ON t.title_id = ta.title_id
                LEFT JOIN authors a ON ta.au_id = a.au_id
                WHERE t.title LIKE ? OR p.pub_name LIKE ? OR a.au_fname LIKE ? OR a.au_lname LIKE ?
                ORDER BY t.title";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $param = "%" . $search_query . "%";
            $stmt->bind_param("ssss", $param, $param, $param, $param);
        }
    }

    // Execute and Fetch
    if (isset($stmt) && $stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $search_results[] = [
                'book'      => $row['book_title'],
                'publisher' => $row['publisher_name'] ?? "Unknown",
                'author'    => $row['author_name'] ?? "Unknown"
            ];
        }
        $stmt->close();
    }
}

?>