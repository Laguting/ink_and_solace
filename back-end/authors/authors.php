<?php
require_once 'C:\Users\MARICON\OneDrive - Polytechnic University of the Philippines\Documents\dbms-final-project\back-end\db_connect.php; // your DB connection

// Get the action from URL or POST
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch($action) {

    case 'add':
        if(isset($_POST['au_id'], $_POST['au_fname'], $_POST['au_lname'])) {
            $au_id = $_POST['au_id'];
            $au_fname = $_POST['au_fname'];
            $au_lname = $_POST['au_lname'];
            $city = $_POST['city'] ?? null;
            $state = $_POST['state'] ?? null;

            $stmt = $conn->prepare("INSERT INTO authors (au_id, au_fname, au_lname, city, state) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $au_id, $au_fname, $au_lname, $city, $state);
            if($stmt->execute()) {
                echo "Author added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        break;

    case 'update':
        if(isset($_POST['au_id'])) {
            $au_id = $_POST['au_id'];
            $au_fname = $_POST['au_fname'] ?? null;
            $au_lname = $_POST['au_lname'] ?? null;
            $city = $_POST['city'] ?? null;
            $state = $_POST['state'] ?? null;

            $stmt = $conn->prepare("UPDATE authors SET au_fname=?, au_lname=?, city=?, state=? WHERE au_id=?");
            $stmt->bind_param("sssss", $au_fname, $au_lname, $city, $state, $au_id);
            if($stmt->execute()) {
                echo "Author updated successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        break;

    case 'delete':
        if(isset($_POST['au_id'])) {
            $au_id = $_POST['au_id'];

            // Delete related books first (titleauthor)
            $stmt1 = $conn->prepare("DELETE FROM titleauthor WHERE au_id=?");
            $stmt1->bind_param("s", $au_id);
            $stmt1->execute();
            $stmt1->close();

            $stmt = $conn->prepare("DELETE FROM authors WHERE au_id=?");
            $stmt->bind_param("s", $au_id);
            if($stmt->execute()) {
                echo "Author deleted successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        break;

    case 'list':
    default:
        $result = $conn->query("SELECT * FROM authors ORDER BY au_lname, au_fname");
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>City</th><th>State</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['au_id']}</td>
                    <td>{$row['au_fname']}</td>
                    <td>{$row['au_lname']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['state']}</td>
                  </tr>";
        }
        echo "</table>";
        break;
}

$conn->close();
?>