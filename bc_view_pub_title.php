<?php
require_once __DIR__ . "/bc_db_connect.php";

/* ==========================================================
   DEFAULT VARIABLES
========================================================== */
$pub_search   = "";
$title_search = "";
$has_results  = false;
$no_results   = false;
$results_list = [];

/* ==========================================================
   HELPER: GET NEXT SEQUENTIAL ID
========================================================== */
function getNextId($conn, $table, $column, $prefix = "") {
    if ($prefix !== "") {
        $sql = "SELECT MAX(CAST(SUBSTRING($column, 2) AS UNSIGNED)) AS max_id FROM $table";
    } else {
        $sql = "SELECT MAX($column) AS max_id FROM $table";
    }

    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $next = ($row['max_id'] ?? 0) + 1;

    return $prefix
        ? $prefix . str_pad($next, 8, "0", STR_PAD_LEFT)
        : $next;
}

/* ==========================================================
   HANDLE POST REQUESTS
========================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* ======================================================
       ADD / FIND PUBLISHER + ADD TITLE (NO DUPLICATES)
    ====================================================== */
    if (isset($_POST['action']) && $_POST['action'] === "add") {

        $conn->begin_transaction();

        try {
            /* ---------- BASIC VALIDATION ---------- */
            if (empty($_POST['pub_name']) || empty($_POST['title'])) {
                throw new Exception("Publisher and Title are required.");
            }

            /* ---------- FIND OR CREATE PUBLISHER ---------- */
            $stmt = $conn->prepare(
                "SELECT pub_id FROM publishers WHERE pub_name = ?"
            );
            $stmt->bind_param("s", $_POST['pub_name']);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $pub_id = $row['pub_id'];
            } else {
                $pub_id = getNextId($conn, "publishers", "pub_id");

                $ins_pub = $conn->prepare(
                    "INSERT INTO publishers (pub_id, pub_name, city, state, country)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $ins_pub->bind_param(
                    "issss",
                    $pub_id,
                    $_POST['pub_name'],
                    $_POST['city'],
                    $_POST['state'],
                    $_POST['country']
                );
                $ins_pub->execute();
                $ins_pub->close();
            }
            $stmt->close();

            /* ---------- BLOCK NULL / INVALID PUB ---------- */
            if (empty($pub_id)) {
                throw new Exception("Invalid publisher.");
            }

            /* ---------- CHECK DUPLICATE TITLE (CASE-INSENSITIVE) ---------- */
            $chk_title = $conn->prepare(
                "SELECT 1 FROM titles
                 WHERE LOWER(title) = LOWER(?)
                 AND pub_id = ?"
            );
            $chk_title->bind_param("ss", $_POST['title'], $pub_id);
            $chk_title->execute();
            $chk_title->store_result();

            if ($chk_title->num_rows > 0) {
                throw new Exception("Title already exists for this publisher.");
            }
            $chk_title->close();

            /* ---------- INSERT TITLE ---------- */
            $title_id = getNextId($conn, "titles", "title_id", "T");

            $ins_title = $conn->prepare(
                "INSERT INTO titles
                (title_id, title, type, pub_id, price, advance, royalty, ytd_sales, notes, pubdate)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $ins_title->bind_param(
                "ssssddiiis",
                $title_id,
                $_POST['title'],
                $_POST['type'],
                $pub_id,
                $_POST['price'],
                $_POST['advance'],
                $_POST['royalty'],
                $_POST['ytd_sales'],
                $_POST['notes'],
                $_POST['pubdate']
            );

            $ins_title->execute();
            $ins_title->close();

            $conn->commit();

            echo json_encode([
                "status"  => "success",
                "message" => "Publisher and Title successfully added."
            ]);
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                "status"  => "error",
                "message" => $e->getMessage()
            ]);
            exit;
        }
    }

    /* ======================================================
       UPDATE
    ====================================================== */
    if (isset($_POST['action']) && $_POST['action'] === "update") {

        $stmt1 = $conn->prepare(
            "UPDATE publishers
             SET pub_name=?, city=?, state=?, country=?
             WHERE pub_id=?"
        );
        $stmt1->bind_param(
            "ssssi",
            $_POST['pub_name'],
            $_POST['city'],
            $_POST['state'],
            $_POST['country'],
            $_POST['pub_id']
        );
        $stmt1->execute();
        $stmt1->close();

        $stmt2 = $conn->prepare(
            "UPDATE titles
             SET title=?, type=?, price=?, advance=?, royalty=?, ytd_sales=?, notes=?, pubdate=?
             WHERE title_id=?"
        );
        $stmt2->bind_param(
            "ssddiiiss",
            $_POST['title'],
            $_POST['type'],
            $_POST['price'],
            $_POST['advance'],
            $_POST['royalty'],
            $_POST['ytd_sales'],
            $_POST['notes'],
            $_POST['pubdate'],
            $_POST['title_id']
        );
        $stmt2->execute();
        $stmt2->close();

        echo json_encode([
            "status" => "success",
            "message" => "ENTRY SUCCESSFULLY EDITED."
        ]);
        exit;
    }

    /* ======================================================
       DELETE
    ====================================================== */
    if (isset($_POST['action']) && $_POST['action'] === "delete") {

        $stmt = $conn->prepare(
            "DELETE FROM titles WHERE title_id = ?"
        );
        $stmt->bind_param("s", $_POST['title_id']);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "ENTRY SUCCESSFULLY DELETED."
        ]);
        exit;
    }

    /* ======================================================
       SEARCH
    ====================================================== */
    if (isset($_POST['pub_search']) || isset($_POST['title_search'])) {

        $pub_search   = trim($_POST['pub_search'] ?? "");
        $title_search = trim($_POST['title_search'] ?? "");

        $sql = "SELECT t.*, p.pub_name, p.city, p.state, p.country
                FROM titles t
                LEFT JOIN publishers p ON t.pub_id = p.pub_id
                WHERE 1=1";

        $params = [];
        $types  = "";

        if ($pub_search !== "") {
            $sql .= " AND p.pub_name LIKE ?";
            $params[] = "%$pub_search%";
            $types .= "s";
        }

        if ($title_search !== "") {
            $sql .= " AND t.title LIKE ?";
            $params[] = "%$title_search%";
            $types .= "s";
        }

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $has_results = true;
            while ($row = $res->fetch_assoc()) {
                $results_list[] = $row;
            }
        } else {
            $no_results = true;
        }

        $stmt->close();
    }
}
?>
