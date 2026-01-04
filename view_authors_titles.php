<?php
require_once __DIR__ . "/bc_us_view_au_title.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Results | Ink & Solace</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --btn-blue: #3c4862;
            --btn-grey: #8b8682; 
            --pill-color: #918a86;
            --pill-hover: #a39c98;
            --input-bg: #f0f0f0;
            --text-color: #3c4862;
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
        }

        .logo-top {
            position: absolute;
            top: 30px;
            left: 40px;
            width: 120px;
        }

        .page-title-img {
            width: 500px;
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

        /* ================= MODAL 1: RESULTS LIST (Pill Buttons) ================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.75); 
            backdrop-filter: blur(8px);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            z-index: 1000; animation: fadeIn 0.4s ease-out;
        }

        .modal-header {
            font-family: 'Cinzel', serif; color: white; font-size: 32px;
            margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .results-scroll-container {
            max-height: 50vh; overflow-y: auto; width: fit-content; min-width: 320px;
            display: flex; flex-direction: column; align-items: center; gap: 15px;
            padding: 5px 15px 5px 5px; 
        }

        /* Custom Scrollbar */
        .results-scroll-container::-webkit-scrollbar { width: 8px; }
        .results-scroll-container::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
        .results-scroll-container::-webkit-scrollbar-thumb { background: #fff; border-radius: 4px; }

        .result-pill {
            background-color: var(--pill-color); color: white;
            padding: 15px 30px; width: 350px; border-radius: 50px; border: none;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            cursor: pointer; transition: transform 0.2s, background-color 0.2s;
        }
        .result-pill:hover { transform: scale(1.02); background-color: var(--pill-hover); }

        .res-main { font-family: 'Cinzel', serif; font-size: 20px; text-transform: uppercase; margin-bottom: 3px; }
        .res-sub { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 300; }

        /* The X close button for List Modal */
        .close-btn-circle {
            margin-top: 25px; width: 45px; height: 45px; border-radius: 50%;
            background: transparent; border: 2px solid white; color: white; font-size: 22px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.2s;
        }
        .close-btn-circle:hover { background: white; color: var(--dark-bg); }


        /* ================= MODAL 2: TABLE DETAIL CARD ================= */
        .detail-card-container {
            background-color: #918a86; 
            width: 1000px;
            max-width: 90vw;
            padding: 50px 40px;
            border-radius: 20px;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            align-items: flex-end; 
        }

        .close-card-x {
            position: absolute; top: -15px; right: -15px;
            width: 40px; height: 40px;
            background-color: white; color: black;
            border-radius: 50%; border: none; font-size: 20px; font-weight: bold;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: 0.2s;
        }
        .close-card-x:hover { transform: scale(1.1); }

        /* THE TABLE STYLES */
        .info-table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 20px 15px;
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            color: #333;
            vertical-align: middle;
        }

        .info-table th {
            font-weight: 700;
            font-size: 16px;
        }

        .info-table td {
            font-weight: 400;
            font-size: 15px;
            line-height: 1.4;
            min-height: 50px; 
        }

        .btn-card-back {
            background-color: var(--btn-blue);
            color: white;
            border: none;
            padding: 12px 50px;
            border-radius: 30px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .btn-card-back:hover { transform: translateY(-2px); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        @media (max-width: 800px) {
            .info-table th, .info-table td { font-size: 12px; padding: 10px; }
            .info-table { display: block; overflow-x: auto; }
        }
    </style>
</head>

<body>

<div class="top-section">
    <img src="assets/text/logo-img.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-authors-titles.png" class="page-title-img" alt="Authors & Titles">
</div>

<div class="bottom-section">
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
        <div class="input-group">
            <label class="input-label">Author</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="author" placeholder="SEARCH" value="<?php echo htmlspecialchars($author_search); ?>">
            </div>
        </div>

        <div class="input-group">
            <label class="input-label">Title</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="title" placeholder="SEARCH" value="<?php echo htmlspecialchars($title_search); ?>">
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
                
                <button class="result-pill" type="button" onclick='openTableDetail(
                    <?php echo json_encode($row["isbn"]); ?>,
                    <?php echo json_encode($row["title"]); ?>,
                    <?php echo json_encode($row["author"]); ?>,
                    <?php echo json_encode($row["publisher"]); ?>,
                    <?php echo json_encode($row["address"]); ?>
                )'>
                    <span class="res-main"><?php echo htmlspecialchars($row['publisher']); ?></span>
                    <span class="res-sub"><?php echo htmlspecialchars($row['title']); ?></span>
                </button>

            <?php endforeach; ?>
        </div>
        <button class="close-btn-circle" onclick="document.getElementById('resultModal').style.display='none'">
            &times;
        </button>
    </div>

    <div class="modal-overlay" id="detailModal" style="display: none;">
        <div class="detail-card-container">
            <button class="close-card-x" onclick="closeTableDetail()">X</button>
            
            <table class="info-table">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Publisher Address</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="td_isbn"></td>
                        <td id="td_title"></td>
                        <td id="td_author"></td>
                        <td id="td_publisher"></td>
                        <td id="td_address"></td>
                    </tr>
                </tbody>
            </table>

            <button class="btn-card-back" onclick="closeTableDetail()">BACK</button>
        </div>
    </div>

    <script>
        // Function to Open the Table Modal and Fill Data
        function openTableDetail(isbn, title, author, publisher, address) {
            // 1. Hide List
            document.getElementById('resultModal').style.display = 'none';

            // 2. Fill Table Data (JavaScript handles putting info inside)
            document.getElementById('td_isbn').innerText = isbn;
            document.getElementById('td_title').innerText = title;
            document.getElementById('td_author').innerText = author;
            document.getElementById('td_publisher').innerText = publisher;
            document.getElementById('td_address').innerText = address;

            // 3. Show Table Modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        // Function to Close Table Modal and return to List
        function closeTableDetail() {
            document.getElementById('detailModal').style.display = 'none';
            document.getElementById('resultModal').style.display = 'flex';
        }
    </script>
<?php endif; ?>

</body>

</html>