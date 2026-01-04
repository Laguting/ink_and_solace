<?php
require_once __DIR__ . "/bc_db_connect.php";

$author_input = trim($_POST['author'] ?? "");
$title_input  = trim($_POST['title'] ?? "");
$found_books  = [];
$show_results_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "SELECT t.title_id, t.title, a.au_fname, a.au_lname
            FROM titles t
            JOIN titleauthor ta ON t.title_id = ta.title_id
            JOIN authors a ON ta.au_id = a.au_id";

    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($author_input)) {
        $conditions[] = "(a.au_fname LIKE ? OR a.au_lname LIKE ?)";
        $params[] = "%" . $author_input . "%";
        $params[] = "%" . $author_input . "%";
        $types .= "ss";
    }

    if (!empty($title_input)) {
        $conditions[] = "t.title LIKE ?";
        $params[] = "%" . $title_input . "%";
        $types .= "s";
    }

    if (!empty($conditions)) {
        // Use OR between conditions, not AND
        $sql .= " WHERE " . implode(" OR ", $conditions);
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $found_books[] = [
                "id"     => $row['title_id'],
                "author" => $row['au_fname'] . " " . $row['au_lname'],
                "title"  => $row['title']
            ];
        }

        $show_results_modal = true;
        $stmt->close();
    } else {
        die("SQL Prepare Error: " . $conn->error);
    }
}

?>