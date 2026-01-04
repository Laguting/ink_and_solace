<?php
require_once __DIR__ . "/bc_db_connect.php";

$author_search = "";
$title_search  = "";
$has_results   = false;
$results_list  = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author_search = trim($_POST['author'] ?? "");
    $title_search  = trim($_POST['title'] ?? "");

    // Only run query if at least one field has text
    if (!empty($author_search) || !empty($title_search)) {

        // ==========================================================
        // SQL QUERY
        // Join authors, titles, and publishers
        // ==========================================================
        $sql = "SELECT t.title_id,
                       t.title,
                       CONCAT(a.au_fname, ' ', a.au_lname) AS author_name,
                       p.pub_name AS publisher_name,
                       t.price,
                       t.pubdate
                FROM titles t
                LEFT JOIN authors a ON t.title_id = a.au_id
                LEFT JOIN publishers p ON t.pub_id = p.pub_id
                WHERE a.au_fname LIKE ? 
                   OR a.au_lname LIKE ? 
                   OR t.title LIKE ?
                ORDER BY t.title";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $auth_param1 = "%" . ($author_search ?: "NO_MATCH_XYZ") . "%";
            $auth_param2 = $auth_param1; // For last name
            $title_param = "%" . ($title_search ?: "NO_MATCH_XYZ") . "%";

            $stmt->bind_param("sss", $auth_param1, $auth_param2, $title_param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $has_results = true;
                while ($row = $result->fetch_assoc()) {
                    $results_list[] = [
                        'title'     => $row['title'],
                        'author'    => $row['author_name'] ?? "Unknown",
                        'publisher' => $row['publisher_name'] ?? "Unknown",
                        'price'     => $row['price'],
                        'pubdate'   => $row['pubdate']
                    ];
                }
            }
            $stmt->close();
        }
    }
}

?>