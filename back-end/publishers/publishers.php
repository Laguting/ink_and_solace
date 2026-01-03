<?php
require_once 'C:\Users\MARICON\OneDrive - Polytechnic University of the Philippines\Documents\dbms-final-project\back-end\db_connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch($action) {

    case 'add':
        if(isset($_POST['pub_id'], $_POST['pub_name'])) {
            $pub_id = $_POST['pub_id'];
            $pub_name = $_POST['pub_name'];
            $city = $_POST['city'] ?? null;
            $state = $_POST['state'] ?? null;
            $country = $_POST['country'] ?? null;

            $stmt = $conn->prepare("INSERT INTO publishers (pub_id, pub_name, city, state, country) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $pub_id, $pub_name, $city, $state, $country);
            if($stmt->execute()) echo "Publisher added successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'update':
        if(isset($_POST['pub_id'])) {
            $pub_id = $_POST['pub_id'];
            $pub_name = $_POST['pub_name'] ?? null;
            $city = $_POST['city'] ?? null;
            $state = $_POST['state'] ?? null;
            $country = $_POST['country'] ?? null;

            $stmt = $conn->prepare("UPDATE publishers SET pub_name=?, city=?, state=?, country=? WHERE pub_id=?");
            $stmt->bind_param("sssss", $pub_name, $city, $state, $country, $pub_id);
            if($stmt->execute()) echo "Publisher updated successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'delete':
        if(isset($_POST['pub_id'])) {
            $pub_id = $_POST['pub_id'];

            // Delete related titleauthor links
            $stmt1 = $conn->prepare("DELETE FROM titleauthor WHERE title_id IN (SELECT title_id FROM titles WHERE pub_id=?)");
            $stmt1->bind_param("s", $pub_id);
            $stmt1->execute();
            $stmt1->close();

            // Delete related titles
            $stmt2 = $conn->prepare("DELETE FROM titles WHERE pub_id=?");
            $stmt2->bind_param("s", $pub_id);
            $stmt2->execute();
            $stmt2->close();

            // Delete publisher
            $stmt = $conn->prepare("DELETE FROM publishers WHERE pub_id=?");
            $stmt->bind_param("s", $pub_id);
            if($stmt->execute()) echo "Publisher deleted successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'list':
    default:
        $result = $conn->query("SELECT * FROM publishers ORDER BY pub_name");
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>City</th><th>State</th><th>Country</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['pub_id']}</td>
                    <td>{$row['pub_name']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['state']}</td>
                    <td>{$row['country']}</td>
                  </tr>";
        }
        echo "</table>";
        break;
}

$conn->close();
?>
