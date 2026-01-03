<?php
// ==========================================
// 1. DATABASE CONNECTION
// ==========================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$author_search = "";
$title_search  = "";
$insert_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $author_search = trim($_POST['author'] ?? "");
    $title_search  = trim($_POST['title'] ?? "");

    if (!empty($author_search) && !empty($title_search)) {

        /* ===========================
           1. SPLIT AUTHOR NAME
           =========================== */
        $name_parts = explode(" ", $author_search, 2);
        $au_fname = $name_parts[0];
        $au_lname = $name_parts[1] ?? "Unknown";

        // ===========================
        // 2. INSERT AUTHOR (if new)
        // ===========================
        $stmtCheckAuthor = $conn->prepare(
            "SELECT au_id FROM authors WHERE au_fname = ? AND au_lname = ?"
        );
        $stmtCheckAuthor->bind_param("ss", $au_fname, $au_lname);
        $stmtCheckAuthor->execute();
        $authorResult = $stmtCheckAuthor->get_result();

        if ($authorResult->num_rows === 1) {
            $authorRow = $authorResult->fetch_assoc();
            $au_id = $authorRow['au_id']; // Existing author
        } else {
            // New author
            do {
                $au_id = "AU" . uniqid(rand(), true);
                $check = $conn->prepare("SELECT 1 FROM authors WHERE au_id = ?");
                $check->bind_param("s", $au_id);
                $check->execute();
                $check->store_result();
                $exists = $check->num_rows > 0;
                $check->close();
            } while ($exists);

            $stmtInsertAuthor = $conn->prepare(
                "INSERT INTO authors (au_id, au_fname, au_lname) VALUES (?, ?, ?)"
            );
            $stmtInsertAuthor->bind_param("sss", $au_id, $au_fname, $au_lname);
            $stmtInsertAuthor->execute();
            $stmtInsertAuthor->close();
        }
        $stmtCheckAuthor->close();

        // ===========================
        // 3. INSERT TITLE
        // ===========================
        do {
            $title_id = "T" . uniqid(rand(), true);
            $checkTitle = $conn->prepare("SELECT 1 FROM titles WHERE title_id = ?");
            $checkTitle->bind_param("s", $title_id);
            $checkTitle->execute();
            $checkTitle->store_result();
            $existsTitle = $checkTitle->num_rows > 0;
            $checkTitle->close();
        } while ($existsTitle);

        $pub_id = NULL; // Can be updated later
        $price  = 0.0;

        $stmtInsertTitle = $conn->prepare(
            "INSERT INTO titles (title_id, title, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
             VALUES (?, ?, ?, ?, 0, 0, 0, '', CURDATE())"
        );
        $stmtInsertTitle->bind_param("sssd", $title_id, $title_search, $pub_id, $price);
        $stmtInsertTitle->execute();
        $stmtInsertTitle->close();

        // ===========================
        // 4. LINK AUTHOR TO TITLE
        // ===========================
        $au_ord = 1;      // Order of the author
        $royaltyper = 10; // Default royalty %
        $stmtLink = $conn->prepare(
            "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper) VALUES (?, ?, ?, ?)"
        );
        $stmtLink->bind_param("ssid", $au_id, $title_id, $au_ord, $royaltyper);
        $stmtLink->execute();
        $stmtLink->close();

        $insert_success = true;
        $author_search = "";
        $title_search  = "";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors & Titles | Ink & Solace</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-confirm: #8f8989;
            --btn-return: #3c4862;
            --footer-text: #666666;
            --transition: all 0.3s ease;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
            background-color: var(--light-bg);
        }

        body { display: flex; flex-direction: column; }

        /* ================= TOP SECTION ================= */
        .top-section {
            background-color: var(--dark-bg);
            height: 45%;
            position: relative;
            display: flex;
            flex-direction: column; 
            justify-content: center;
            align-items: center;
            padding-top: 40px; 
        }

        .logo-top {
            position: absolute;
            top: 30px;
            width: 220px;
            max-width: 35vw;
        }

        .page-title-img {
            width: 520px;
            max-width: 85%;
            height: auto;
            margin-top: 60px; 
        }

        .instruction-text {
            color: #ffffff; 
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            margin-top: 15px; 
            text-align: center;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        /* ================= BOTTOM SECTION ================= */
        .bottom-section {
            background-color: var(--light-bg);
            flex: 1; 
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
        }

        .form-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            width: 100%;
            max-width: 550px;
            text-align: center;
        }

        /* ================= INPUT GROUP ================= */
        .input-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .label-img {
            width: 160px;
            max-width: 40vw;
            margin: 0 0 10px 15px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px 20px; 
            border-radius: 50px;
            border: none;
            outline: none;
            background-color: var(--input-bg);
            font-size: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .placeholder-img {
            position: absolute;
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 90px;
            opacity: 0.5;
            pointer-events: none;
        }

        input:focus + .placeholder-img,
        input:not(:placeholder-shown) + .placeholder-img {
            display: none;
        }

        /* ================= BUTTONS ================= */
        .btn-confirm-wrap {
            background-color: var(--btn-confirm);
            border-radius: 50px;
            padding: 10px 40px;
            display: inline-flex;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            margin-bottom: 15px;
        }

        .btn-return-wrap {
            background-color: var(--btn-return);
            border-radius: 50px;
            padding: 12px 40px;
            display: inline-flex;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
        }

        .btn-img-confirm { width: 80px; }
        .btn-img-return { width: 160px; }

        .btn-confirm-wrap:hover, .btn-return-wrap:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* ================= MODAL PROMPT ================= */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: none; 
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-icon {
            position: absolute;
            top: 15px; right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            line-height: 1;
        }

        .modal-box h2 {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark-bg);
            margin-bottom: 20px;
            font-size: 22px; 
        }

        .success-icon {
            color: #4CAF50;
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
        }

        .btn-done {
            background-color: var(--btn-return);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-done:hover {
            background-color: #506180;
        }
    </style>
</head>

<body>

<div class="modal-overlay" id="updateModal">
    <div class="modal-box">
        <span class="close-icon" onclick="closeModal()">&times;</span>
        <span class="success-icon">&#10004;</span>
        <h2>Successfully added to database!</h2>
        <button class="btn-done" onclick="closeModal()">DONE</button>
    </div>
</div>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-authors-titles.png" class="page-title-img" alt="Authors & Titles">
    
    <p class="instruction-text">Please type the Author and the Title accurately to avoid data mismatch.</p>
</div>

<div class="bottom-section">
    <div class="form-container">
        <form id="authorForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <div class="input-group">
                <img src="assets/text/label-author.png" class="label-img" alt="author">
                <div class="input-wrapper">
                    <input type="text" id="inputAuthor" name="author" placeholder=" " value="<?php echo htmlspecialchars($author_search); ?>" required>
                    <img src="assets/text/placeholder-search.png" class="placeholder-img" alt="">
                </div>
            </div>

            <div class="input-group">
                <img src="assets/text/label-title.png" class="label-img" alt="Title">
                <div class="input-wrapper">
                    <input type="text" id="inputTitle" name="title" placeholder=" " value="<?php echo htmlspecialchars($title_search); ?>" required>
                    <img src="assets/text/placeholder-search.png" class="placeholder-img" alt="">
                </div>
            </div>

            <button type="submit" class="btn-confirm-wrap">
                <img src="assets/text/btn-confirm.png" class="btn-img-confirm" alt="Confirm">
            </button>

            <div style="display: block; margin-top: 10px;">
                <a href="add_database.php" style="text-decoration:none;">
                    <div class="btn-return-wrap">
                        <img src="assets/text/btn-return.png" class="btn-img-return" alt="Return to Main Menu">
                    </div>
                </a>
            </div>

        </form>
    </div>

</div>

<script>
    // 1. Check if PHP reported a success
    // We echo the PHP variable into JS. If it's true, show modal.
    const wasSuccessful = <?php echo json_encode($insert_success); ?>;

    if (wasSuccessful) {
        document.getElementById('updateModal').style.display = 'flex';
    }

    // 2. Function to close the modal
    function closeModal() {
        document.getElementById('updateModal').style.display = 'none';
    }
</script>

</body>
</html>