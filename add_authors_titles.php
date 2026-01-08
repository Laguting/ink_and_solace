<?php
require_once "bc_add_au_t.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Entry | Ink & Solace</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --input-bg: #f2f2f2;
            --btn-add-color: #8f8989;    
            --btn-return-color: #3c4862; 
            --success-bg: #20252d;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0; min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light-bg);
            display: flex; flex-direction: column;
        }

        /* HEADER */
        .top-section {
            background-color: var(--dark-bg); height: 250px;
            display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative;
        }
        .logo-top { position: absolute; top: 20px; left: 30px; width: 150px; }
        .page-title-img { width: 520px; max-width: 85%; height: auto; margin-top: 30px; }
        .instruction-text { color: white; font-size: 14px; margin-top: 15px; opacity: 0.9; }

        /* MAIN SELECTION AREA */
        .selection-container {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 25px; padding: 50px;
        }

        .big-btn {
            padding: 20px 0; width: 320px;
            border-radius: 50px; border: none;
            font-family: 'Cinzel', serif; font-weight: 700; font-size: 18px;
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
            font-family: 'Cinzel', serif; font-size: 24px; color: var(--dark-bg);
            text-align: center; margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 10px;
        }

        /* GRID FORM */
        .form-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 15px; 
            text-align: left;
        }
        .full-width { grid-column: 1 / -1; }

        .input-group { display: flex; flex-direction: column; }
        .text-label {
            font-family: 'Cinzel', serif; font-size: 13px; font-weight: 700; color: #444;
            margin-bottom: 3px; 
            margin-left: 10px; text-transform: uppercase;
        }
        .table-input {
            width: 100%; 
            padding: 8px 15px; 
            border-radius: 50px; border: 1px solid #ccc;
            background-color: var(--input-bg); font-family: 'Montserrat', sans-serif; font-size: 14px;
        }

        /* Dropdown specific styling adjustments */
        select.table-input {
            appearance: none; 
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            cursor: pointer;
        }

        .submit-btn-container {
            margin-top: 30px; display: flex; justify-content: center;
        }
        .submit-btn {
            color: white; border: none; background-color: var(--btn-add-color);
            padding: 15px 50px; border-radius: 30px; font-weight: 700; cursor: pointer;
            font-family: 'Montserrat', sans-serif; text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .submit-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* SUCCESS MODAL */
        .success-box {
            background-color: var(--success-bg); width: 450px; padding: 40px; border-radius: 15px; text-align: center; color: white; display: flex; flex-direction: column; align-items: center; gap: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .btn-done {
            background-color: #f0f0f0; color: #20252d; border: none; padding: 10px 40px; border-radius: 30px; font-weight: 700; cursor: pointer;
        }

        .hidden { display: none !important; }
    </style>
</head>

<body>

<div class="modal-overlay <?php echo $show_modal ? '' : 'hidden'; ?>" id="successModal" style="<?php echo $show_modal ? 'display:flex;' : ''; ?>">
    <div class="success-box">
        <h2 style="font-family: 'Cinzel'; margin:0; line-height:1.4;"><?php echo $success_message; ?></h2>
        <button class="btn-done" onclick="window.location.href='add_authors_titles.php'">DONE</button>
    </div>
</div>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-authors-titles.png" class="page-title-img" alt="Authors & Titles">
    <p class="instruction-text">Complete the Author information first, then proceed to Title information.</p>
</div>

<div class="selection-container">
    <button class="big-btn btn-add" onclick="openModal('authorModal')">ADD NEW INPUT</button>
    <a href="add_database.php" class="big-btn btn-return">Return to Main Menu</a>
</div>

<div class="modal-overlay hidden" id="authorModal">
    <div class="form-card">
        <span class="close-icon" onclick="closeModal('authorModal')">&times;</span>
        <div class="form-title">STEP 1: ADD NEW AUTHOR</div>

        <form id="authorForm" onsubmit="handleAuthorSubmit(event)">
            <div class="form-grid">
                <div class="input-group">
                    <label class="text-label">First Name</label>
                    <input type="text" id="au_fname" name="au_fname" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">Last Name</label>
                    <input type="text" id="au_lname" name="au_lname" class="table-input" required>
                </div>






                <div class="input-group">
                    <label class="text-label">Phone</label>
                    <input type="text" id="phone" name="phone" class="table-input" required>
                </div>
                
                <div class="input-group full-width">
                    <label class="text-label">Address</label>
                    <input type="text" id="address" name="address" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">City</label>
                    <input type="text" id="city" name="city" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">State</label>
                    <input type="text" id="state" name="state" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">Zip Code</label>
                    <input type="text" id="zip" name="zip" class="table-input" required>
                </div>
                <div class="input-group">
                    <label class="text-label">Contract</label>
                    <input type="number" id="contract" name="contract" class="table-input" required>
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

            <input type="hidden" name="au_id" id="hidden_au_id">

            <input type="hidden" name="pub_id" value="P001"> 

            <div class="form-grid">
                <div class="input-group full-width">
                    <label class="text-label">Title Name</label>
                    <input type="text" name="title" class="table-input" required>
                </div>

                <div class="input-group">
                    <label class="text-label">Type</label>
                    <select name="type" class="table-input" required>
                        <option value="" disabled selected>Select a Category...</option>
                        
                        <optgroup label="1. General Non-Fiction">
                            <option value="Arts & Recreation">Arts & Recreation</option>
                            <option value="Biographies & Memoirs">Biographies & Memoirs</option>
                            <option value="Business & Economics">Business & Economics</option>
                            <option value="History & Geography">History & Geography</option>
                            <option value="Philosophy & Psychology">Philosophy & Psychology</option>
                            <option value="Religion & Spirituality">Religion & Spirituality</option>
                            <option value="Science & Nature">Science & Nature</option>
                            <option value="Social Sciences">Social Sciences</option>
                            <option value="Technology & Applied Science">Technology & Applied Science</option>
                            <option value="True Crime">True Crime</option>
                        </optgroup>

                        <optgroup label="2. Fiction">
                            <option value="Action & Adventure">Action & Adventure</option>
                            <option value="Classics">Classics</option>
                            <option value="Contemporary Fiction">Contemporary Fiction</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="Historical Fiction">Historical Fiction</option>
                            <option value="Horror">Horror</option>
                            <option value="Literary Fiction">Literary Fiction</option>
                            <option value="Mystery & Thriller">Mystery & Thriller</option>
                            <option value="Romance">Romance</option>
                            <option value="Science Fiction">Science Fiction</option>
                        </optgroup>

                        <optgroup label="3. Visual & Alternative Formats">
                            <option value="Graphic Novels">Graphic Novels</option>
                            <option value="Manga">Manga</option>
                            <option value="Comic Books">Comic Books</option>
                            <option value="Large Print">Large Print</option>
                            <option value="Audiobooks">Audiobooks</option>
                        </optgroup>

                        <optgroup label="4. Specialized Collections">
                            <option value="Reference">Reference</option>
                            <option value="Periodicals">Periodicals</option>
                            <option value="Government Documents">Government Documents</option>
                            <option value="Special Collections/Archives">Special Collections/Archives</option>
                        </optgroup>

                        <optgroup label="5. Age-Specific Categories">
                            <option value="Children’s">Children’s (Board, Picture, Easy Readers)</option>
                            <option value="Young Adult">Young Adult (YA)</option>
                            <option value="Adult">Adult</option>
                        </optgroup>
                    </select>
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
                <div class="input-group">
                    <label class="text-label">Pub Date</label>
                    <input type="date" name="pubdate" class="table-input">
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

    // Handles Step 1 (Author) submission via AJAX
    function handleAuthorSubmit(e) {
        e.preventDefault(); 

        const data = {
            au_fname: document.getElementById('au_fname').value,
            au_lname: document.getElementById('au_lname').value,

            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            city: document.getElementById('city').value,
            state: document.getElementById('state').value,
            zip: document.getElementById('zip').value,
            contract: document.getElementById('contract').value
        };

        fetch('?ajax_add_author=1', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                closeModal('authorModal');
                document.getElementById('hidden_au_id').value = result.au_id;
                openModal('titleModal');
            } else {
                alert('Error adding author: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
    }
</script>

</body>
</html>