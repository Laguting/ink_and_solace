<?php
require_once __DIR__ . "/bc_db_connect.php";

$publisher_input = "";
$title_input = "";
$show_results_modal = false;

$found_publisher = "";
$found_titles = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $publisher_input = trim($_POST['publisher'] ?? "");
    $title_input     = trim($_POST['title'] ?? "");

    if ($publisher_input !== "" || $title_input !== "") {

        // ==========================================================
        // SEARCH PUBLISHER NAME OR TITLE
        // ==========================================================
        $sql = "
            SELECT 
                p.pub_name,
                t.title,
                t.pubdate
            FROM titles t
            LEFT JOIN publishers p ON p.pub_id = t.pub_id
            WHERE p.pub_name LIKE ? OR t.title LIKE ?
        ";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            // Prevent empty fields from matching everything
            $pub_param   = $publisher_input !== "" ? "%$publisher_input%" : "__NO_MATCH__";
            $title_param = $title_input     !== "" ? "%$title_input%"     : "__NO_MATCH__";

            $stmt->bind_param("ss", $pub_param, $title_param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $show_results_modal = true;

                while ($row = $result->fetch_assoc()) {

                    $found_titles[] = [
                        "title" => $row['title'],
                        "info"  => $row['pubdate'],   // You may replace this with notes/price/etc
                        "pub"   => $row['pub_name']
                    ];

                    if ($found_publisher === "" && $row['pub_name']) {
                        $found_publisher = $row['pub_name'];
                    }
                }
            }

            $stmt->close();
        }
    }
}

?>
