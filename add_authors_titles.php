<?php
$has_results  = false;
$results_list = [];
$author_search = "";
$title_search  = "";

require_once __DIR__ . "/bc_add_au_t.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Author | Ink & Solace</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-confirm: #8f8989;
            --btn-return: #3c4862;
            --footer-text: #666666;
            --transition: all 0.3s ease;
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
            min-height: 250px; 
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .logo-top {
            position: absolute;
            top: 20px; left: 30px;
            width: 150px;
        }

        .page-title-img {
            width: 520px;
            max-width: 85%;
            height: auto;
            margin-top: 30px;
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
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            width: 100%;
            max-width: 900px; /* Wider container for grid */
        }

        form {
            width: 100%;
        }

        /* ================= GRID SYSTEM ================= */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two columns */
            gap: 20px 40px; /* row-gap col-gap */
            margin-bottom: 30px;
            text-align: left;
        }

        /* Make address span the full width */
        .full-width { grid-column: 1 / -1; }

        /* ================= INPUT STYLES ================= */
        .input-group {
            display: flex;
            flex-direction: column;
        }

        .text-label {
            font-family: 'Cinzel', serif;
            font-size: 16px;
            font-weight: 700;
            color: #444;
            margin-bottom: 8px;
            margin-left: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper { position: relative; }

        input[type="text"] {
            width: 100%;
            padding: 12px 20px; 
            border-radius: 50px;
            border: 1px solid #ccc; /* Subtle border added for definition */
            outline: none;
            background-color: var(--input-bg);
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        input:focus {
            background-color: white;
            border-color: #999;
        }

        /* ================= BUTTONS ================= */
        .btn-center-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-confirm-wrap {
            background-color: var(--btn-confirm);
            border-radius: 50px;
            padding: 10px 40px;
            display: inline-flex;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn-return-wrap {
            background-color: var(--btn-return);
            border-radius: 50px;
            padding: 12px 40px;
            display: inline-flex;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
        }

        .btn-img-confirm { width: 80px; }
        .btn-img-return { width: 160px; }

        .btn-confirm-wrap:hover, .btn-return-wrap:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* ================= MODAL PROMPT ================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: none; justify-content: center; align-items: center; z-index: 1000;
        }

        .modal-box {
            background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 400px;
            text-align: center; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-icon {
            position: absolute; top: 15px; right: 15px; font-size: 24px; cursor: pointer; color: #999; line-height: 1;
        }

        .modal-box h2 {
            font-family: 'Montserrat', sans-serif; color: var(--dark-bg); margin-bottom: 20px; font-size: 22px; 
        }

        .success-icon { color: #4CAF50; font-size: 40px; margin-bottom: 10px; display: block; }

        .btn-done {
            background-color: var(--btn-return); color: white; border: none; padding: 10px 30px;
            border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: bold;
            cursor: pointer; transition: var(--transition);
        }

        .btn-done:hover { background-color: #506180; }

        /* Mobile Responsive */
        @media (max-width: 700px) {
            .form-grid { grid-template-columns: 1fr; } /* Stack to 1 column on mobile */
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
    <img src="assets/text/title-authors-titles.png" class="page-title-img" alt="Authors & Titles">
    <p class="instruction-text">Please fill in all author details accurately to ensure data consistency.</p>
</div>

<div class="bottom-section">
    <div class="form-container">
        <form id="authorForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return showPrompt(event)">

            <div class="form-grid">
                <div class="input-group">
                    <label class="text-label">Author ID</label>
                    <div class="input-wrapper">
                        <input type="text" name="au_id" placeholder="e.g. 172-32-1176" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">Phone</label>
                    <div class="input-wrapper">
                        <input type="text" name="phone" placeholder="e.g. 408 496-7223" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">First Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="au_fname" placeholder="Enter first name" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">Last Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="au_lname" placeholder="Enter last name" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label class="text-label">Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address" placeholder="Enter full street address" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">City</label>
                    <div class="input-wrapper">
                        <input type="text" name="city" placeholder="e.g. Menlo Park" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">State</label>
                    <div class="input-wrapper">
                        <input type="text" name="state" placeholder="e.g. CA" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">Zip Code</label>
                    <div class="input-wrapper">
                        <input type="text" name="zip" placeholder="e.g. 94025" required>
                    </div>
                </div>

                <div class="input-group">
                    <label class="text-label">Number of Contracts</label>
                    <div class="input-wrapper">
                        <input type="text" name="contract" placeholder="e.g. 1" required>
                    </div>
                </div>
            </div>

            <div class="btn-center-wrapper">
                <button type="submit" class="btn-confirm-wrap">
                    <img src="assets/text/btn-confirm.png" class="btn-img-confirm" alt="Confirm">
                </button>

                <a href="admin_view_database.php" style="text-decoration:none;">
                    <div class="btn-return-wrap">
                        <img src="assets/text/btn-return.png" class="btn-img-return" alt="Return to Main Menu">
                    </div>
                </a>
            </div>

        </form>
    </div>
</div>

<script>
    function showPrompt(event) {
        event.preventDefault();
        document.getElementById('updateModal').style.display = 'flex';
        return false; 
    }

    function closeModal() {
        document.getElementById('updateModal').style.display = 'none';
        
        // Optional: clear form logic
        // document.getElementById('authorForm').reset();
    }
</script>

</body>
</html>