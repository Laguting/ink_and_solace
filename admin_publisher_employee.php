<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ink_and_solace";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$publisher_input = trim($_POST['publisher'] ?? "");
$employee_input  = trim($_POST['employee'] ?? "");
$found_employees = [];
$show_results_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conditions = [];
    $params = [];
    $types = "";

    // Filter by publisher if input is not empty
    if (!empty($publisher_input)) {
        $conditions[] = "p.pub_name LIKE ?";
        $params[] = "%" . $publisher_input . "%";
        $types .= "s";
    }

    // Filter by employee if input is not empty
    if (!empty($employee_input)) {
        $conditions[] = "(e.fname LIKE ? OR e.lname LIKE ?)";
        $params[] = "%" . $employee_input . "%";
        $params[] = "%" . $employee_input . "%";
        $types .= "ss";
    }

    if (!empty($conditions)) {
        $sql = "SELECT e.emp_id, e.fname, e.lname, j.job_desc, p.pub_id, p.pub_name
                FROM employee e
                LEFT JOIN publishers p ON e.pub_id = p.pub_id
                LEFT JOIN jobs j ON e.job_id = j.job_id
                WHERE " . implode(" AND ", $conditions); // combine with AND for proper filtering

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Dynamically bind parameters
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $found_employees[] = [
                    "emp_id"    => $row['emp_id'],
                    "pub_id"    => $row['pub_id'],
                    "publisher" => $row['pub_name'] ?? "N/A",
                    "name"      => $row['fname'] . " " . $row['lname'],
                    "job"       => $row['job_desc'] ?? "N/A"
                ];
            }
            $stmt->close();
        } else {
            die("SQL Prepare Error: " . $conn->error);
        }
    }
    $show_results_modal = true;
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers & Employees | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d;
            --btn-blue: #3c4862;
            --btn-grey: #8b8682;
            --btn-red: #800000; 
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

        /* ================= TOP & BOTTOM LAYOUT ================= */
        .top-section {
            background-color: var(--dark-bg);
            min-height: 200px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
        }
        .logo-top { position: absolute; top: 30px; left: 40px; width: 120px; }
        .page-title-img { width: 520px; max-width: 80%; height: auto; margin-top: 40px; }

        .bottom-section {
            background-color: var(--light-bg);
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        /* --- SEARCH FORM --- */
        .search-form { width: 100%; max-width: 700px; display: flex; flex-direction: column; gap: 25px; margin-bottom: 30px; }
        .input-group { display: flex; flex-direction: column; }
        .input-label { font-family: 'Cinzel', serif; font-size: 24px; color: #555; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; margin-left: 15px; }
        .input-wrapper { position: relative; width: 100%; }
        .input-wrapper input { width: 100%; padding: 15px 20px 15px 50px; border-radius: 50px; border: none; background-color: var(--input-bg); font-family: 'Montserrat', sans-serif; font-size: 16px; color: #333; outline: none; }
        .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; fill: none; stroke: #555; stroke-width: 2; }
        
        .btn-container { display: flex; flex-direction: column; align-items: center; gap: 20px; margin-top: 20px; }
        .btn { padding: 15px 0; width: 250px; border-radius: 50px; border: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; cursor: pointer; text-align: center; text-decoration: none; transition: transform 0.2s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .btn:hover { transform: translateY(-2px); }
        .btn-confirm { background-color: var(--btn-grey); color: white; }
        .btn-return { background-color: var(--btn-blue); color: white; }

        /* ================= RESULTS MODAL (LEVEL 1) ================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; backdrop-filter: blur(5px); animation: fadeIn 0.3s ease-out;
        }
        .modal-content { display: flex; flex-direction: column; align-items: center; max-width: 90%; max-height: 90vh; position: relative; }
        .results-heading { color: white; font-family: 'Cinzel', serif; font-size: 36px; letter-spacing: 2px; margin-bottom: 20px; font-weight: 400; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .titles-scroll-container { display: flex; flex-direction: column; align-items: center; gap: 15px; overflow-y: auto; max-height: 60vh; padding: 10px 20px; width: 100%; }
        
        .result-pill-btn {
            background-color: #918a86; color: white; padding: 25px 40px; border-radius: 60px; border: none; cursor: pointer;
            width: 100%; min-width: 350px; max-width: 550px; text-align: center; transition: transform 0.2s, background-color 0.2s;
            display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .result-pill-btn:hover { transform: scale(1.02); background-color: #a39e9a; }
        .pill-publisher { font-family: 'Cinzel', serif; font-size: 24px; text-transform: uppercase; margin-bottom: 5px; line-height: 1.2; }
        .pill-employee { font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 300; opacity: 0.9; }

        .close-btn { margin-top: 30px; background: transparent; border: 2px solid white; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .close-btn:hover { background: white; color: var(--dark-bg); }

        /* ================= DETAIL CARD (LEVEL 2) ================= */
        .detail-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4); justify-content: center; align-items: center;
            z-index: 2000; animation: fadeIn 0.3s ease-out;
        }

        .detail-card {
            background-color: #3c4456; 
            width: 500px; max-width: 90%; padding: 40px 30px; border-radius: 15px;
            text-align: center; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid #5a647d;
        }

        .close-detail-x {
            position: absolute; top: 15px; right: 15px; width: 30px; height: 30px;
            background: #e0e0e0; color: #333; border-radius: 50%; font-size: 18px; font-weight: bold;
            display: flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.2s;
        }
        .close-detail-x:hover { background: #fff; }

        /* View Mode Text */
        .card-publisher { font-family: 'Cinzel', serif; color: white; font-size: 30px; margin: 0; margin-bottom: 5px; text-transform: uppercase; }
        .card-subtext { font-family: 'Montserrat', sans-serif; font-weight: 600; color: white; font-size: 14px; margin-bottom: 30px; }
        .card-employee-name { font-family: 'Montserrat', sans-serif; font-style: italic; color: white; font-size: 22px; margin-bottom: 40px; font-weight: 300; }

        /* Edit Mode Inputs */
        .edit-box-border { border: 2px solid white; padding: 20px; margin-bottom: 30px; }
        .edit-input-pub, .edit-input-name, .edit-input-subtext {
            background: transparent; border: none; color: white; width: 100%; text-align: center; outline: none; border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        .edit-input-pub { font-family: 'Cinzel', serif; font-size: 30px; text-transform: uppercase; margin-bottom: 10px; }
        .edit-input-subtext { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; margin-bottom: 30px; }
        .edit-input-name { font-family: 'Montserrat', sans-serif; font-size: 22px; font-style: italic; margin-top: 10px; }

        .card-actions { display: flex; justify-content: center; gap: 20px; }
        .action-btn { border: none; padding: 12px 0; width: 140px; border-radius: 30px; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer; text-transform: uppercase; color: white; }
        .btn-edit-card { background-color: var(--btn-grey); }
        .btn-delete-card { background-color: var(--btn-red); }

        /* ================= SUCCESS & DELETE MODALS (LEVEL 3 & 4) ================= */
        .success-overlay, .delete-overlay {
            display: none; 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            justify-content: center; align-items: center;
            animation: fadeIn 0.3s ease-out;
        }

        .success-overlay { z-index: 3000; }
        .delete-overlay { z-index: 4000; }
        
        .success-box, .delete-box {
            background-color: #20252d; 
            width: 450px; max-width: 90%;
            padding: 50px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.7);
            border: 1px solid #444;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 30px;
        }

        .success-text, .delete-text {
            color: white; font-family: 'Cinzel', serif; font-size: 28px; font-weight: 400; margin: 0; line-height: 1.3;
        }

        .btn-done {
            background-color: #f0f0f0; color: #20252d; border: none; padding: 12px 60px; border-radius: 30px;
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-transform: uppercase; transition: transform 0.2s;
        }
        .btn-done:hover { transform: scale(1.05); background-color: white; }

        .delete-btn-container { display: flex; gap: 20px; }
        .btn-yes {
            background-color: var(--btn-grey); color: white; border: none; padding: 12px 40px; border-radius: 30px;
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-transform: uppercase;
        }
        .btn-cancel {
            background-color: var(--btn-red); color: white; border: none; padding: 12px 40px; border-radius: 30px;
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; text-transform: uppercase;
        }
        .btn-yes:hover, .btn-cancel:hover { filter: brightness(1.2); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        @media (max-width: 768px) {
            .detail-card { width: 90%; padding: 30px 20px; }
            .card-actions { flex-direction: column; width: 100%; }
            .action-btn { width: 100%; }
        }
    </style>
</head>

<body>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-publishers-employees.png" class="page-title-img" alt="Publishers & Employees">
</div>

<div class="bottom-section">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
        <div class="input-group">
            <label class="input-label">Publisher</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="publishers" placeholder="SEARCH" value="<?php echo htmlspecialchars($publisher_input); ?>">
            </div>
        </div>
        <div class="input-group">
            <label class="input-label">Employee</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="employee" placeholder="SEARCH" value="<?php echo htmlspecialchars($employee_input); ?>">
            </div>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-confirm">Confirm</button>
            <a href="admin_view_database.php" class="btn btn-return">Return to Main Menu</a>
        </div>
    </form>
</div>

<?php if ($show_results_modal): ?>
    <div class="modal-overlay" id="resultsModal">
        <div class="modal-content">
            <h2 class="results-heading">RESULTS:</h2>
            <div class="titles-scroll-container">
                <?php foreach($found_employees as $emp): 
                    // Extract Variables from the SQL Result Array
                    $name = $emp['name'];
                    $job = $emp['job'];
                    $publisher_name = $emp['publisher']; 
                ?>
                    <button class="result-pill-btn" type="button" 
                        onclick="openDetailCard('<?php echo addslashes($publisher_name); ?>', '<?php echo addslashes($name); ?>', '<?php echo addslashes($job); ?>')">
                        <span class="pill-publisher"><?php echo htmlspecialchars($publisher_name); ?></span>
                        <span class="pill-employee"><?php echo htmlspecialchars($name); ?></span>
                    </button>
                <?php endforeach; ?>
                
                <?php if (count($found_employees) == 0): ?>
                    <p style="color:white; font-family:'Montserrat', sans-serif;">No matching results found.</p>
                <?php endif; ?>
            </div>
            <button class="close-btn" onclick="document.getElementById('resultsModal').style.display='none'">
                &times;
            </button>
        </div>
    </div>
<?php endif; ?>

<div class="detail-overlay" id="detailModal">
    <div class="detail-card">
        <div class="close-detail-x" onclick="closeDetailCard()">✕</div>
        
        <div id="viewModeWrapper">
            <h2 class="card-publisher" id="detailPubName">PUBLISHER</h2>
            <p class="card-subtext" id="detailJobTitle">Job Title</p> 
            <p class="card-employee-name" id="detailEmpName">Employee Name</p>
            
            <div class="card-actions">
                <button class="action-btn btn-edit-card" onclick="enableEditMode()">EDIT</button>
                <button class="action-btn btn-delete-card" onclick="askDeleteConfirmation()">DELETE</button>
            </div>
        </div>

        <div id="editModeWrapper" style="display: none;">
            <div class="edit-box-border">
                <input type="text" id="editPubInput" class="edit-input-pub">
                <input type="text" id="editJobInput" class="edit-input-subtext">
                <input type="text" id="editNameInput" class="edit-input-name">
            </div>
            
            <div class="card-actions">
                <button class="action-btn btn-edit-card" onclick="saveChanges()">SAVE</button>
                <button class="action-btn btn-delete-card" onclick="cancelEditMode()">CANCEL</button>
            </div>
        </div>
    </div>
</div>

<div class="delete-overlay" id="deleteConfirmModal">
    <div class="delete-box">
        <h2 class="delete-text">Are you sure you want to delete this?</h2>
        <div class="delete-btn-container">
            <button class="btn-yes" onclick="confirmDelete()">YES</button>
            <button class="btn-cancel" onclick="closeDeleteConfirmation()">CANCEL</button>
        </div>
    </div>
</div>

<div class="success-overlay" id="successModal">
    <div class="success-box">
        <h2 class="success-text" id="successMessageText">Entry successfully edited.</h2>
        <button class="btn-done" onclick="closeSuccessModal()">DONE</button>
    </div>
</div>

<script>
    // 1. OPEN MODAL
    function openDetailCard(publisher, empName, jobTitle) {
        cancelEditMode(); 
        
        document.getElementById('detailPubName').innerText = publisher;
        document.getElementById('detailEmpName').innerText = empName;
        document.getElementById('detailJobTitle').innerText = jobTitle; 
        
        document.getElementById('detailModal').style.display = 'flex';
    }

    // 2. CLOSE MODAL
    function closeDetailCard() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // 3. ENABLE EDIT MODE
    function enableEditMode() {
        var currentPub = document.getElementById('detailPubName').innerText;
        var currentJob = document.getElementById('detailJobTitle').innerText;
        var currentName = document.getElementById('detailEmpName').innerText;

        document.getElementById('editPubInput').value = currentPub;
        document.getElementById('editJobInput').value = currentJob;
        document.getElementById('editNameInput').value = currentName;

        document.getElementById('viewModeWrapper').style.display = 'none';
        document.getElementById('editModeWrapper').style.display = 'block';
    }

    // 4. CANCEL EDIT MODE
    function cancelEditMode() {
        document.getElementById('editModeWrapper').style.display = 'none';
        document.getElementById('viewModeWrapper').style.display = 'block';
    }

    // 5. SAVE CHANGES (UI Simulation Only)
    function saveChanges() {
        document.getElementById('detailPubName').innerText = document.getElementById('editPubInput').value;
        document.getElementById('detailJobTitle').innerText = document.getElementById('editJobInput').value;
        document.getElementById('detailEmpName').innerText = document.getElementById('editNameInput').value;

        cancelEditMode();
        
        // Note: To make this persist in the database, you would need an AJAX call here
        document.getElementById('successMessageText').innerText = "Entry successfully edited.";
        document.getElementById('successModal').style.display = 'flex';
    }

    // 6. DELETE LOGIC (UI Simulation Only)
    function askDeleteConfirmation() {
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }

    function closeDeleteConfirmation() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    function confirmDelete() {
        closeDeleteConfirmation();
        closeDetailCard();
        // Note: To make this persist in the database, you would need an AJAX call here
        document.getElementById('successMessageText').innerText = "Entry successfully deleted.";
        document.getElementById('successModal').style.display = 'flex';
    }

    // 7. CLOSE SUCCESS MODAL
    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }
</script>

</body>
</html>