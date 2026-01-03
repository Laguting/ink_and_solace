<?php
require_once 'C:\Users\MARICON\OneDrive - Polytechnic University of the Philippines\Documents\dbms-final-project\back-end\db_connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch($action) {

    case 'add':
        if(isset($_POST['title_id'], $_POST['title'], $_POST['pub_id'], $_POST['au_id'])) {
            $title_id = $_POST['title_id'];
            $title = $_POST['title'];
            $pub_id = $_POST['pub_id'];
            $price = $_POST['price'] ?? 0;
            $au_id = $_POST['au_id'];

            // Add book
            $stmt = $conn->prepare("INSERT INTO titles (title_id, title, pub_id, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssd", $title_id, $title, $pub_id, $price);
            if($stmt->execute()) {
                // Link author
                $stmt2 = $conn->prepare("INSERT INTO titleauthor (au_id, title_id) VALUES (?, ?)");
                $stmt2->bind_param("ss", $au_id, $title_id);
                $stmt2->execute();
                $stmt2->close();
                echo "Book added successfully.";
            } else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'update':
        if(isset($_POST['title_id'])) {
            $title_id = $_POST['title_id'];
            $title = $_POST['title'] ?? null;
            $pub_id = $_POST['pub_id'] ?? null;
            $price = $_POST['price'] ?? null;

            $stmt = $conn->prepare("UPDATE titles SET title=?, pub_id=?, price=? WHERE title_id=?");
            $stmt->bind_param("ssds", $title, $pub_id, $price, $title_id);
            if($stmt->execute()) echo "Book updated successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'delete':
        if(isset($_POST['title_id'])) {
            $title_id = $_POST['title_id'];

            // Delete titleauthor link first
            $stmt1 = $conn->prepare("DELETE FROM titleauthor WHERE title_id=?");
            $stmt1->bind_param("s", $title_id);
            $stmt1->execute();
            $stmt1->close();

            // Delete book
            $stmt = $conn->prepare("DELETE FROM titles WHERE title_id=?");
            $stmt->bind_param("s", $title_id);
            if($stmt->execute()) echo "Book deleted successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'list':
    default:
        $sql = "
        SELECT t.title_id, t.title, t.price, p.pub_name
        FROM titles t
        LEFT JOIN publishers p ON t.pub_id = p.pub_id
        ORDER BY t.title
        ";
        $result = $conn->query($sql);
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Title</th><th>Price</th><th>Publisher</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['title_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['price']}</td>
                    <td>{$row['pub_name']}</td>
                  </tr>";
        }
        echo "</table>";
        break;
}

$conn->close();
?>
