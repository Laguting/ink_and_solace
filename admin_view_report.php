<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$search_query = trim($_POST['search_query'] ?? "");
$search_results = [];
$has_searched = ($_SERVER["REQUEST_METHOD"] == "POST");

if ($has_searched) {

    // Base SQL: join titles → publishers → titleauthor → authors
    $sql = "SELECT 
                t.title_id, t.title, 
                p.pub_name, 
                a.au_fname, a.au_lname,
                ta.au_ord
            FROM titles t
            LEFT JOIN publishers p ON t.pub_id = p.pub_id
            LEFT JOIN titleauthor ta ON t.title_id = ta.title_id
            LEFT JOIN authors a ON ta.au_id = a.au_id";

    $params = [];
    $types = "";
    if (!empty($search_query)) {
        // Search publisher OR author
        $sql .= " WHERE p.pub_name LIKE ? OR a.au_fname LIKE ? OR a.au_lname LIKE ?";
        $param = "%" . $search_query . "%";
        $params = [$param, $param, $param];
        $types = "sss";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $search_results[] = [
                "id"        => $row['title_id'],
                "publisher" => $row['pub_name'] ?? "N/A",
                "author"    => trim($row['au_fname'] . " " . $row['au_lname']),
                "title"     => $row['title'],
                "count"     => $row['au_ord'] ?? 0   // <-- au_ord as count
            ];
        }
        $stmt->close();
    } else {
        die("SQL Prepare Error: " . $conn->error);
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-return: #3c4862;
            --pill-color: #918a86; 
            --pill-hover: #a39c98;
            --btn-edit: #888888; 
            --btn-delete: #8b0000;
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
            height: 45vh; 
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center; 
            align-items: center;
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

        /* NEW SUBTITLE STYLE */
        .header-subtitle {
            font-family: 'Montserrat', sans-serif;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            margin: 0;
            font-weight: 300;
            opacity: 0.8; 
        }

        /* ================= BOTTOM SECTION ================= */
        .bottom-section {
            flex: 1;
            padding: 50px 10% 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* SEARCH BAR STYLES */
        .search-form {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center; 
            gap: 15px; 
            margin-bottom: 40px;
        }

        .search-container {
            width: 100%;
            max-width: 600px; 
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 25px; 
            border-radius: 50px;
            border: 1px solid #ccc;
            background-color: var(--input-bg);
            outline: none;
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #333;
        }

        /* SEARCH BUTTON STYLE */
        .btn-search-submit {
            background-color: var(--btn-return);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s, background-color 0.2s;
            height: 54px; 
        }

        .btn-search-submit:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* ================= RESULTS LIST STYLES ================= */
        .results-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 700px;
        }

        .report-pill {
            background-color: var(--pill-color);
            color: white;
            width: 100%;
            padding: 20px 40px;
            border-radius: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s, background-color 0.2s;
            cursor: pointer;
            border: none;
        }

        .report-pill:hover {
            transform: scale(1.02);
            background-color: var(--pill-hover);
        }

        .rep-title {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            text-transform: uppercase;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .rep-details {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 300;
            opacity: 0.9;
        }

        .no-results {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            color: #555;
            margin-top: 20px;
        }

        /* ================= DETAIL MODAL STYLES ================= */
        .modal-overlay, .confirm-overlay, .success-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.75); 
            backdrop-filter: blur(5px);
            display: flex; justify-content: center; align-items: center;
            z-index: 2000; animation: fadeIn 0.3s ease-out;
            display: none; /* Hidden by default */
        }

        .detail-card {
            background-color: var(--pill-color);
            color: white;
            width: 700px;
            max-width: 90vw;
            padding: 60px 50px;
            border-radius: 20px;
            position: relative;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Close X Button */
        .close-card-x {
            position: absolute; top: -20px; right: -20px;
            width: 50px; height: 50px;
            background-color: white; color: #333;
            border-radius: 50%; border: none; font-size: 24px; font-weight: bold;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: 0.2s;
        }
        .close-card-x:hover { transform: scale(1.1); }

        /* VIEW MODE STYLES */
        .dt-header {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
            width: 100%;
        }

        .dt-sub {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .dt-body {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 300;
            margin-bottom: 40px;
            text-align: left;
            width: 100%;
            background-color: rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 10px;
        }

        .books-label {
            font-weight: 700;
            text-transform: uppercase;
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            opacity: 0.8;
        }

        /* EDIT MODE STYLES */
        .edit-input-field {
            width: 100%; padding: 12px; margin-bottom: 15px;
            border-radius: 5px; border: 1px solid #ccc; font-family: 'Montserrat', sans-serif; font-size: 16px;
            background-color: rgba(255,255,255,0.1); color: white; display: none;
        }
        .edit-input-field:focus { outline: 1px solid white; background-color: rgba(255,255,255,0.2); }
        .edit-input-field::placeholder { color: rgba(255,255,255,0.6); }
        textarea.edit-input-field { min-height: 120px; resize: vertical; }

        /* WARNING TEXT IN POPUP */
        .warning-text {
            color: #8b0000;
            background-color: rgba(255,255,255,0.8);
            padding: 10px;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            margin-bottom: 20px;
            font-weight: 700;
            display: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
        }

        /* ACTIONS */
        .modal-actions { display: flex; justify-content: center; gap: 20px; width: 100%; margin-top: 10px; }
        .btn-action {
            border: none; padding: 15px 0; width: 160px; border-radius: 50px;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; text-transform: uppercase; transition: transform 0.2s, opacity 0.2s; color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-action:hover { transform: translateY(-2px); opacity: 0.9; }

        .btn-edit { background-color: var(--btn-edit); }
        .btn-delete { background-color: var(--btn-delete); }
        .btn-save { background-color: var(--btn-return); border: 1px solid white; display: none; }
        .btn-cancel { background-color: #a33b3b; display: none; }

        /* ================= CONFIRMATION MODAL STYLES ================= */
        .confirm-card {
            background-color: #20252d; color: white; width: 500px; padding: 50px;
            border-radius: 20px; text-align: center; border: 1px solid #444;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            display: flex; flex-direction: column; align-items: center;
        }
        .confirm-msg {
            font-family: 'Cinzel', serif; font-size: 28px;
            margin-bottom: 40px; font-weight: 400; line-height: 1.3;
        }
        .confirm-actions { display: flex; gap: 20px; }
        .btn-confirm-yes {
            background-color: #918a86; color: white;
            padding: 12px 40px; border-radius: 30px; border: none;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; text-transform: uppercase;
        }
        .btn-confirm-no {
            background-color: #8b0000; color: white;
            padding: 12px 40px; border-radius: 30px; border: none;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; text-transform: uppercase;
        }

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
            margin-top: 50px;
            display: flex;
            justify-content: center;
        }
        .btn-return-wrap {
            background-color: var(--btn-return);
            padding: 12px 30px;
            border-radius: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: transform 0.2s ease;
        }
        .btn-return-wrap:hover { transform: translateY(-2px); }
        .btn-return-img { width: 160px; height: auto; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 768px) {
            .page-title-text { font-size: 50px; }
            .logo-top { width: 130px; }
            .search-form { flex-direction: column; gap: 10px; }
            .search-container { width: 100%; }
            .btn-search-submit { width: 100%; }
        }
    </style>
</head>

<body>

    <div class="top-section">
        <div class="header-content-group">
            <img src="assets/text/logo.png" class="logo-top" alt="Logo">
            
            <h1 class="page-title-text">REPORT</h1>
            
            <p class="header-subtitle">add correct information to avoid data mismatch.</p>
        </div>
    </div>

    <div class="bottom-section">
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
            <div class="search-container">
                <input type="text" name="search_query" class="search-input" placeholder="SEARCH ENTRIES" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn-search-submit">SEARCH</button>
        </form>

        <?php if ($has_searched): ?>
            <div class="results-list">
                <?php if (count($search_results) > 0): ?>
                    <?php foreach($search_results as $row): ?>
                        
                        <button class="report-pill" onclick='openReportDetail(
                            <?php echo json_encode($row["id"]); ?>,
                            <?php echo json_encode($row["publisher"]); ?>,
                            <?php echo json_encode($row["author"]); ?>,
                            <?php echo json_encode($row["count"]); ?>,
                            <?php echo json_encode($row["books"]); ?>
                        )'>
                            <span class="rep-title"><?php echo htmlspecialchars($row['publisher']); ?></span>
                            <span class="rep-details"><?php echo htmlspecialchars($row['author']); ?> | <?php echo htmlspecialchars($row['count']); ?> Books</span>
                        </button>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">NO ENTRIES FOUND</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="return-footer">
            <a href="admin_view_database.php" style="text-decoration:none;">
                <div class="btn-return-wrap">
                    <img src="assets/text/btn-return.png" class="btn-return-img" alt="Return to Main Menu">
                </div>
            </a>
        </div>

    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="detail-card">
            <button class="close-card-x" onclick="closeReportDetail()">X</button>
            
            <div class="dt-header" id="dt-publisher">Publisher Name</div>
            <div class="dt-sub" id="dt-author-count">Author Name | 0 Books</div>
            <div class="dt-body" id="dt-books-container">
                <span class="books-label">List of Books:</span>
                <span id="dt-books">List content...</span>
            </div>

            <div id="edit-warning" class="warning-text">
                NOTE: Please put the correct information to avoid data mismatch.
            </div>

            <input type="text" id="edit-publisher" class="edit-input-field" placeholder="Edit Publisher Name">
            <input type="text" id="edit-author" class="edit-input-field" placeholder="Edit Author Name">
            <input type="number" id="edit-count" class="edit-input-field" placeholder="Edit Total Count">
            <textarea id="edit-books" class="edit-input-field" placeholder="Edit List of Books"></textarea>

            <div class="modal-actions">
                <button id="btn-edit" class="btn-action btn-edit" onclick="enableEditMode()">EDIT</button>
                <button id="btn-delete" class="btn-action btn-delete" onclick="handleDelete()">DELETE</button>
                
                <button id="btn-save" class="btn-action btn-save" onclick="handleSave()">SAVE</button>
                <button id="btn-cancel" class="btn-action btn-cancel" onclick="cancelEditMode()">CANCEL</button>
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="deleteConfirmModal">
        <div class="confirm-card">
            <div class="confirm-msg">Are you sure you want to delete this?</div>
            <div class="confirm-actions">
                <button class="btn-confirm-yes" onclick="confirmDelete()">CONFIRM</button>
                <button class="btn-confirm-no" onclick="closeDeleteConfirm()">CANCEL</button>
            </div>
        </div>
    </div>

    <div class="success-overlay" id="successModal">
        <div class="success-card">
            <div class="success-msg" id="success-msg-text">Entry successfully edited.</div>
            <button class="btn-done" onclick="closeSuccessModal()">DONE</button>
        </div>
    </div>

    <script>
        let currentReportId = null;

        function openReportDetail(id, publisher, author, count, books) {
            currentReportId = id;

            // Populate View Mode
            document.getElementById('dt-publisher').innerText = publisher;
            document.getElementById('dt-author-count').innerText = author + " | " + count + " Books";
            document.getElementById('dt-books').innerText = books;

            // Populate Edit Mode (Hidden Inputs)
            document.getElementById('edit-publisher').value = publisher;
            document.getElementById('edit-author').value = author;
            document.getElementById('edit-count').value = count;
            document.getElementById('edit-books').value = books;

            // Ensure we start in View Mode
            cancelEditMode();

            // Show the modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeReportDetail() {
            document.getElementById('detailModal').style.display = 'none';
        }

        function enableEditMode() {
            // Hide View Elements
            document.getElementById('dt-publisher').style.display = 'none';
            document.getElementById('dt-author-count').style.display = 'none';
            document.getElementById('dt-books-container').style.display = 'none';
            document.getElementById('btn-edit').style.display = 'none';
            document.getElementById('btn-delete').style.display = 'none';

            // Show Edit Inputs & Warning
            document.getElementById('edit-warning').style.display = 'block'; 
            document.getElementById('edit-publisher').style.display = 'block';
            document.getElementById('edit-author').style.display = 'block';
            document.getElementById('edit-count').style.display = 'block';
            document.getElementById('edit-books').style.display = 'block';
            document.getElementById('btn-save').style.display = 'block';
            document.getElementById('btn-cancel').style.display = 'block';
        }

        function cancelEditMode() {
            // Show View Elements
            document.getElementById('dt-publisher').style.display = 'block';
            document.getElementById('dt-author-count').style.display = 'block';
            document.getElementById('dt-books-container').style.display = 'block';
            document.getElementById('btn-edit').style.display = 'block';
            document.getElementById('btn-delete').style.display = 'block';

            // Hide Edit Inputs & Warning
            document.getElementById('edit-warning').style.display = 'none'; 
            document.getElementById('edit-publisher').style.display = 'none';
            document.getElementById('edit-author').style.display = 'none';
            document.getElementById('edit-count').style.display = 'none';
            document.getElementById('edit-books').style.display = 'none';
            document.getElementById('btn-save').style.display = 'none';
            document.getElementById('btn-cancel').style.display = 'none';
        }

        function handleSave() {
            // UI Simulation Only (Requires AJAX for DB Update)
            let newPub = document.getElementById('edit-publisher').value;
            let newAuth = document.getElementById('edit-author').value;
            let newCount = document.getElementById('edit-count').value;
            let newBooks = document.getElementById('edit-books').value;

            document.getElementById('dt-publisher').innerText = newPub;
            document.getElementById('dt-author-count').innerText = newAuth + " | " + newCount + " Books";
            document.getElementById('dt-books').innerText = newBooks;

            document.getElementById('detailModal').style.display = 'none';
            document.getElementById('success-msg-text').innerText = "Entry successfully edited.";
            document.getElementById('successModal').style.display = 'flex';
        }

        /* --- DELETE LOGIC --- */

        function handleDelete() {
            if(currentReportId) {
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            }
        }

        function closeDeleteConfirm() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        function confirmDelete() {
            // UI Simulation Only (Requires AJAX for DB Delete)
            document.getElementById('deleteConfirmModal').style.display = 'none';
            document.getElementById('detailModal').style.display = 'none';
            document.getElementById('success-msg-text').innerText = "Entry successfully deleted.";
            document.getElementById('successModal').style.display = 'flex';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>

</body>
</html>