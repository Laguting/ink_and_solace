<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_search = "";
$title_search = "";
$has_results = false;
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $publisher_search = trim($_POST['publisher'] ?? "");
    $title_search     = trim($_POST['title'] ?? "");

    if (!empty($publisher_search) || !empty($title_search)) {

        $conditions = [];
        $params = [];
        $types = "";

        if (!empty($publisher_search)) {
            $conditions[] = "p.pub_name LIKE ?";
            $params[] = "%" . $publisher_search . "%";
            $types .= "s";
        }

        if (!empty($title_search)) {
            $conditions[] = "t.title LIKE ?";
            $params[] = "%" . $title_search . "%";
            $types .= "s";
        }

        $sql = "
            SELECT 
                p.pub_name AS publisher,
                t.title AS title,
                CONCAT(a.au_fname, ' ', a.au_lname) AS author,
                CONCAT('Books Count: ', COUNT(DISTINCT t.title_id)) AS info
            FROM titles t
            JOIN publishers p ON t.pub_id = p.pub_id
            JOIN titleauthor ta ON t.title_id = ta.title_id
            JOIN authors a ON ta.au_id = a.au_id
        ";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" OR ", $conditions);
        }

        $sql .= "
            GROUP BY 
                p.pub_id,
                t.title_id,
                a.au_id
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $has_results = true;
            while ($row = $result->fetch_assoc()) {
                $results_list[] = $row;
            }
        }

        $stmt->close();
    }
}
?>
