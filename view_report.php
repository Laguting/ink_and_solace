<?php
require_once __DIR__ . "/bc_us_view_rep.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-return: #3c4862;
            --pill-color: #918a86; 
            --pill-hover: #a39c98;
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
            gap: 40px; 
        }

        .logo-top { width: 180px; height: auto; }
        .page-title-img { width: 500px; max-width: 90%; height: auto; }

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

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            opacity: 0.5;
            color: #555;
        }

        .search-input {
            width: 100%;
            padding: 15px 15px 15px 55px;
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
        .modal-overlay {
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

        /* Styling for the books list */
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

        .btn-card-back {
            background-color: var(--btn-return);
            color: white;
            border: none;
            padding: 12px 60px;
            border-radius: 30px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            align-self: flex-end; 
        }
        .btn-card-back:hover { transform: translateY(-2px); }

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
        
        .btn-return-wrap:hover {
            transform: translateY(-2px);
        }

        .btn-return-img { width: 160px; height: auto; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 768px) {
            .page-title-img { width: 350px; }
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
            <img src="assets/text/title-report.png" class="page-title-img" alt="REPORT">
        </div>
    </div>

    <div class="bottom-section">
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
            <div class="search-container">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search_query" class="search-input" placeholder="SEARCH ENTRIES" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn-search-submit">SEARCH</button>
        </form>

        <?php if ($has_searched): ?>
            <div class="results-list">
                <?php if (count($search_results) > 0): ?>
                    <?php foreach($search_results as $row): ?>
                        
                        <button class="report-pill" onclick='openReportDetail(
                            <?php echo json_encode($row["publisher"]); ?>,
                            <?php echo json_encode($row["author"]); ?>,
                            <?php echo json_encode($row["count"]); ?>,
                            <?php echo json_encode($row["books"]); ?>
                        )'>
                            <span class="rep-title"><?php echo htmlspecialchars($row['publisher']); ?></span>
                            <span class="rep-details"><?php echo htmlspecialchars($row['author']); ?></span>
                        </button>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">NO ENTRIES FOUND</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="return-footer">
            <a href="menu.php" style="text-decoration:none;">
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
            
            <div class="dt-body">
                <span class="books-label">List of Books:</span>
                <span id="dt-books">List content...</span>
            </div>

            <button class="btn-card-back" onclick="closeReportDetail()">Back</button>
        </div>
    </div>

    <script>
        function openReportDetail(publisher, author, count, books) {
            // Update modal content with specific fields
            document.getElementById('dt-publisher').innerText = publisher;
            document.getElementById('dt-author-count').innerText = author + " | " + count;
            document.getElementById('dt-books').innerText = books;

            // Show the modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeReportDetail() {
            // Hide the modal   
            document.getElementById('detailModal').style.display = 'none';
        }
    </script>

</body>
</html>