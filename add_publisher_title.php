<?php
$publisher_search = "";
$title_search     = "";
$insert_success   = false;

require_once __DIR__ . "/bc_add_pub_title.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Publisher & Title</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* USING SAME STYLING AS PREVIOUS FILES */
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-add-color: #8f8989;    
            --btn-return-color: #3c4862; 
            --success-bg: #20252d;
            --header-font: 'Cinzel', serif;
            --body-font: 'Montserrat', sans-serif;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0; min-height: 100vh;
            font-family: var(--body-font);
            background-color: var(--light-bg);
            display: flex; flex-direction: column;
        }

        /* HEADER */
        .top-section {
            background-color: var(--dark-bg); height: 250px;
            display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative;
        }
        /* Enlarged Logo */
        .logo-top { position: absolute; top: 30px; left: 40px; width: 180px; }
        
        .page-title-img { width: 500px; max-width: 85%; height: auto; margin-top: 30px; }
        .instruction-text { color: white; font-size: 14px; margin-top: 15px; opacity: 0.9; }

        /* MAIN SELECTION AREA */
        .selection-container {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 25px; padding: 50px;
        }
        
        /* Enlarged Main Buttons */
        .big-btn {
            padding: 20px 0; width: 350px;
            border-radius: 50px; border: none;
            font-family: var(--header-font); font-weight: 700; font-size: 18px;
            color: white; cursor: pointer; text-transform: uppercase;
            box-shadow: 0 6px 15px rgba(0,0,0,0.25); transition: transform 0.2s;
            text-decoration: none; display: flex; justify-content: center; align-items: center;
        }
        .big-btn:hover { transform: scale(1.03); }
        .btn-add { background-color: var(--btn-add-color); }
        .btn-return { background-color: var(--btn-return-color); }

        /* MODAL FORMS */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(5px);
            display: none; justify-content: center; align-items: center; z-index: 1000;
        }

        .form-card {
            background-color: white; width: 900px; max-width: 95vw;
            padding: 40px; border-radius: 20px; position: relative;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        .close-icon {
            position: absolute; top: 20px; right: 20px; font-size: 24px; cursor: pointer; color: #555;
        }

        .form-title {
            font-family: var(--header-font); font-size: 24px; color: var(--dark-bg);
            text-align: center; margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 10px;
        }

        /* GRID FORM */
        .form-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 25px; text-align: left;
        }
        .full-width { grid-column: 1 / -1; }

        .input-group { display: flex; flex-direction: column; }
        
        .text-label {
            font-family: var(--header-font); font-size: 16px; font-weight: 700; color: #444;
            margin-bottom: 8px; margin-left: 10px; text-transform: uppercase;
        }
        /* Enlarged Inputs */
        .table-input {
            width: 100%; padding: 15px 20px; border-radius: 50px; border: 1px solid #ccc;
            background-color: var(--input-bg); font-family: var(--body-font); font-size: 15px;
        }

        .submit-btn-container {
            margin-top: 40px; display: flex; justify-content: center;
        }
        .submit-btn {
            color: white; border: none; background-color: var(--btn-add-color);
            padding: 15px 60px; border-radius: 30px; font-weight: 700; cursor: pointer;
            font-family: var(--body-font); text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-size: 16px;
        }
        .submit-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* SUCCESS MODAL (Dark Theme) */
        .success-box {
            background-color: var(--success-bg); width: 500px; padding: 50px; border-radius: 15px; text-align: center; color: white; display: flex; flex-direction: column; align-items: center; gap: 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.8);
        }
        .success-text { font-family: var(--header-font); font-size: 28px; line-height: 1.3; }
        .btn-done {
            background-color: #f0f0f0; color: #20252d; border: none; padding: 12px 50px; border-radius: 30px; font-weight: 700; cursor: pointer; font-family: var(--body-font);
        }

        .hidden { display: none !important; }
    </style>
</head>

<body>

<div class="modal-overlay <?php echo $show_modal ? '' : 'hidden'; ?>" id="successModal" style="<?php echo $show_modal ? 'display:flex;' : ''; ?>">
    <div class="success-box">
        <h2 class="success-text"><?php echo $success_message; ?></h2>
        <button class="btn-done" onclick="window.location.href='add_publisher_title.php'">DONE</button>
    </div>
</div>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-publishers-titles.png" class="page-title-img" alt="Publishers & Titles">
    <p class="instruction-text">Complete the Publisher information first, then proceed to Title information.</p>
</div>

<div class="selection-container">
    <button class="big-btn btn-add" onclick="openModal('pubModal')">ADD NEW INPUT</button>
    <a href="add_database.php" class="big-btn btn-return">Return to Main Menu</a>
</div>

<div class="modal-overlay hidden" id="pubModal">
    <div class="form-card">
        <span class="close-icon" onclick="closeModal('pubModal')">&times;</span>
        <div class="form-title">STEP 1: ADD NEW PUBLISHER</div>
        
        <form id="pubForm" onsubmit="handlePublisherSubmit(event)">
            <div class="form-grid">
                <div class="input-group full-width">
                    <label class="text-label">Publisher Name</label>
                    <input type="text" id="pub_name" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">City</label>
                    <input type="text" id="city" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">State</label>
                    <input type="text" id="state" class="table-input">
                </div>
                <div class="input-group full-width">
                    <label class="text-label">Country</label>
                    <input type="text" id="country" class="table-input" required>
                </div>
            </div>
            <div class="submit-btn-container">
                <button type="submit" class="submit-btn">CONFIRM & PROCEED TO TITLE</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="titleModal">
    <div class="form-card">
        <span class="close-icon" onclick="closeModal('titleModal')">&times;</span>
        <div class="form-title">STEP 2: ADD NEW TITLE</div>
        
        <form method="POST">
            <input type="hidden" name="action" value="add_title">
            
            <input type="hidden" name="pub_id" id="hidden_pub_id">

            <div class="form-grid">
                <div class="input-group full-width">
                    <label class="text-label">Title Name</label>
                    <input type="text" name="title" class="table-input" required>
                </div>
                
                <div class="input-group">
                    <label class="text-label">Type</label>
                    <input type="text" name="type" class="table-input" placeholder="e.g. business" required>
                </div>
                
                <div class="input-group">
                    <label class="text-label">Pub Date</label>
                    <input type="date" name="pubdate" class="table-input" required>
                </div>

                <div class="input-group">
                    <label class="text-label">Price</label>
                    <input type="text" name="price" class="table-input">
                </div>

                <div class="input-group">
                    <label class="text-label">Advance</label>
                    <input type="text" name="advance" class="table-input">
                </div>
                
                <div class="input-group">
                    <label class="text-label">Royalty</label>
                    <input type="number" name="royalty" class="table-input">
                </div>

                <div class="input-group">
                    <label class="text-label">YTD Sales</label>
                    <input type="number" name="ytd_sales" class="table-input">
                </div>

                <div class="input-group full-width">
                    <label class="text-label">Notes</label>
                    <input type="text" name="notes" class="table-input">
                </div>
            </div>
            <div class="submit-btn-container">
                <button type="submit" class="submit-btn" style="background-color: var(--btn-return-color);">FINISH & SAVE TITLE</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).style.display = 'none';
    }

    // Handles Step 1 (Publisher) submission via AJAX
    function handlePublisherSubmit(e) {
        e.preventDefault(); 

        const data = {
            pub_name: document.getElementById('pub_name').value,
            city: document.getElementById('city').value,
            state: document.getElementById('state').value,
            country: document.getElementById('country').value
        };

        // Call the AJAX endpoint at top of file
        fetch('?ajax_add_publisher=1', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                // 1. Close Publisher Modal
                closeModal('pubModal');
                
                // 2. Set the generated (or found) PUB_ID in the Title Form
                document.getElementById('hidden_pub_id').value = result.pub_id;
                
                // 3. Open Title Modal
                openModal('titleModal');
            } else {
                alert('Error adding publisher: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Check console.');
        });
    }
</script>

</body>
</html>