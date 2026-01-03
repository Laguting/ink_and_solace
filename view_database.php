<?php
// ==========================================================
// 1. DATABASE CONNECTION
// ==========================================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ==========================================================
// 2. SEARCH LOGIC
// ==========================================================
$author_search = "";
$title_search  = "";
$results_list  = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author_search = trim($_POST['author'] ?? "");
    $title_search  = trim($_POST['title'] ?? "");

    $sql = "SELECT t.title_id, t.title, p.pub_name AS publisher, CONCAT(a.au_fname,' ',a.au_lname) AS author
            FROM titles t
            LEFT JOIN titleauthor ta ON t.title_id = ta.title_id
            LEFT JOIN authors a ON ta.au_id = a.au_id
            LEFT JOIN publishers p ON t.pub_id = p.pub_id
            WHERE a.au_fname LIKE ? OR a.au_lname LIKE ? OR t.title LIKE ?
            ORDER BY t.title";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $auth_param1 = "%" . ($author_search ?: "NO_MATCH_XYZ") . "%";
        $auth_param2 = $auth_param1; // last name
        $title_param = "%" . ($title_search ?: "NO_MATCH_XYZ") . "%";

        $stmt->bind_param("sss", $auth_param1, $auth_param2, $title_param);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results_list[] = $row;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Authors & Titles | Ink & Solace</title>
<style>
    body { font-family: 'Montserrat', sans-serif; background: #f0f0f0; padding: 40px; }
    table { border-collapse: collapse; width: 100%; background: white; border-radius: 10px; overflow: hidden; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ccc; }
    th { background: #3c4862; color: white; }
    tr:hover { background-color: #e0e0e0; }
    .search-form { margin-bottom: 20px; display: flex; gap: 10px; }
    .search-form input { padding: 8px; border-radius: 5px; border: 1px solid #ccc; flex: 1; }
    .search-form button { padding: 8px 20px; background: #3c4862; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .search-form button:hover { background: #4e5e7a; }
</style>
</head>
<body>

<h2>Authors & Titles</h2>

<form method="POST" class="search-form">
    <input type="text" name="author" placeholder="Search by Author" value="<?php echo htmlspecialchars($author_search); ?>">
    <input type="text" name="title" placeholder="Search by Title" value="<?php echo htmlspecialchars($title_search); ?>">
    <button type="submit">Search</button>
</form>

<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Publisher</th>
    </tr>
    <?php if(count($results_list) === 0): ?>
        <tr><td colspan="3">No records found.</td></tr>
    <?php else: ?>
        <?php foreach($results_list as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['publisher']); ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

</body>
</html>
