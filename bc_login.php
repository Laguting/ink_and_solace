<?php
session_start(); // Start session

$error = "";
require_once __DIR__ . "/bc_db_connect.php";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        
        // Prepare SQL to prevent injection
        // Assumed Table: 'admins'
        // Assumed Columns: 'id', 'username', 'password'
        $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Verify Password
                // Note: For better security, use password_verify() and hashed passwords in production.
                // Here we use direct comparison as requested.
                if ($password === $row['password']) {
                    
                    // LOGIN SUCCESS
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $row['username'];
                    
                    header("Location: admin_welcome.php"); 
                    exit;
                    
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Username not found.";
            }
            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare statement.";
        }
    } else {
        $error = "Please fill in all fields.";
    }

}
?>