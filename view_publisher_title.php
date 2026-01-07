<?php
require_once __DIR__ . "/bc_us_view_pub_title.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers & Authors | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --btn-blue: #3c4862;
            --btn-grey: #8b8682;
            --pill-color: #918a86;
            --pill-hover: #a39c98;
            --input-bg: #f0f0f0;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-bg);
            display: flex;
            flex-direction: column;
        }

        /* ================= TOP SECTION ================= */
        .top-section {
            background-color: var(--dark-bg);
            min-height: 200px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
        }

        .logo-top {
            position: absolute;
            top: 30px;
            left: 40px;
            width: 120px;
        }

        .page-title-img {
            width: 520px;
            max-width: 80%;
            height: auto;
            margin-top: 40px;
        }

        /* ================= BOTTOM SECTION ================= */
        .bottom-section {
            background-color: var(--light-bg);
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        /* --- SEARCH FORM STYLES --- */
        .search-form {
            width: 100%;
            max-width: 700px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 30px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-label {
            font-family: 'Cinzel', serif;
            font-size: 24px;
            color: #555;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 15px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px 20px 15px 50px; 
            border-radius: 50px;
            border: none;
            background-color: var(--input-bg);
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            color: #333;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #555;
            stroke-width: 2;
        }

        /* --- BUTTONS --- */
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            padding: 15px 0;
            width: 250px;
            border-radius: 50px;
            border: none;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }

        .btn-confirm { background-color: var(--btn-grey); color: white; }
        .btn-return { background-color: var(--btn-blue); color: white; }

        /* ================= MODAL 1: RESULTS LIST ================= */
        .modal-overlay {
            position: fixed;
            top: 0; 
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75); 
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.4s ease-out;
        }

        .modal-header {
            font-family: 'Cinzel', serif;
            color: white;
            font-size: 32px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .results-scroll-container {
            max-height: 50vh;
            overflow-y: auto;
            width: fit-content; 
            min-width: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 5px 15px 5px 5px; 
        }

        .results-scroll-container::-webkit-scrollbar { width: 8px; }
        .results-scroll-container::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
        .results-scroll-container::-webkit-scrollbar-thumb { background: #fff; border-radius: 4px; }

        .result-pill {
            background-color: var(--pill-color);
            color: white;
            padding: 15px 30px; 
            width: 350px;
            border-radius: 50px;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }

        .result-pill:hover {
            transform: scale(1.02);
            background-color: var(--pill-hover);
        }

        .res-publisher {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .res-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 300;
        }

        .close-btn-circle {
            margin-top: 25px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: transparent;
            border: 2px solid white;
            color: white;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }
        .close-btn-circle:hover { background: white; color: var(--dark-bg); }

        /* ================= MODAL 2: DETAIL CARD ================= */
        .detail-card {
            background-color: var(--pill-color);
            color: white;
            width: 600px;
            max-width: 90vw;
            padding: 60px 40px;
            border-radius: 20px;
            position: relative;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .close-card-btn {
            position: absolute;
            top: -15px;
            right: -15px;
            width: 40px;
            height: 40px;
            background-color: white; 
            color: #555;
            border-radius: 50%;
            border: none;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: 0.2s;
        }
        .close-card-btn:hover { background-color: #f0f0f0; }

        .dt-publisher {
            font-family: 'Cinzel', serif;
            font-size: 38px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .dt-stats {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 18px;
            margin: 0 0 30px 0;
        }

        .dt-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-style: italic;
            font-weight: 400;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>

<body>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-publishers-titles.png" class="page-title-img" alt="Publishers & Titles">
</div>

<div class="bottom-section">

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
        <div class="input-group">
            <label class="input-label">Publisher</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="publisher" placeholder="SEARCH BY PUBLISHER" value="<?php echo htmlspecialchars($publisher_search); ?>">
            </div>
        </div>

        <div class="input-group">
            <label class="input-label">Title</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="title" placeholder="SEARCH BY TITLE" value="<?php echo htmlspecialchars($title_search); ?>">
            </div>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn btn-confirm">Confirm</button>
            <a href="menu.php" class="btn btn-return">Return to Main Menu</a>
        </div>
    </form>

</div>

<?php if ($has_results): ?>
    
    <div class="modal-overlay" id="resultModal">
        <div class="modal-header">RESULTS:</div>
        <div class="results-scroll-container">
            <?php foreach($results_list as $row): ?>
                
                <button class="result-pill" onclick='openDetail(
                    <?php echo json_encode($row["publisher"]); ?>, 
                    <?php echo json_encode($row["title"]); ?>, 
                    <?php echo json_encode($row["author"]); ?>, 
                    <?php echo json_encode($row["info"]); ?>
                )'>
                    <span class="res-publisher"><?php echo htmlspecialchars($row['publisher']); ?></span>
                    <span class="res-title"><?php echo htmlspecialchars($row['title']); ?></span>
                </button>

            <?php endforeach; ?>
        </div>
        <button class="close-btn-circle" onclick="document.getElementById('resultModal').style.display='none'">
            &times;
        </button>
    </div>

    <div class="modal-overlay" id="detailModal" style="display: none;">
        <div class="detail-card">
            <button class="close-card-btn" onclick="closeDetail()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <h1 class="dt-publisher" id="detail-pub">PUBLISHER</h1>
            <p class="dt-stats" id="detail-stats">STATS</p>
            <p class="dt-title" id="detail-title">Title by Author</p>
        </div>
    </div>

    <script>
        // Updated function to accept Author and Info
        function openDetail(publisher, title, author, info) {
            // 1. Hide the List Modal
            document.getElementById('resultModal').style.display = 'none';

            // 2. Populate data into Detail Modal
            document.getElementById('detail-pub').innerText = publisher;
            
            // Map the "info" column to the stats text
            document.getElementById('detail-stats').innerText = info; 
            
            // Construct the "Title by Author" text
            document.getElementById('detail-title').innerText = title + " by " + author;

            // 3. Show the Detail Modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeDetail() {
            // Close details and go back to results list
            document.getElementById('detailModal').style.display = 'none';
            document.getElementById('resultModal').style.display = 'flex';
        }
    </script>
<?php endif; ?>

</body>

</html>
