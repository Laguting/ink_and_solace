<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Welcome, Admin!</h2>

<p>You are now logged in.</p>

<a href="logout.php">Logout</a>

</body>
</html>
