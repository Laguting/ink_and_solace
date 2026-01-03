<?php
require_once 'C:\Users\MARICON\OneDrive - Polytechnic University of the Philippines\Documents\dbms-final-project\back-end\db_connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch($action) {

    case 'add':
        if(isset($_POST['emp_id'], $_POST['emp_name'], $_POST['pub_id'])) {
            $emp_id = $_POST['emp_id'];
            $emp_name = $_POST['emp_name'];
            $pub_id = $_POST['pub_id'];
            $position = $_POST['position'] ?? null;
            $email = $_POST['email'] ?? null;

            $stmt = $conn->prepare("INSERT INTO employees (emp_id, emp_name, pub_id, position, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $emp_id, $emp_name, $pub_id, $position, $email);
            if($stmt->execute()) echo "Employee added successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'update':
        if(isset($_POST['emp_id'])) {
            $emp_id = $_POST['emp_id'];
            $emp_name = $_POST['emp_name'] ?? null;
            $pub_id = $_POST['pub_id'] ?? null;
            $position = $_POST['position'] ?? null;
            $email = $_POST['email'] ?? null;

            $stmt = $conn->prepare("UPDATE employees SET emp_name=?, pub_id=?, position=?, email=? WHERE emp_id=?");
            $stmt->bind_param("sssss", $emp_name, $pub_id, $position, $email, $emp_id);
            if($stmt->execute()) echo "Employee updated successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'delete':
        if(isset($_POST['emp_id'])) {
            $emp_id = $_POST['emp_id'];
            $stmt = $conn->prepare("DELETE FROM employees WHERE emp_id=?");
            $stmt->bind_param("s", $emp_id);
            if($stmt->execute()) echo "Employee deleted successfully.";
            else echo "Error: " . $stmt->error;
            $stmt->close();
        }
        break;

    case 'list':
    default:
        $sql = "
        SELECT e.emp_id, e.emp_name, e.position, e.email, p.pub_name
        FROM employees e
        LEFT JOIN publishers p ON e.pub_id = p.pub_id
        ORDER BY e.emp_name
        ";
        $result = $conn->query($sql);
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Position</th><th>Email</th><th>Publisher</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['emp_id']}</td>
                    <td>{$row['emp_name']}</td>
                    <td>{$row['position']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['pub_name']}</td>
                  </tr>";
        }
        echo "</table>";
        break;
}

$conn->close();
?>
