<?php
require_once __DIR__ . "/bc_add_rep.php";    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books | Ink & Solace</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-return: #3c4862;
            --pill-color: #918a86; 
            --pill-hover: #a39c98;
            --modal-bg: #2e343e;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100vh; font-family: 'Montserrat', sans-serif; background-color: var(--light-bg); }
        body { display: flex; flex-direction: column; }

        /* TOP SECTION */
        .top-section { background-color: var(--dark-bg); height: 45vh; min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .header-content-group { display: flex; flex-direction: column; align-items: center; gap: 15px; }
        .logo-top { width: 180px; height: auto; }
        .page-title-text { font-family: 'Cinzel', serif; font-size: 80px; color: white; text-transform: uppercase; font-weight: 400; letter-spacing: 5px; margin: 0; line-height: 1; margin-bottom: 10px; }
        .header-subtitle { font-family: 'Montserrat', sans-serif; color: white; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; margin: 0; font-weight: 300; opacity: 0.8; }

        /* BOTTOM SECTION */
        .bottom-section { flex: 1; padding: 50px 10% 80px; display: flex; flex-direction: column; align-items: center; position: relative; }
        .search-form { width: 100%; display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 40px; }
        .search-container { width: 100%; max-width: 600px; position: relative; }
        .search-input { width: 100%; padding: 15px 25px; border-radius: 50px; border: 1px solid #ccc; background-color: var(--input-bg); outline: none; font-family: 'Montserrat', sans-serif; font-size: 18px; letter-spacing: 2px; text-transform: uppercase; color: #333; }
        .btn-search-submit { background-color: var(--btn-return); color: white; border: none; padding: 15px 30px; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; cursor: pointer; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.2s, background-color 0.2s; height: 54px; }
        .btn-search-submit:hover { transform: translateY(-2px); opacity: 0.9; }

        /* RESULTS LIST */
        .results-list { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 20px; width: 100%; max-width: 700px; }
        .report-pill { background-color: var(--pill-color); color: white; width: 100%; padding: 25px 40px; border-radius: 60px; display: flex; flex-direction: row; align-items: center; justify-content: space-between; text-align: left; box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: all 0.2s ease; cursor: pointer; border: none; }
        .report-pill:hover { transform: translateY(-3px); background-color: var(--pill-hover); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
        .pill-left { display: flex; flex-direction: column; }
        .rep-title { font-family: 'Cinzel', serif; font-size: 24px; text-transform: uppercase; margin-bottom: 5px; line-height: 1.2; font-weight: 700; }
        .rep-details { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 400; opacity: 0.9; letter-spacing: 1px; }
        .pill-arrow { font-size: 24px; opacity: 0.7; }
        .no-results { font-family: 'Cinzel', serif; font-size: 20px; color: #555; margin-top: 20px; }

        /* MODALS */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(8px); display: flex; justify-content: center; align-items: center; z-index: 2000; animation: fadeIn 0.3s ease-out; display: none; }
        
        .detail-card { background-color: var(--modal-bg); color: white; width: 800px; max-width: 90vw; padding: 50px; border-radius: 15px; position: relative; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.7); display: flex; flex-direction: column; align-items: center; max-height: 90vh; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1); }
        
        .close-card-x { position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; background-color: transparent; color: white; border-radius: 50%; border: 2px solid white; font-size: 20px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .close-card-x:hover { background-color: white; color: var(--modal-bg); transform: rotate(90deg); }
        
        .dt-header { font-family: 'Cinzel', serif; font-size: 36px; text-transform: uppercase; margin-bottom: 10px; width: 100%; letter-spacing: 2px; }
        .dt-subheader { font-family: 'Montserrat', sans-serif; font-size: 14px; opacity: 0.6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.2); width: 100%; padding-bottom: 15px; }

        /* LIST STYLES */
        .entries-container { width: 100%; display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px; text-align: left; }
        
        .entry-item-read { 
            background-color: rgba(255,255,255,0.05); 
            padding: 20px; 
            border-radius: 8px; 
            display: flex; 
            align-items: flex-start;
            border-bottom: 2px solid rgba(255,255,255,0.05);
        }

        .entry-number {
            font-family: 'Cinzel', serif; font-size: 24px; font-weight: 700; margin-right: 20px; color: var(--pill-color); min-width: 30px;
        }

        .entry-text-group { display: flex; flex-direction: column; width: 100%; }
        
        .entry-author-name { 
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 18px; color: #fff; margin-bottom: 10px; text-transform: uppercase;
        }

        /* --- BULLET STYLES --- */
        .book-ul { margin: 0; padding-left: 20px; list-style-type: disc; color: #e0e0e0; }
        .book-li { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 300; margin-bottom: 5px; line-height: 1.4; opacity: 0.9; }

        /* CLOSE BUTTON IN MODAL */
        .btn-card-close {
            background-color: var(--btn-return); color: white; border: none; padding: 12px 50px; border-radius: 50px; 
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-transform: uppercase; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); margin-top: 20px; transition: transform 0.2s;
        }
        .btn-card-close:hover { transform: translateY(-2px); }

        .return-footer { margin-top: 50px; display: flex; justify-content: center; }
        .btn-return-wrap { background-color: var(--btn-return); padding: 12px 30px; border-radius: 50px; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s ease; }
        .btn-return-wrap:hover { transform: translateY(-2px); }
        .btn-return-img { width: 160px; height: auto; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @media (max-width: 768px) { .page-title-text { font-size: 50px; } .logo-top { width: 130px; } .search-form { flex-direction: column; gap: 10px; } .search-container { width: 100%; } .btn-search-submit { width: 100%; } }
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
        <form method="POST" class="search-form">
            <div class="search-container">
                <input type="text" name="search_query" class="search-input" placeholder="SEARCH ENTRIES" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn-search-submit">SEARCH</button>
        </form>

        <?php if ($has_searched): ?>
            <div class="results-list">
                <?php if (count($grouped_results) > 0): ?>
                    <?php foreach($grouped_results as $publisherName => $entries): ?>
                        
                        <button class="report-pill" onclick='openGroupModal(
                            <?php echo json_encode($publisherName); ?>,
                            "<?php echo base64_encode(json_encode($entries)); ?>"
                        )'>
                            <div class="pill-left">
                                <span class="rep-title"><?php echo htmlspecialchars($publisherName); ?></span>
                                <span class="rep-details"><?php echo count($entries); ?> REGISTERED AUTHORS</span>
                            </div>
                            <div class="pill-arrow">➜</div>
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
            <button class="close-card-x" onclick="closeReportDetail()">✕</button>
            
            <div class="dt-header" id="modal-pub-title">Publisher Name</div>
            <div class="dt-subheader" id="modal-pub-stats">Total Books: 0</div>

            <div id="list-view-area" style="width: 100%;">
                <div class="entries-container" id="entries-list">
                    </div>
            </div>

            <button class="btn-card-close" onclick="closeReportDetail()">CLOSE</button>
        </div>
    </div>

    <script>
        // 1. OPEN MODAL (Handles Base64 Decoding)
        function openGroupModal(publisherName, encodedEntries) {
            
            // Decodes the safe Base64 string back into a JS Array
            const entriesArray = JSON.parse(atob(encodedEntries));

            // A. Set Header
            document.getElementById('modal-pub-title').innerText = publisherName;

            // B. Calculate Stats
            let totalBooks = 0;
            entriesArray.forEach(entry => {
                totalBooks += parseInt(entry.count || 0);
            });
            document.getElementById('modal-pub-stats').innerText = "Total Number of Books under this Publisher: " + totalBooks;

            // C. Generate List
            const listContainer = document.getElementById('entries-list');
            listContainer.innerHTML = '';

            entriesArray.forEach((entry, index) => {
                const div = document.createElement('div');
                div.className = 'entry-item-read'; 
                
                // Bullet Logic
                let bookString = entry.books || "";
                // Split by comma or newline
                let bookItems = bookString.split(/[,\n]+/);
                
                let bulletsHTML = '<ul class="book-ul">';
                bookItems.forEach(book => {
                    let cleanBook = book.trim();
                    if(cleanBook !== "") {
                        bulletsHTML += `<li class="book-li">${cleanBook}</li>`;
                    }
                });
                bulletsHTML += '</ul>';

                div.innerHTML = `
                    <div class="entry-number">${index + 1}.</div>
                    <div class="entry-text-group">
                        <div class="entry-author-name">${entry.author}</div>
                        <div class="entry-book-list">${bulletsHTML}</div>
                    </div>
                `;
                
                listContainer.appendChild(div);
            });
            
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeReportDetail() {
            document.getElementById('detailModal').style.display = 'none';
        }
    </script>

</body>
</html>