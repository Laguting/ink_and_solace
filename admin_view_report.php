<?php
require_once __DIR__ . "/bc_view_rep.php";
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
        .rep-details { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 400; opacity: 0.9; letter-spacing: 1px; text-transform: uppercase; }

        /* MODAL STYLES */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(8px); display: none; justify-content: center; align-items: center; z-index: 2000; }
        .detail-card { background-color: var(--modal-bg); color: white; width: 650px; max-width: 90vw; padding: 50px; border-radius: 15px; position: relative; box-shadow: 0 25px 60px rgba(0,0,0,0.7); max-height: 85vh; overflow-y: auto; }
        .close-card-x { position: absolute; top: 20px; right: 20px; width: 35px; height: 35px; background: none; color: white; border: 2px solid white; border-radius: 50%; cursor: pointer; font-weight: bold; }
        
        .dt-header { font-family: 'Cinzel', serif; font-size: 36px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 2px; }
        .dt-subheader { font-family: 'Montserrat', sans-serif; font-size: 14px; opacity: 0.6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 15px; }

        /* BULLET LIST */
        .book-ul { list-style: none; padding: 0; margin: 0; }
        .book-li { padding: 12px 20px; background: rgba(255,255,255,0.05); margin-bottom: 10px; border-radius: 8px; font-size: 16px; border-left: 5px solid var(--pill-color); font-weight: 400; line-height: 1.4; }
        
        .btn-card-close { background-color: var(--btn-return); color: white; border: none; padding: 15px 50px; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-transform: uppercase; width: 100%; margin-top: 20px; transition: transform 0.2s; }
        .btn-card-close:hover { transform: scale(1.02); }

        .return-footer { margin-top: 50px; display: flex; justify-content: center; }
        .btn-return-wrap { background-color: var(--btn-return); padding: 12px 30px; border-radius: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s ease; }
        .btn-return-wrap:hover { transform: translateY(-2px); }
        .btn-return-img { width: 160px; height: auto; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>

<body>

    <div class="top-section">
        <div class="header-content-group">
            <img src="assets/text/logo.png" class="logo-top" alt="Logo">
            <h1 class="page-title-text">REPORT</h1>
            <p class="header-subtitle">Publisher Inventory and Registered Titles.</p>
        </div>
    </div>

    <div class="bottom-section">
        <form method="POST" class="search-form">
            <div class="search-container">
                <input type="text" name="search_query" class="search-input" placeholder="SEARCH PUBLISHER OR BOOK" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn-search-submit">SEARCH</button>
        </form>

        <?php if ($has_searched): ?>
            <div class="results-list">
                <?php if (count($grouped_results) > 0): ?>
                    <?php foreach($grouped_results as $pubName => $data): ?>
                        <button class="report-pill" onclick='openReportModal(<?php echo json_encode($data); ?>)'>
                            <div class="pill-left">
                                <span class="rep-title"><?php echo htmlspecialchars($pubName); ?></span>
                                <span class="rep-details"><?php echo $data['count']; ?> TOTAL BOOKS</span>
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
            <a href="add_database.php" style="text-decoration:none;">
                <div class="btn-return-wrap">
                    <img src="assets/text/btn-return.png" class="btn-return-img" alt="Return">
                </div>
            </a>
        </div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="detail-card">
            <button class="close-card-x" onclick="closeModal()">✕</button>
            <div class="dt-header" id="m-publisher">Publisher</div>
            <div class="dt-subheader" id="m-count">Total Books: 0</div>
            <div id="m-list-container">
                </div>
            <button class="btn-card-close" onclick="closeModal()">CLOSE</button>
        </div>
    </div>

    <script>
        function openReportModal(data) {
            document.getElementById('m-publisher').innerText = data.publisher;
            document.getElementById('m-count').innerText = "Total number of Books by that publisher: " + data.count;
            
            const listContainer = document.getElementById('m-list-container');
            listContainer.innerHTML = '';

            // Split titles by our special separator ||
            if (data.books && data.books.trim() !== "") {
                const titles = data.books.split('||');
                let listHtml = '<ul class="book-ul">';
                titles.forEach(title => {
                    if(title.trim() !== "") {
                        listHtml += `<li class="book-li">${title.trim()}</li>`;
                    }
                });
                listHtml += '</ul>';
                listContainer.innerHTML = listHtml;
            } else {
                listContainer.innerHTML = '<p style="text-align:center; opacity:0.5; padding: 20px;">No books registered under this publisher.</p>';
            }

            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modal when clicking outside the card
        window.onclick = function(event) {
            let modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>