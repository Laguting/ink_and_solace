<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$insert_success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $publisher_name = trim($_POST['publisher'] ?? ""); // publisher name from form
    $author_name    = trim($_POST['author'] ?? "");
    $book           = trim($_POST['books'] ?? "");
    $count          = intval($_POST['count'] ?? 0);

    if ($publisher_name && $book) {

        // ================= GET PUBLISHER ID =================
        $stmt = $conn->prepare("SELECT pub_id FROM publishers WHERE pub_name = ?");
        $stmt->bind_param("s", $publisher_name);
        $stmt->execute();
        $stmt->bind_result($pub_id);
        $stmt->fetch();
        $stmt->close();

        // If publisher doesn't exist, insert new publisher
        if (!$pub_id) {
            $pub_id = uniqid("P");
            $stmt = $conn->prepare("INSERT INTO publishers (pub_id, pub_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $pub_id, $publisher_name);
            $stmt->execute();
            $stmt->close();
        }

        // ================= INSERT TITLE =================
        $title_id = uniqid("T");
        $stmt = $conn->prepare(
            "INSERT INTO titles (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, pubdate)
             VALUES (?, ?, 'Tech', ?, 0, 0, 0, 0, CURDATE())"
        );
        $stmt->bind_param("sss", $title_id, $book, $pub_id);
        $stmt->execute();
        $stmt->close();

        // ================= INSERT AUTHOR =================
        if ($author_name) {
            $au_id = uniqid("AU");
            $name_parts = explode(" ", $author_name, 2);
            $au_fname = $name_parts[0];
            $au_lname = $name_parts[1] ?? "";

            // Insert author
            $stmt = $conn->prepare(
                "INSERT INTO authors (au_id, au_fname, au_lname) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $au_id, $au_fname, $au_lname);
            $stmt->execute();
            $stmt->close();

            // Link author to title with au_ord from form
            $stmt = $conn->prepare(
                "INSERT INTO titleauthor (au_id, title_id, au_ord, royaltyper)
                VALUES (?, ?, ?, 10)"
            );
            $stmt->bind_param("ssi", $au_id, $title_id, $count);
            $stmt->execute();
            $stmt->close();
        }


        $insert_success = true;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Report | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-return: #3c4862;
            --pill-color: #918a86; 
            --pill-hover: #a39c98;
            --btn-add: #3c4862; 
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-bg);
        }

        body { display: flex; flex-direction: column; }

        /* ================= TOP SECTION ================= */
        .top-section {
            background-color: var(--dark-bg);
            height: 45vh; min-height: 300px;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
        }
        
        .header-content-group { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 15px; 
        }
        
        .logo-top { width: 180px; height: auto; }
        
        /* TEXT TITLE STYLE */
        .page-title-text {
            font-family: 'Cinzel', serif;
            font-size: 80px;
            color: white;
            text-transform: uppercase;
            font-weight: 400;
            letter-spacing: 5px;
            margin: 0;
            line-height: 1;
            margin-bottom: 10px;
        }

        .header-subtitle {
            font-family: 'Montserrat', sans-serif;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            margin: 0;
            font-weight: 300;
            opacity: 0.8;
        }

        /* ================= BOTTOM SECTION ================= */
        .bottom-section {
            flex: 1; padding: 50px 10% 80px;
            display: flex; flex-direction: column; 
            align-items: center; 
            justify-content: flex-start; 
            gap: 40px; 
            position: relative;
        }

        /* ADD BUTTON STYLE */
        .btn-add-new {
            background-color: var(--btn-add);
            color: white;
            border: none;
            padding: 20px 60px; 
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s, background-color 0.2s;
            display: flex; align-items: center; gap: 10px;
        }
        .btn-add-new:hover { transform: translateY(-2px); opacity: 0.9; }

        /* ================= MODAL STYLES ================= */
        .modal-overlay, .success-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.75); 
            backdrop-filter: blur(5px);
            display: none; justify-content: center; align-items: center;
            z-index: 2000; animation: fadeIn 0.3s ease-out;
        }

        /* Detail/Add Card */
        .detail-card {
            background-color: #3c4862; color: white; width: 700px; max-width: 90vw;
            padding: 50px 50px 40px; border-radius: 20px;
            position: relative; text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex; flex-direction: column; align-items: center;
        }
        .close-card-x {
            position: absolute; top: -20px; right: -20px;
            width: 40px; height: 40px; background-color: white; color: #333;
            border-radius: 50%; border: none; font-size: 20px; font-weight: bold;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: 0.2s;
            z-index: 10;
        }
        .close-card-x:hover { transform: scale(1.1); }

        .dt-header { font-family: 'Cinzel', serif; font-size: 30px; text-transform: uppercase; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 15px; width: 100%; }
        
        /* INPUT FIELDS FOR ADDING */
        .input-group { width: 100%; text-align: left; margin-bottom: 15px; }
        .input-label { font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; display: block; opacity: 0.8; }
        
        .add-input-field {
            width: 100%; padding: 12px;
            border-radius: 5px; border: 1px solid #ccc; font-family: 'Montserrat', sans-serif; font-size: 16px;
            background-color: rgba(255,255,255,0.1); color: white;
        }
        .add-input-field::placeholder { color: rgba(255,255,255,0.5); }
        .add-input-field:focus { outline: 1px solid white; background-color: rgba(255,255,255,0.2); }
        textarea.add-input-field { min-height: 100px; resize: vertical; }

        .modal-actions { display: flex; justify-content: center; gap: 20px; width: 100%; margin-top: 20px; }
        .btn-action {
            border: none; padding: 15px 0; width: 160px; border-radius: 50px;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; text-transform: uppercase; transition: transform 0.2s, opacity 0.2s; color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-action:hover { transform: translateY(-2px); opacity: 0.9; }

        .btn-save { background-color: var(--pill-color); border: 1px solid white; }
        .btn-cancel { background-color: #8b0000; }

        /* ================= SUCCESS MODAL STYLES ================= */
        .success-card {
            background-color: #20252d; color: white; width: 500px; padding: 50px;
            border-radius: 20px; text-align: center; border: 1px solid #444;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        }
        .success-msg {
            font-family: 'Cinzel', serif; font-size: 32px;
            margin-bottom: 40px; font-weight: 400; line-height: 1.2;
        }
        .btn-done {
            background-color: #e0e0e0; color: #20252d;
            padding: 12px 60px; border-radius: 30px; border: none;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; text-transform: uppercase;
        }
        .btn-done:hover { background-color: white; }

        /* ================= RETURN BUTTON (Footer) ================= */
        .return-footer {
            display: flex;
            justify-content: center;
        }
        .btn-return-wrap {
            background-color: var(--btn-return); padding: 12px 30px; border-radius: 50px;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s ease;
        }
        .btn-return-wrap:hover { transform: translateY(-2px); }
        .btn-return-img { width: 160px; height: auto; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        /* Ensures the form wrapper doesn't break layout */
        .form-wrapper { width: 100%; display: flex; flex-direction: column; align-items: center; }

        @media (max-width: 768px) {
            .page-title-text { font-size: 50px; }
            .logo-top { width: 130px; }
        }
    </style>
</head>

<body>

    <div class="top-section">
        <div class="header-content-group">
            <img src="assets/text/logo.png" class="logo-top" alt="Logo">
            
            <h1 class="page-title-text">REPORT</h1>
            
            <p class="header-subtitle">add correct info to avoid data mismatch</p>
        </div>
    </div>

    <div class="bottom-section">
        
        <button class="btn-add-new" onclick="openAddModal()">
            <span>+ ADD NEW</span>
        </button>

        <div class="return-footer">
            <a href="menu.php" style="text-decoration:none;">
                <div class="btn-return-wrap">
                    <img src="assets/text/btn-return.png" class="btn-return-img" alt="Return to Main Menu">
                </div>
            </a>
        </div>
    </div>

    <div class="modal-overlay" id="addModal">
        <div class="detail-card">
            <button type="button" class="close-card-x" onclick="closeAddModal()">X</button>
            
            <div class="dt-header">Add New Entry</div>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-wrapper">

                <div class="input-group">
                    <span class="input-label">Publisher Name</span>
                    <input type="text" name="publisher" class="add-input-field" placeholder="Enter Publisher Name" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Author Name</span>
                    <input type="text" name="author" class="add-input-field" placeholder="Enter Author Name" required>
                </div>

                <div class="input-group">
                    <span class="input-label">List of Books</span>
                    <textarea name="books" class="add-input-field" placeholder="Enter list of books (separated by commas or new lines)"></textarea>
                </div>

                <div class="input-group">
                    <span class="input-label">Total Amount of Numbers</span>
                    <input type="number" name="count" class="add-input-field" placeholder="Enter Total Count">
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-action btn-save">SAVE</button>
                    <button type="button" class="btn-action btn-cancel" onclick="closeAddModal()">CANCEL</button>
                </div>

            </form>
        </div>
    </div>

    <div class="success-overlay" id="successModal">
        <div class="success-card">
            <div class="success-msg">Entry successfully added.</div>
            <button class="btn-done" onclick="closeSuccessModal()">DONE</button>
        </div>
    </div>

    <script>
        // Check if PHP insertion was successful
        const wasSuccessful = <?php echo json_encode($insert_success); ?>;

        // If true (page reloaded after post), show the success modal
        if (wasSuccessful) {
            document.getElementById('successModal').style.display = 'flex';
        }

        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>

</body>
</html>