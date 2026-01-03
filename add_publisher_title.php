<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$publisher_search = trim($_POST['publisher'] ?? "");
$title_search     = trim($_POST['title'] ?? "");
$insert_success   = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($publisher_search) && !empty($title_search)) {

    // ================= GET OR INSERT PUBLISHER =================
    $stmtPub = $conn->prepare("SELECT pub_id FROM publishers WHERE pub_name = ?");
    $stmtPub->bind_param("s", $publisher_search);
    $stmtPub->execute();
    $stmtPub->bind_result($pub_id);
    $stmtPub->fetch();
    $stmtPub->close();

    // Publisher doesn't exist → insert it
    if (!$pub_id) {
        $pub_id = uniqid("P"); // Generate unique publisher ID
        $stmtInsertPub = $conn->prepare("INSERT INTO publishers (pub_id, pub_name) VALUES (?, ?)");
        $stmtInsertPub->bind_param("ss", $pub_id, $publisher_search);
        $stmtInsertPub->execute();
        $stmtInsertPub->close();
    }

    // ================= INSERT TITLE =================
    $title_id = uniqid("T"); // Generate unique title ID
    $stmtTitle = $conn->prepare(
        "INSERT INTO titles (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
         VALUES (?, ?, 'Tech', ?, 0, 0, 0, 0, '', NOW())"
    );
    $stmtTitle->bind_param("sss", $title_id, $title_search, $pub_id);
    $stmtTitle->execute();
    $stmtTitle->close();

    $insert_success = true;

    // Clear inputs
    $publisher_search = "";
    $title_search     = "";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers & Authors | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-confirm: #8f8989;
            --btn-return: #3c4862;
            --transition: all 0.3s ease;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
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
            max-width: 40vw;
            min-width: 180px;
        }

        .page-title-img {
            width: 520px;
            max-width: 90%;
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
            padding: 20px 30px 15px;
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
            max-width: 600px;
            text-align: center;
        }

        /* ================= INPUT GROUP ================= */
        .input-group {
            margin-bottom: 22px;
            text-align: left;
        }

        .label-img {
            width: 180px;
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
        }

        .placeholder-img {
            position: absolute;
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 90px;
            opacity: 0.6;
            pointer-events: none;
        }

        input:focus + .placeholder-img,
        input:not(:placeholder-shown) + .placeholder-img {
            display: none;
        }

        /* ================= BUTTONS ================= */
        .btn-confirm-wrap,
        .btn-return-wrap {
            margin: 14px auto 0;
            border-radius: 50px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border: none;
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .btn-confirm-wrap {
            background-color: var(--btn-confirm);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            padding: 10px 35px;
        }

        .btn-return-wrap {
            background-color: var(--btn-return);
            box-shadow: 0 5px 12px rgba(0,0,0,0.25);
            margin-top: 18px; 
            padding: 14px 45px; 
            max-width: 80%;
        }

        .btn-img {
            display: block;
            width: 90px; 
        }

        .btn-return-wrap .btn-img {
            width: 180px; 
            max-width: 100%;
            height: auto;
        }

        .btn-confirm-wrap:hover, .btn-return-wrap:hover {
            transform: scale(1.03); 
            filter: brightness(1.1);
        }

        /* ================= MODAL PROMPT STYLES ================= */
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

        /* ================= FOOTER ================= */
        footer {
            display: flex;
            justify-content: flex-end; 
            align-items: center;
            padding-top: 10px;
        }

        .footer-text-img {
            width: 120px;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .logo-top { width: 110px; top: 20px; }
            .top-section { padding-top: 110px; }
            .page-title-img { width: 380px; }
            .btn-return-wrap { padding: 10px 30px; }
            .btn-return-wrap .btn-img { width: 140px; }
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
    <img src="assets/text/title-publishers-titles.png" class="page-title-img" alt="Publishers & Titles">
    
    <p class="instruction-text">Please type the Publisher and the Title accurately to avoid data mismatch.</p>
</div>

<div class="bottom-section">
    <div class="form-container">
        <form id="publisherTitleForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <div class="input-group">
                <img src="assets/text/label-publisher.png" class="label-img" alt="Publisher">
                <div class="input-wrapper">
                    <input type="text" id="inputPublisher" name="publisher" placeholder=" " value="<?php echo htmlspecialchars($publisher_search); ?>" required>
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
                <img src="assets/text/btn-confirm.png" class="btn-img" alt="Confirm">
            </button>

            <div style="display: block;">
                <a href="add_database.php" style="text-decoration:none;">
                    <div class="btn-return-wrap">
                        <img src="assets/text/btn-return.png" class="btn-img" alt="Return to Main Menu">
                    </div>
                </a>
            </div>

        </form>
    </div>

    <footer>
        <img src="assets/text/footer-by-group2.png" class="footer-text-img" alt="">
    </footer>
</div>

<script>
    // 1. Check PHP flag for success
    const wasSuccessful = <?php echo json_encode($insert_success); ?>;

    if (wasSuccessful) {
        document.getElementById('updateModal').style.display = 'flex';
    }

    // 2. Close Modal
    function closeModal() {
        document.getElementById('updateModal').style.display = 'none';
    }
</script>

</body>
</html>