<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_search = "";
$employee_search = "";
$has_results = false;
$show_no_data_modal = false; 
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publisher_search = trim($_POST['publisher'] ?? "");
    $employee_search = trim($_POST['employee'] ?? "");
    
    if(!empty($publisher_search) || !empty($employee_search)){
        // Joining Publishers and Employee tables
        $sql = "SELECT p.*, e.* FROM publishers p 
                JOIN employee e ON p.pub_id = e.pub_id 
                WHERE p.pub_name LIKE ? OR (e.fname LIKE ? OR e.lname LIKE ?)";
        
        $stmt = $conn->prepare($sql);
        $term_pub = "%" . $publisher_search . "%";
        $term_emp = "%" . $employee_search . "%";
        
        if(!empty($publisher_search) && empty($employee_search)) {
             $stmt->bind_param("sss", $term_pub, $term_pub, $term_pub); 
        } elseif(empty($publisher_search) && !empty($employee_search)) {
             $stmt->bind_param("sss", $term_emp, $term_emp, $term_emp); 
        } else {
             $stmt->bind_param("sss", $term_pub, $term_emp, $term_emp); 
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $has_results = true;
            while($row = $result->fetch_assoc()) {
                $results_list[] = $row;
            }
        } else {
            $show_no_data_modal = true;
        }
        $stmt->close();
    }
}
?>
