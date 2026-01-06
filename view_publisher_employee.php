<?php
require_once __DIR__ . "/bc_us_view_pub_emp.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers & Employees | Ink & Solace</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d; 
            --modal-card-bg: #3c4456; 
            --input-bg: #f0f0f0;
            --btn-confirm: #8b8682;
            --btn-return: #3c4862;
            --header-font: 'Cinzel', serif;
            --body-font: 'Montserrat', sans-serif;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100vh; font-family: var(--body-font); background-color: var(--light-bg); display: flex; flex-direction: column; }
        
        /* HEADER */
        .top-section { background-color: var(--dark-bg); min-height: 250px; position: relative; display: flex; justify-content: center; align-items: center; }
        .logo-top { position: absolute; top: 20px; left: 30px; width: 150px; }
        .page-title-img { width: 500px; max-width: 80%; height: auto; margin-top: 40px; }
        
        /* MAIN FORM SECTION */
        .bottom-section { background-color: var(--light-bg); flex: 1; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        
        .search-form { width: 100%; max-width: 800px; display: flex; flex-direction: column; } 
        
        .input-group { margin-bottom: 30px; } 

        .input-label { 
            font-family: var(--header-font); 
            font-size: 22px; 
            color: #666; 
            margin-bottom: 12px; margin-left: 15px; letter-spacing: 1px; text-transform: uppercase;
        }

        .input-wrapper { position: relative; width: 100%; }
        
        .input-wrapper input { 
            width: 100%; 
            padding: 18px 22px 18px 55px; 
            border-radius: 50px; border: none; 
            background-color: var(--input-bg); font-family: var(--body-font); 
            font-size: 18px; 
            outline: none; color: #333;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
        }
        .search-icon { position: absolute; left: 22px; top: 50%; transform: translateY(-50%); width: 20px; stroke: #555; fill: none; stroke-width: 2; } 
        
        /* BUTTON WRAPPER */
        .btn-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;       
            margin-top: 45px; 
        }

        .btn { 
            padding: 18px 0; 
            width: 310px;    
            border-radius: 50px; border: none; 
            font-weight: 600; 
            font-size: 17px; 
            cursor: pointer; text-align: center; text-decoration: none; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-family: var(--body-font); 
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-confirm { background-color: var(--btn-confirm); color: white; }
        .btn-return { background-color: var(--btn-return); color: white; }

        /* MODAL OVERLAY */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); 
            display: flex; justify-content: center; align-items: center; z-index: 1000; 
        }
        
        /* RESULTS LIST STYLES */
        .results-container { display: flex; flex-direction: column; align-items: center; }
        .modal-header { font-family: var(--header-font); color: white; font-size: 38px; margin-bottom: 25px; text-shadow: 0 2px 4px rgba(0,0,0,0.5); letter-spacing: 2px; }
        .results-scroll-container { max-height: 50vh; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; padding-right: 10px; }
        .result-pill { background-color: #918a86; color: white; padding: 15px 30px; width: 380px; border-radius: 50px; border: none; cursor: pointer; text-align: center; transition: 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.3); }
        .result-pill:hover { transform: scale(1.02); background-color: #a39c98; }
        .res-main { font-family: var(--header-font); font-size: 16px; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .res-sub { font-family: var(--body-font); font-size: 13px; font-weight: 300; }

        /* NO DATA MODAL */
        .no-data-card {
            background-color: var(--dark-bg); 
            width: 550px; padding: 60px 40px; border-radius: 15px;
            text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.8);
            position: relative; display: flex; flex-direction: column; align-items: center; gap: 25px;
        }
        .no-data-text { 
            font-family: var(--header-font); color: white; font-size: 26px; 
            font-weight: 400; line-height: 1.4; letter-spacing: 1px;
            text-transform: uppercase;
        }
        .btn-ok { 
            background-color: white; color: #20252d; border: none; 
            padding: 12px 70px; border-radius: 30px; 
            font-family: var(--body-font); font-weight: 700; font-size: 14px; 
            cursor: pointer; text-transform: uppercase; margin-top: 10px;
            transition: transform 0.2s; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .btn-ok:hover { transform: scale(1.05); background-color: #f2f2f2; }

        /* DETAILED VIEW MODAL STYLES */
        .detail-card { background-color: var(--modal-card-bg); width: 1200px; max-width: 95vw; max-height: 90vh; padding: 30px 40px; border-radius: 10px; position: relative; overflow-y: auto; border: 1px solid #555; box-shadow: 0 20px 60px rgba(0,0,0,0.6); }
        .close-x { position: absolute; top: 15px; right: 15px; background: transparent; border: 2px solid white; color: white; border-radius: 50%; width: 35px; height: 35px; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-family: var(--body-font); }
        .close-x:hover { background: white; color: var(--modal-card-bg); }
        .section-header { font-family: var(--header-font); color: white; font-size: 22px; margin-bottom: 15px; margin-top: 25px; border-bottom: 1px solid #777; padding-bottom: 8px; letter-spacing: 1px; }
        .section-header:first-of-type { margin-top: 0; }
        .table-wrapper { width: 100%; overflow-x: auto; margin-bottom: 20px; background: white; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { padding: 12px 15px; text-align: center; font-size: 14px; border: 1px solid #ccc; color: #333; font-family: var(--body-font); }
        th { background-color: #f0f0f0; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        td { font-weight: 400; }
        .btn-back-center { display: block; margin: 30px auto 0 auto; background-color: #666; color: white; padding: 12px 50px; border-radius: 30px; border: none; font-family: var(--body-font); font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .btn-back-center:hover { background-color: #888; }
        
        .hidden { display: none !important; }
    </style>
</head>

<body>

<div class="top-section">
    <img src="assets/text/logo.png" class="logo-top" alt="Logo">
    <img src="assets/text/title-publishers-employees.png" class="page-title-img" alt="Publishers & Employees">
</div>

<div class="bottom-section">
    <form method="POST" class="search-form">
        <div class="input-group">
            <label class="input-label">Publisher Search</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="publisher" placeholder="Search by Publisher Name" value="<?php echo htmlspecialchars($publisher_search); ?>">
            </div>
        </div>
        <div class="input-group">
            <label class="input-label">Employee Search</label>
            <div class="input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24"><path d="M11 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zM21 21l-4.35-4.35"></path></svg>
                <input type="text" name="employee" placeholder="Search by Employee Name" value="<?php echo htmlspecialchars($employee_search); ?>">
            </div>
        </div>
        
        <div class="btn-wrapper">
            <button type="submit" class="btn btn-confirm">Search</button>
            <a href="admin_view_database.php" class="btn btn-return">Return to Main Menu</a>
        </div>
    </form>
</div>

<?php if ($has_results): ?>
    <div class="modal-overlay" id="listModal">
        <div class="results-container">
            <div class="modal-header">RESULTS</div>
            <div class="results-scroll-container">
                <?php foreach($results_list as $row): 
                    $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                ?>
                    <button class="result-pill" type="button" onclick='openDetails(<?php echo $jsonData; ?>)'>
                        <span class="res-main"><?php echo htmlspecialchars($row['pub_name'] ?? 'Untitled'); ?></span>
                        <span class="res-sub">Emp: <?php echo htmlspecialchars(($row['fname'] ?? '') . " " . ($row['lname'] ?? '')); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <button class="close-x" style="position:relative; margin-top:20px; right:auto;" onclick="location.href='view_publisher_employee.php'">✕</button>
        </div>
    </div>

    <div class="modal-overlay hidden" id="detailModal">
        <div class="detail-card">
            <button class="close-x" onclick="closeDetails()">✕</button>
            
            <div class="section-header">PUBLISHER INFORMATION</div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Pub_ID</th><th>Publisher Name</th><th>City</th><th>State</th><th>Country</th></tr></thead>
                    <tbody><tr>
                        <td id="v_pub_id"></td><td id="v_pub_name"></td><td id="v_city"></td><td id="v_state"></td><td id="v_country"></td>
                    </tr></tbody>
                </table>
            </div>

            <div class="section-header">EMPLOYEE INFORMATION</div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Emp_ID</th><th>First Name</th><th>M.I.</th><th>Last Name</th><th>Job_ID</th><th>Job Level</th><th>Hire Date</th></tr></thead>
                    <tbody><tr>
                        <td id="v_emp_id"></td><td id="v_fname"></td><td id="v_minit"></td><td id="v_lname"></td><td id="v_job_id"></td><td id="v_job_lvl"></td><td id="v_hire_date"></td>
                    </tr></tbody>
                </table>
            </div>

            <button class="btn-back-center" onclick="closeDetails()">BACK</button>
        </div>
    </div>

    <script>
        function openDetails(data) {
            document.getElementById('listModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('hidden');

            const map = {
                'v_pub_id': data.pub_id, 'v_pub_name': data.pub_name, 'v_city': data.city, 
                'v_state': data.state, 'v_country': data.country,
                'v_emp_id': data.emp_id, 'v_fname': data.fname, 'v_minit': data.minit, 
                'v_lname': data.lname, 'v_job_id': data.job_id,
                'v_job_lvl': data.job_lvl, 'v_hire_date': data.hire_date
            };
            for(let id in map) document.getElementById(id).innerText = map[id] || '';
        }

        function closeDetails() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('listModal').classList.remove('hidden');
        }
    </script>
<?php endif; ?>

<?php if ($show_no_data_modal): ?>
<div class="modal-overlay">
    <div class="no-data-card">
        <button class="close-x" onclick="location.href='view_publisher_employee.php'">✕</button>
        
        <div class="no-data-text">
            NO RECORDS FOUND<br>
            MATCHING YOUR SEARCH.
        </div>
        
        <button class="btn-ok" onclick="location.href='view_publisher_employee.php'">OK</button>
    </div>
</div>
<?php endif; ?>

</body>
</html>