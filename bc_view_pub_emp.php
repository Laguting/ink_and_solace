<?php
require_once __DIR__ . "/bc_db_connect.php";

// Initialize variables to prevent "Undefined Variable" errors in the HTML
$has_results = false;
$show_no_data_modal = false;
$results_list = [];
$publisher_search = "";
$employee_search = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Capture and trim search inputs
    // Note: These names match the corrected 'name' attributes in the HTML form
    $publisher_search = trim($_POST['publisher_search'] ?? "");
    $employee_search = trim($_POST['employee_search'] ?? "");

    // 2. Only proceed if at least one search field is filled
    if (!empty($publisher_search) || !empty($employee_search)) {
        
        /**
         * SQL EXPLANATION:
         * We use LEFT JOIN so that Publishers appear even if they have no 
         * matching records in the Employees table. 
         * We search pub_name, fname, and lname using LIKE.
         */
        $sql = "SELECT 
                    p.pub_id, p.pub_name, p.city, p.state, p.country,
                    e.emp_id, e.fname, e.minit, e.lname, e.job_id, e.job_lvl, e.hire_date
                FROM publishers p 
                LEFT JOIN employee e ON p.pub_id = e.pub_id 
                WHERE p.pub_name LIKE ? 
                   OR e.fname LIKE ? 
                   OR e.lname LIKE ?";
        
        $stmt = $conn->prepare($sql);
        
        // Prepare search terms with wildcards
        $term_p = "%" . $publisher_search . "%";
        $term_e = "%" . $employee_search . "%";
        
        // Bind parameters: 
        // 1st ? = Publisher Name
        // 2nd ? = Employee First Name
        // 3rd ? = Employee Last Name
        $stmt->bind_param("sss", $term_p, $term_e, $term_e); 
        
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            
            if ($res->num_rows > 0) {
                $has_results = true;
                while ($row = $res->fetch_assoc()) {
                    $results_list[] = $row;
                }
            } else {
                $show_no_data_modal = true;
            }
        } else {
            // Log database errors for debugging
            error_log("Database Error: " . $conn->error);
        }
        $stmt->close();
    }
}
?>