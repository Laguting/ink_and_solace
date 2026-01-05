<?php
require_once __DIR__ . "/bc_view_au_title.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ink & Solace Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --dark-header: #20252d; --body-bg: #dbdbdb; --input-bg: #eeeeee; --btn-grey: #8b8682; --btn-blue: #3c4862; --modal-bg: #3c4456; }
        body { margin: 0; font-family: 'Montserrat', sans-serif; background-color: var(--body-bg); }
        
        .header { background-color: var(--dark-header); height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative; }
        /* Added Logo CSS */
        .logo-top { position: absolute; top: 30px; left: 40px; width: 150px; }
        .page-title { width: 500px; max-width: 80%; }
        
        .main-content { display: flex; flex-direction: column; align-items: center; padding: 40px 20px; }
        .search-box { width: 100%; max-width: 800px; }
        .label { font-family: 'Cinzel', serif; color: #666; font-size: 20px; margin: 25px 0 10px; display: block; }
        .input-field { width: 100%; padding: 18px; border-radius: 50px; border: none; background: var(--input-bg); font-size: 16px; margin-bottom: 15px; outline: none; }
        .btn { padding: 15px; width: 310px; border-radius: 50px; border: none; color: white; font-weight: 600; cursor: pointer; font-family: 'Montserrat'; margin-bottom: 15px; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(8px); background: rgba(0,0,0,0.5); }
        .results-container { display: flex; flex-direction: column; align-items: center; width: 550px; }
        .results-list { max-height: 450px; overflow-y: auto; padding: 10px; width: 100%; display: flex; flex-direction: column; align-items: center; }
        
        .result-btn { 
            background: #918a86; 
            color: white; 
            width: 100%; 
            padding: 18px; 
            border-radius: 50px; 
            border: none; 
            margin-bottom: 15px; 
            cursor: pointer; 
            text-align: center; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); 
        }

        .status-card { background: #1e2229; padding: 50px; border-radius: 15px; text-align: center; color: white; position: relative; width: 450px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .status-card h2 { font-family: 'Cinzel'; font-size: 24px; margin-bottom: 30px; line-height: 1.4; text-transform: uppercase; }
        .done-btn { background: #eeeeee; color: black; border: none; padding: 10px 60px; border-radius: 20px; font-weight: bold; cursor: pointer; font-family: 'Montserrat'; text-transform: uppercase; }

        .detail-card { background: var(--modal-bg); width: 1350px; max-width: 95vw; padding: 40px; border-radius: 20px; color: white; position: relative; }
        .section-title { font-family: 'Cinzel'; font-size: 18px; margin: 25px 0 10px; text-transform: uppercase; }
        .table-wrap { background: white; border-radius: 5px; overflow: hidden; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background: #f8f9fa; color: #555; font-size: 11px; padding: 12px; border-bottom: 1px solid #ddd; text-transform: uppercase; }
        .grid-input { width: 100%; border: 1px solid transparent; padding: 12px 5px; text-align: center; font-family: inherit; font-size: 13px; color: #333; background: transparent; transition: 0.3s; }
        
        .close-circle { width: 35px; height: 35px; border: 2px solid white; border-radius: 50%; color: white; background: transparent; cursor: pointer; font-weight: bold; font-size: 16px; display: flex; align-items: center; justify-content: center; margin-top: 10px; }
        .close-corner { position: absolute; top: 15px; right: 15px; }
        .hidden { display: none !important; }
    </style>
</head>
<body>

<div class="header">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-publishers-titles.png" class="page-title" alt="Publishers & Titles">
</div>

<div class="main-content">
    <form class="search-box" method="POST">
        <label class="label">PUBLISHER</label>
        <input type="text" name="pub_search" class="input-field" placeholder="Enter Publisher Name" value="<?= htmlspecialchars($pub_search) ?>">
        <label class="label">TITLE</label>
        <input type="text" name="title_search" class="input-field" placeholder="Enter Book Title" value="<?= htmlspecialchars($title_search) ?>">
        <div style="display:flex; flex-direction:column; align-items:center; margin-top:30px;">
            <button type="submit" class="btn" style="background: var(--btn-grey);">Search</button>
            <a href="admin_view_database.php" class="btn" style="background: var(--btn-blue); text-decoration:none; text-align:center; display:flex; align-items:center; justify-content:center;">Return to Main Menu</a>
        </div>
    </form>
</div>

<?php if($has_results): ?>
<div class="modal-overlay" id="resultsModal" style="display:flex;">
    <div class="results-container">
        <h2 style="color:white; font-family:'Cinzel'; font-size:38px; margin-bottom:25px;">RESULTS</h2>
        <div class="results-list">
            <?php foreach($results_list as $row): ?>
            <button class="result-btn" onclick='showDetails(<?= json_encode($row) ?>)'>
                <div style="font-family:'Cinzel'; font-size:17px; text-transform:uppercase; margin-bottom: 5px;">
                    <?= htmlspecialchars($row['pub_name'] ?? 'N/A') ?>
                </div>
                <div style="font-size:12px; opacity:0.8; text-transform:uppercase;">
                    TITLE: <?= htmlspecialchars($row['title']) ?>
                </div>
            </button>
            <?php endforeach; ?>
        </div>
        <button class="close-circle" onclick="document.getElementById('resultsModal').style.display='none'">✕</button>
    </div>
</div>
<?php endif; ?>

<div class="modal-overlay" id="statusModal">
    <div class="status-card">
        <button class="close-circle close-corner" onclick="closeStatus()">✕</button>
        <h2 id="statusText">NO RECORDS FOUND<br>MATCHING YOUR SEARCH.</h2>
        <button class="done-btn" id="statusBtn" onclick="closeStatus()">OK</button>
    </div>
</div>

<script>
    function closeStatus() {
        document.getElementById('statusModal').style.display = 'none';
    }

    function showStatus(message, btnText = 'DONE') {
        document.getElementById('detailModal').style.display = 'none';
        if (document.getElementById('resultsModal')) {
             document.getElementById('resultsModal').style.display = 'none';
        }
        document.getElementById('statusText').innerText = message;
        document.getElementById('statusBtn').innerText = btnText;
        document.getElementById('statusModal').style.display = 'flex';
    }

    function showDetails(data) {
        document.getElementById('resultsModal').style.display = 'none';
        document.getElementById('detailModal').style.display = 'flex';
        
        document.getElementById('p_id').value = data.pub_id || '';
        document.getElementById('p_name').value = data.pub_name || '';
        document.getElementById('p_city').value = data.city || '';
        document.getElementById('p_state').value = data.state || '';
        document.getElementById('p_country').value = data.country || '';
        document.getElementById('t_id').value = data.title_id || '';
        document.getElementById('t_title').value = data.title || '';
        document.getElementById('t_type').value = data.type || '';
        document.getElementById('t_price').value = data.price || '0.00';
        document.getElementById('t_advance').value = data.advance || '0.00';
        document.getElementById('t_royalty').value = data.royalty || '0';
        document.getElementById('t_ytd').value = data.ytd_sales || '0';
        document.getElementById('t_date').value = data.pubdate || '';
    }

    function goBackToResults() {
        document.getElementById('detailModal').style.display = 'none';
        if (document.getElementById('resultsModal')) {
            document.getElementById('resultsModal').style.display = 'flex';
        }
    }

    function enableEdit() {
        document.querySelectorAll('.mode-toggle').forEach(el => { 
            el.readOnly = false; 
            el.style.background = "#fff"; 
            el.style.border = "1px solid #ddd"; 
        });
        document.getElementById('viewBtns').classList.add('hidden');
        document.getElementById('editBtns').classList.remove('hidden');
    }

    function saveChanges() {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('pub_id', document.getElementById('p_id').value);
        formData.append('pub_name', document.getElementById('p_name').value);
        formData.append('city', document.getElementById('p_city').value);
        formData.append('state', document.getElementById('p_state').value);
        formData.append('country', document.getElementById('p_country').value);
        formData.append('title_id', document.getElementById('t_id').value);
        formData.append('title', document.getElementById('t_title').value);
        formData.append('type', document.getElementById('t_type').value);
        formData.append('price', document.getElementById('t_price').value);
        formData.append('advance', document.getElementById('t_advance').value);
        formData.append('royalty', document.getElementById('t_royalty').value);
        formData.append('ytd_sales', document.getElementById('t_ytd').value);
        formData.append('notes', '');
        formData.append('pubdate', document.getElementById('t_date').value);

        fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => res.json()).then(data => { 
            if(data.status === 'success') showStatus(data.message); 
        });
    }

    function deleteRecord() {
        if(confirm("Are you sure you want to delete this title?")) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('title_id', document.getElementById('t_id').value);

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json()).then(data => { 
                if(data.status === 'success') showStatus(data.message); 
            });
        }
    }
</script>

<?php if($no_results): ?>
<script>
    document.getElementById('statusModal').style.display = 'flex';
    document.getElementById('statusBtn').innerText = 'OK';
</script>
<?php endif; ?>

<div class="modal-overlay" id="detailModal">
    <div class="detail-card">
        <div class="close-corner">
            <button class="close-circle" onclick="goBackToResults()">✕</button>
        </div>
        
        <div class="section-title">PUBLISHER INFORMATION</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>PUB_ID</th><th>Publisher Name</th><th>City</th><th>State</th><th>Country</th></tr></thead>
                <tbody><tr>
                    <td><input id="p_id" class="grid-input" readonly style="background:#f0f0f0;"></td>
                    <td><input id="p_name" class="grid-input mode-toggle" readonly></td>
                    <td><input id="p_city" class="grid-input mode-toggle" readonly></td>
                    <td><input id="p_state" class="grid-input mode-toggle" readonly></td>
                    <td><input id="p_country" class="grid-input mode-toggle" readonly></td>
                </tr></tbody>
            </table>
        </div>

        <div class="section-title">TITLE INFORMATION</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title ID</th><th>Title</th><th>Type</th><th>Price</th><th>Advance</th><th>Royalty</th><th>YTD Sales</th><th>Pub Date</th></tr></thead>
                <tbody><tr>
                    <td><input id="t_id" class="grid-input" readonly style="background:#f0f0f0;"></td>
                    <td><input id="t_title" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_type" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_price" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_advance" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_royalty" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_ytd" class="grid-input mode-toggle" readonly></td>
                    <td><input id="t_date" class="grid-input mode-toggle" readonly></td>
                </tr></tbody>
            </table>
        </div>

        <div id="viewBtns" style="display:flex; justify-content:center; gap:20px; margin-top:30px;">
            <button class="btn" style="background:#8b8682; width:180px;" onclick="enableEdit()">EDIT</button>
            <button class="btn" style="background:#800000; width:180px;" onclick="deleteRecord()">DELETE</button>
        </div>

        <div id="editBtns" class="hidden" style="display:flex; justify-content:center; gap:20px; margin-top:30px;">
            <button class="btn" style="background:#8b8682; width:180px;" onclick="saveChanges()">SAVE</button>
            <button class="btn" style="background:#800000; width:180px;" onclick="location.reload()">CANCEL</button>
        </div>
    </div>
</div>
</body>
</html>
