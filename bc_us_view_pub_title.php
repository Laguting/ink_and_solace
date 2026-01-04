<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_search = "";
$title_search = "";
$has_results = false; 
$results_list = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publisher_search = trim($_POST['publisher'] ?? "");
    $title_search     = trim($_POST['title'] ?? "");
    
    // Only search if at least one field has text
    if(!empty($publisher_search) || !empty($title_search)) {
        
        // ==========================================================
        // SQL QUERY
        // Matches if Publisher Name OR Book Title contains the input
        // ==========================================================
        // Assumed Table: library_books
        // Assumed Columns: publisher_name, book_title, author_name, book_info (e.g. '250,000 copies')
        $sql = "SELECT * FROM library_books WHERE publisher_name LIKE ? OR book_title LIKE ?";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Add wildcards for partial matches (e.g. "Harp" finds "HarperCollins")
            $pub_param = "%" . $publisher_search . "%";
            $title_param = "%" . $title_search . "%";
            
            // Logic to prevent empty inputs from matching everything unexpectedly
            if(empty($publisher_search)) $pub_param = "NO_MATCH_XYZ";
            if(empty($title_search)) $title_param = "NO_MATCH_XYZ";

            $stmt->bind_param("ss", $pub_param, $title_param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $has_results = true;
                
                // Fetch data
                while($row = $result->fetch_assoc()) {
                    $results_list[] = [
                        'publisher' => $row['publisher_name'],
                        'title'     => $row['book_title'],
                        'author'    => $row['author_name'], // Added Author
                        'info'      => $row['book_info']    // Added Info/Stats
                    ];
                }
            }
            $stmt->close();
        }
    }
}

?>