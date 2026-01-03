<?php
session_start(); // Start session

$error = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // =======================================================================
    // 1. DATABASE CONNECTION SETTINGS (PLACEHOLDERS)
    // =======================================================================
    $servername = "localhost";   // Usually 'localhost'
    $db_username = "root";       // Your Database Username
    $db_password = "";           // Your Database Password
    $dbname = "ink_and_solace";  // Your Database Name
    $port = 3307;

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // =======================================================================
    // 2. AUTHENTICATION LOGIC
    // =======================================================================
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

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Log-in | Ink & Solace</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        :root {
            --bg-color: #dbdbdb;
            --input-bg: #ececec;
            --btn-confirm-bg: #8F8989; 
            --btn-return-bg: #3C4862;  
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: var(--bg-color);
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 40px; 
        }

        .logo-img { width: 80px; height: auto; }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; 
            padding: 20px;
            margin-top: -90px; 
        }

        .title-container {
            display: flex;
            justify-content: center;
            margin-bottom: 0px; 
            width: 100%;
        }

        .img-admin { 
            height: 190px; 
            width: auto; 
            filter: none; 
            display: block;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80%; 
            max-width: 650px; 
        }

        .input-group {
            width: 100%;
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .label-img {
            height: 28px;
            width: auto;
            margin-bottom: 12px;
            margin-left: 25px;
        }

        input {
            width: 100%;
            padding: 18px 35px; 
            border-radius: 60px;
            border: none;
            background-color: var(--input-bg);
            font-size: 18px;
            outline: none;
            box-shadow: inset 3px 3px 6px rgba(0,0,0,0.06);
            box-sizing: border-box;
            text-align: center;
        }

        .btn-bubble {
            display: inline-flex; 
            align-items: center;
            justify-content: center;
            border-radius: 70px;
            padding: 18px 60px;
            margin-top: 15px;
            text-decoration: none;
            transition: transform 0.2s, opacity 0.2s;
            border: none;
            cursor: pointer;
            width: auto; 
        }

        .btn-confirm-bubble {
            background-color: var(--btn-confirm-bg);
            box-shadow: 0 6px 18px rgba(140, 130, 125, 0.4);
        }

        .btn-return-bubble {
            background-color: var(--btn-return-bg);
            box-shadow: 0 6px 18px rgba(59, 66, 82, 0.4);
        }

        .btn-bubble:hover {
            opacity: 0.95;
            transform: scale(1.04);
        }

        .img-btn-text {
            height: 34px;
            width: auto;
            pointer-events: none;
            display: block;
        }
        
        .error-msg {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        @media (max-width: 768px) {
            .main-container { margin-top: -40px; }
            .img-admin { height: 150px; } 
            form { width: 95%; }
            .btn-bubble { padding: 15px 45px; }
            .img-btn-text { height: 28px; }
        }
    </style>
</head>
<body>

    <header class="header">
        <img src="assets/text/logo-img.png" alt="Logo" class="logo-img">
    </header>

    <div class="main-container">
        <div class="title-container">
            <img src="assets/text/text-admin.png" alt="ADMIN" class="img-admin">
        </div>

        <form action="" method="POST">
            
            <?php if(!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="input-group">
                <img src="assets/text/label-username.png" alt="Username" class="label-img">
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>

            <div class="input-group">
                <img src="assets/text/label-password.png" alt="Password" class="label-img">
                <input type="password" id="password" name="password">
            </div>

            <button type="submit" class="btn-bubble btn-confirm-bubble">
                <img src="assets/text/btn-confirm.png" alt="Confirm" class="img-btn-text">
            </button>
            
            <a href="index.php" class="btn-bubble btn-return-bubble">
                <img src="assets/text/btn-return.png" alt="Return to Home Page" class="img-btn-text">
            </a>
        </form>
    </div>

</body>
</html>