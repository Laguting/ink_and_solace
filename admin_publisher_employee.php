<?php
require_once __DIR__ . "/bc_view_pub_emp.php";
$publisher_search = "";
$employee_search = "";
?>

<!DOCTYPE html>

<!-- Website Design for Admin View of Publisher & Employee -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers & Employees</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
       :root {
            --dark-header: #20252d;
            --body-bg: #dbdbdb;
            --input-bg: #eeeeee;
            --btn-search: #8b8682;
            --btn-return: #3c4862;
            --modal-bg: #3c4456;
            --success-bg: #20252d;
            --delete-btn-bg: #800000;
            --header-font: 'Cinzel', serif;
            --body-font: 'Montserrat', sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; min-height: 100vh; font-family: var(--body-font); background-color: var(--body-bg); display: flex; flex-direction: column; }

        .content-wrapper { width: 100%; display: flex; flex-direction: column; height: 100%; flex: 1; transition: filter 0.3s; }
        
        /* HEADER */
        .header-section { background-color: var(--dark-header); height: 280px; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative; }
        .logo-small { position: absolute; top: 30px; left: 40px; width: 180px; }
        .page-title-img { width: 550px; max-width: 85%; height: auto; margin-top: 10px; }

        /* MAIN CONTENT & SEARCH */
        .main-content { flex: 1; display: flex; flex-direction: column; align-items: center; padding-top: 40px; }
        .search-container { width: 100%; max-width: 800px; padding: 0 20px; }
        .search-label { font-family: var(--header-font); color: #666; font-size: 22px; margin-bottom: 12px; margin-top: 30px; text-transform: uppercase; letter-spacing: 1px; display: block; }
        .input-group { position: relative; width: 100%; margin-bottom: 30px; }
        .search-input { width: 100%; padding: 18px 22px 18px 55px; border-radius: 50px; border: none; background-color: var(--input-bg); font-family: var(--body-font); font-size: 18px; color: #333; outline: none; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); }
        .search-icon { position: absolute; left: 22px; top: 50%; transform: translateY(-50%); width: 20px; opacity: 0.5; }
        
        /* MAIN BUTTONS */
        .btn-wrapper { display: flex; flex-direction: column; align-items: center; gap: 18px; margin-top: 45px; }
        .btn-main { padding: 18px 0; width: 310px; border-radius: 50px; border: none; font-weight: 600; font-size: 17px; cursor: pointer; color: white; text-align: center; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s; font-family: var(--body-font); }
        .btn-main:hover { transform: translateY(-2px); }
        .btn-search { background-color: var(--btn-search); }
        .btn-return { background-color: var(--btn-return); }

        /* MODAL & RESULTS LIST */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; justify-content: center; align-items: center; z-index: 2000; backdrop-filter: blur(8px); background-color: rgba(0, 0, 0, 0.4); }
        
        .results-box { display: flex; flex-direction: column; align-items: center; gap: 15px; max-height: 60vh; overflow-y: auto; padding: 20px; width: 100%; }
        .result-item-btn { background-color: #918a86; color: white; padding: 15px; width: 400px; border-radius: 50px; border: none; cursor: pointer; text-align: center; transition: 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .result-item-btn:hover { transform: scale(1.02); background-color: #a39e9a; }
        .res-title { font-family: var(--header-font); font-size: 18px; display: block; margin-bottom: 4px; }
        .res-sub { font-size: 14px; opacity: 0.9; }

        /* DETAIL CARD & TABLES */
        .detail-card { background-color: var(--modal-bg); width: 1400px; max-width: 95vw; max-height: 90vh; padding: 30px; border-radius: 20px; position: relative; overflow-y: auto; display: flex; flex-direction: column; gap: 30px; border: 1px solid #555; box-shadow: 0 20px 50px rgba(0,0,0,0.6); }
        .close-x { position: absolute; top: 15px; right: 15px; background: transparent; width: 35px; height: 35px; border-radius: 50%; border: 2px solid white; font-weight: bold; color: white; cursor: pointer; z-index: 10; display:flex; align-items:center; justify-content:center; font-size: 18px; transition: 0.2s; }
        .close-x:hover { background: rgba(255,255,255,0.2); }

        .table-section-title { color: white; font-family: var(--header-font); font-size: 20px; border-bottom: 1px solid #777; padding-bottom: 5px; margin-bottom: 10px; }
        .table-responsive { width: 100%; overflow-x: auto; background: white; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; font-size: 13px; color: #333; }
        th { background: #f0f0f0; font-weight: 700; text-transform: uppercase; }
        .table-input { width: 100%; padding: 5px; border: 1px solid #ccc; text-align: center; font-family: inherit; }
        .action-btns { display: flex; justify-content: center; gap: 20px; margin-top: 10px; }
        
        /* SUCCESS & DELETE ALERTS */
        .success-card, .delete-card { background-color: var(--success-bg); width: 500px; padding: 40px; border-radius: 15px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.8); position: relative; display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .success-text, .delete-text { font-family: var(--header-font); color: white; font-size: 32px; font-weight: 400; line-height: 1.2; }
        .btn-done { background-color: #eeeeee; color: #20252d; border: none; padding: 12px 60px; border-radius: 30px; font-family: var(--body-font); font-weight: 700; font-size: 14px; cursor: pointer; text-transform: uppercase; transition: transform 0.2s; }
        .btn-done:hover { transform: scale(1.05); }
        .delete-btns-wrapper { display: flex; gap: 20px; }
        .btn-yes { background-color: var(--btn-search); color: white; border: none; padding: 12px 40px; border-radius: 30px; font-family: var(--body-font); font-weight: 700; font-size: 14px; cursor: pointer; text-transform: uppercase; transition: transform 0.2s; }
        .btn-cancel { background-color: var(--delete-btn-bg); color: white; border: none; padding: 12px 40px; border-radius: 30px; font-family: var(--body-font); font-weight: 700; font-size: 14px; cursor: pointer; text-transform: uppercase; transition: transform 0.2s; }
        .btn-yes:hover, .btn-cancel:hover { transform: scale(1.05); }

        /* NO DATA ALERT */
        .no-data-card { background-color: var(--dark-header); width: 550px; padding: 60px 40px; border-radius: 15px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.8); position: relative; display: flex; flex-direction: column; align-items: center; gap: 25px; }
        .no-data-text { font-family: var(--header-font); color: white; font-size: 26px; font-weight: 400; line-height: 1.4; letter-spacing: 1px; text-transform: uppercase; }
        .btn-ok { background-color: white; color: #20252d; border: none; padding: 12px 70px; border-radius: 30px; font-family: var(--body-font); font-weight: 700; font-size: 14px; cursor: pointer; text-transform: uppercase; margin-top: 10px; transition: transform 0.2s; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .btn-ok:hover { transform: scale(1.05); background-color: #f2f2f2; }

        .hidden { display: none !important; }
    </style>
</head>
<body>

<div class="content-wrapper" id="mainWrapper">
    <div class="header-section">
        <img src="assets/text/logo.png" class="logo-small" alt="Logo">
        <img src="assets/text/title-publishers-employees.png" class="page-title-img" alt="Publishers & Employees">
    </div>

    <div class="main-content">
        <form class="search-container" method="POST">
            <label class="search-label">PUBLISHER SEARCH</label>
            <div class="input-group">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35" fill="none" stroke="#555" stroke-width="2"/></svg>
                <input type="text" class="search-input" name="publisher_search" placeholder="Search by Publisher Name" value="<?php echo htmlspecialchars($publisher_search); ?>">
            </div>

            <label class="search-label">EMPLOYEE SEARCH</label>
            <div class="input-group">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35" fill="none" stroke="#555" stroke-width="2"/></svg>
                <input type="text" class="search-input" name="employee_search" placeholder="Search by Employee Name" value="<?php echo htmlspecialchars($employee_search); ?>">
            </div>

            <div class="btn-wrapper">
                <button type="submit" class="btn-main btn-search">Search</button>
                <a href="admin_view_database.php" class="btn-main btn-return">Return to Main Menu</a>
            </div>
        </form>
    </div>
</div>

<?php if ($has_results): ?>
<div class="modal-overlay" id="resultsModal" style="display:flex;">
    <div style="display:flex; flex-direction:column; align-items:center;">
        <h2 style="color:white; font-family:'Cinzel'; font-size:32px; margin-bottom:20px;">RESULTS</h2>
        <div class="results-box">
            <?php foreach($results_list as $row): 
                $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            ?>
                <button class="result-item-btn" onclick='openDetails(<?php echo $jsonData; ?>)'>
                    <span class="res-title"><?php echo htmlspecialchars($row['pub_name'] ?? 'Unknown'); ?></span>
                    <span class="res-sub">Employee: <?php echo htmlspecialchars(($row['fname'] ?? '') . ' ' . ($row['lname'] ?? '')); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <button class="close-x" style="position:relative; margin-top:20px; right:auto;" onclick="window.location.href=window.location.pathname">✕</button>
    </div>
</div>
<?php endif; ?>

<div class="modal-overlay" id="detailModal">
    <div class="detail-card">
        <button class="close-x" onclick="closeDetails()">✕</button>

        <div id="viewMode">
            <div class="table-section-title">PUBLISHER INFORMATION</div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Pub_ID</th><th>Publisher Name</th><th>City</th><th>State</th><th>Country</th></tr></thead>
                    <tbody><tr>
                        <td id="v_pub_id"></td><td id="v_pub_name"></td><td id="v_city"></td><td id="v_state"></td><td id="v_country"></td>
                    </tr></tbody>
                </table>
            </div>

            <div class="table-section-title" style="margin-top:20px;">EMPLOYEE INFORMATION</div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Emp_ID</th><th>First Name</th><th>M.I.</th><th>Last Name</th><th>Job_ID</th><th>Job Level</th><th>Hire Date</th></tr></thead>
                    <tbody><tr>
                        <td id="v_emp_id"></td><td id="v_fname"></td><td id="v_minit"></td><td id="v_lname"></td><td id="v_job_id"></td><td id="v_job_lvl"></td><td id="v_hire_date"></td>
                    </tr></tbody>
                </table>
            </div>

            <div class="action-btns">
                <button class="btn-main btn-search" style="width:150px;" onclick="enableEdit()">EDIT</button>
                <button class="btn-main btn-return" style="width:150px; background-color:#800000;" onclick="deleteRecord()">DELETE</button>
            </div>
        </div>

        <div id="editMode" class="hidden">
            <div class="table-section-title">EDIT PUBLISHER</div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Pub_ID</th><th>Publisher Name</th><th>City</th><th>State</th><th>Country</th></tr></thead>
                    <tbody><tr>
                        <td><input id="e_pub_id" class="table-input" readonly style="background-color: #eee; cursor: not-allowed; color: #555;"></td>
                        <td><input id="e_pub_name" class="table-input"></td>
                        <td><input id="e_city" class="table-input"></td>
                        <td><input id="e_state" class="table-input"></td>
                        <td><input id="e_country" class="table-input"></td>
                    </tr></tbody>
                </table>
            </div>

            <div class="table-section-title" style="margin-top:20px;">EDIT EMPLOYEE</div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Emp_ID</th><th>First Name</th><th>M.I.</th><th>Last Name</th><th>Job_ID</th><th>Job Level</th><th>Hire Date</th></tr></thead>
                    <tbody><tr>
                        <td><input id="e_emp_id" class="table-input" readonly style="background-color: #eee; cursor: not-allowed; color: #555;"></td>
                        <td><input id="e_fname" class="table-input"></td>
                        <td><input id="e_minit" class="table-input"></td>
                        <td><input id="e_lname" class="table-input"></td>
                        <td><input id="e_job_id" class="table-input"></td>
                        <td><input id="e_job_lvl" class="table-input"></td>
                        <td><input id="e_hire_date" class="table-input"></td>
                    </tr></tbody>
                </table>
            </div>

            <div class="action-btns">
                <button class="btn-main btn-search" style="width:150px;" onclick="saveData()">SAVE</button>
                <button class="btn-main btn-return" style="width:150px; background-color:#800000;" onclick="cancelEdit()">CANCEL</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="successModal" style="z-index: 3000;">
    <div class="success-card">
        <button class="close-x" onclick="closeSuccess()">✕</button>
        <div class="success-text" id="successMessage">Entry successfully edited.</div>
        <button class="btn-done" onclick="closeSuccess()">DONE</button>
    </div>
</div>

<div class="modal-overlay hidden" id="deleteModal" style="z-index: 3000;">
    <div class="delete-card">
        <button class="close-x" onclick="closeDeleteModal()">✕</button>
        <div class="delete-text">Are you sure you want to delete this?</div>
        <div class="delete-btns-wrapper">
            <button class="btn-yes" onclick="confirmDelete()">YES</button>
            <button class="btn-cancel" onclick="closeDeleteModal()">CANCEL</button>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="noDataModal" style="z-index: 3000;">
    <div class="no-data-card">
        <button class="close-x" onclick="closeNoData()">✕</button>
        <div class="no-data-text">NO RECORDS FOUND<br>MATCHING YOUR SEARCH.</div>
        <button class="btn-ok" onclick="closeNoData()">OK</button>
    </div>
</div>

<script>
    let currentData = {};

    function openDetails(data) {
        currentData = data;
        document.getElementById('resultsModal').classList.add('hidden');
        document.getElementById('detailModal').style.display = 'flex';

        // Populate VIEW MODE - PUBLISHER
        document.getElementById('v_pub_id').innerText = data.pub_id;
        document.getElementById('v_pub_name').innerText = data.pub_name;
        document.getElementById('v_city').innerText = data.city;
        document.getElementById('v_state').innerText = data.state;
        document.getElementById('v_country').innerText = data.country;

        // Populate VIEW MODE - EMPLOYEE
        document.getElementById('v_emp_id').innerText = data.emp_id;
        document.getElementById('v_fname').innerText = data.fname;
        document.getElementById('v_minit').innerText = data.minit;
        document.getElementById('v_lname').innerText = data.lname;
        document.getElementById('v_job_id').innerText = data.job_id;
        document.getElementById('v_job_lvl').innerText = data.job_lvl;
        document.getElementById('v_hire_date').innerText = data.hire_date;
    }

    function closeDetails() {
        document.getElementById('detailModal').style.display = 'none';
        document.getElementById('resultsModal').classList.remove('hidden');
        cancelEdit();
    }

    function enableEdit() {
        document.getElementById('viewMode').classList.add('hidden');
        document.getElementById('editMode').classList.remove('hidden');

        // Populate EDIT INPUTS - PUBLISHER
        document.getElementById('e_pub_id').value = currentData.pub_id;
        document.getElementById('e_pub_name').value = currentData.pub_name;
        document.getElementById('e_city').value = currentData.city;
        document.getElementById('e_state').value = currentData.state;
        document.getElementById('e_country').value = currentData.country;

        // Populate EDIT INPUTS - EMPLOYEE
        document.getElementById('e_emp_id').value = currentData.emp_id;
        document.getElementById('e_fname').value = currentData.fname;
        document.getElementById('e_minit').value = currentData.minit;
        document.getElementById('e_lname').value = currentData.lname;
        document.getElementById('e_job_id').value = currentData.job_id;
        document.getElementById('e_job_lvl').value = currentData.job_lvl;
        document.getElementById('e_hire_date').value = currentData.hire_date;
    }

    function cancelEdit() {
        document.getElementById('editMode').classList.add('hidden');
        document.getElementById('viewMode').classList.remove('hidden');
    }

    function saveData() {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('pub_id', currentData.pub_id);
        formData.append('emp_id', currentData.emp_id);

        const fields = [
            'e_pub_name', 'e_city', 'e_state', 'e_country',
            'e_fname', 'e_minit', 'e_lname', 'e_job_id', 'e_job_lvl', 'e_hire_date'
        ];
        const keys = [
            'pub_name', 'city', 'state', 'country',
            'fname', 'minit', 'lname', 'job_id', 'job_lvl', 'hire_date'
        ];

        fields.forEach((id, index) => {
            formData.append(keys[index], document.getElementById(id).value);
        });

        fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('successMessage').innerText = "Entry successfully edited.";
                document.getElementById('successModal').classList.remove('hidden');
                document.getElementById('successModal').style.display = 'flex';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function closeSuccess() {
        window.location.href = window.location.pathname;
    }

    function deleteRecord() {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').style.display = 'none';
    }

    function confirmDelete() {
        closeDeleteModal();
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('pub_id', currentData.pub_id); 

        fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('successMessage').innerText = "Entry successfully deleted.";
                document.getElementById('successModal').classList.remove('hidden');
                document.getElementById('successModal').style.display = 'flex';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function closeNoData() {
        document.getElementById('noDataModal').classList.add('hidden');
        document.getElementById('noDataModal').style.display = 'none';
    }

    <?php if($show_no_data_modal): ?>
        document.getElementById('noDataModal').classList.remove('hidden');
        document.getElementById('noDataModal').style.display = 'flex';
    <?php endif; ?>
</script>

</body>
</html>