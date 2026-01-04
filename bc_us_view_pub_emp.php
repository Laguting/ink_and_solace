<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_search = "";
$employee_search  = "";
$has_results      = false;
$results_list     = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publisher_search = trim($_POST['publisher'] ?? "");
    $employee_search  = trim($_POST['employee'] ?? "");

    // Only run query if at least one field has text
    if(!empty($publisher_search) || !empty($employee_search)){

        // ==========================================================
        // SQL QUERY - join employees and publishers
        // ==========================================================
        $sql = "SELECT e.emp_id,
                       CONCAT(e.fname, ' ', e.minit, ' ', e.lname) AS employee_name,
                       e.job_id,
                       e.job_lvl,
                       p.pub_name AS publisher_name,
                       e.hire_date
                FROM employee e
                LEFT JOIN publishers p ON e.pub_id = p.pub_id
                WHERE e.fname LIKE ? 
                   OR e.lname LIKE ? 
                   OR p.pub_name LIKE ?
                ORDER BY e.lname, e.fname";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $emp_param    = "%" . ($employee_search ?: "NO_MATCH_XYZ") . "%";
            $emp_param2   = $emp_param; // For last name
            $pub_param    = "%" . ($publisher_search ?: "NO_MATCH_XYZ") . "%";

            $stmt->bind_param("sss", $emp_param, $emp_param2, $pub_param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $has_results = true;
                while($row = $result->fetch_assoc()) {
                    $results_list[] = [
                        'publisher' => $row['publisher_name'] ?? "Unknown",
                        'name'      => $row['employee_name'],
                        'job_id'    => $row['job_id'],
                        'job_lvl'   => $row['job_lvl'],
                        'hire_date' => $row['hire_date']
                    ];
                }
            }
            $stmt->close();
        }
    }
}

?>