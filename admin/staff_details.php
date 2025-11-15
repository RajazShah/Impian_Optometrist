<?php
include "../db_connect.php";

$search_query = isset($_GET["search_query"]) ? $_GET["search_query"] : "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");

if (isset($_GET["action"]) && isset($_GET["nric"])) {
    $nric = $_GET["nric"];
    $action = $_GET["action"];
    $new_status = "";

    if ($action == "resign") {
        $new_status = "Resigned";
    } elseif ($action == "rehire") {
        $new_status = "Active";
    }

    if (!empty($new_status)) {
        $stmt_update = $conn->prepare(
            "UPDATE staff SET STAFF_STATUS = ? WHERE STAFF_NRIC = ?",
        );
        $stmt_update->bind_param("ss", $new_status, $nric);
        $stmt_update->execute();
        $stmt_update->close();
    }

    $redirect_url = $current_page_url;
    if (!empty($search_query)) {
        $redirect_url .= "?search_query=" . urlencode($search_query);
    }
    header("Location: " . $redirect_url);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="staff_details.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Staff Details</h2>

        <div class="search-container">
            <form method="GET" action="<?php echo $current_page_url; ?>">
                <label for="search_query">Search Staff (NRIC/Name):</label>
                <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                    $search_query,
                ); ?>" placeholder="Enter NRIC, first, or last name...">
                <button type="submit">Search</button>
                <a href="<?php echo $current_page_url; ?>">Clear</a>
            </form>
        </div>

        <table class="appointments-table">
            <tr>
                <th>NRIC</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Hire Date</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            $sql = "SELECT * FROM staff";
            $params = [];
            $types = "";

            if (!empty($search_query)) {
                $sql .=
                    " WHERE (STAFF_NRIC LIKE ? OR STAFF_FNAME LIKE ? OR STAFF_LNAME LIKE ?)";
                $search_term = "%" . $search_query . "%";
                $params = [$search_term, $search_term, $search_term];
                $types = "sss";
            }

            $stmt_select = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt_select->bind_param($types, ...$params);
            }

            $stmt_select->execute();
            $result = $stmt_select->get_result();

            $search_param_url = !empty($search_query)
                ? "&search_query=" . urlencode($search_query)
                : "";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" .
                        htmlspecialchars($row["STAFF_NRIC"]) .
                        "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["STAFF_FNAME"]) .
                        "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["STAFF_LNAME"]) .
                        "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["STAFF_PHONE"]) .
                        "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["STAFF_HIRE_DATE"]) .
                        "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";

                    $status = $row["STAFF_STATUS"] ?? "Active";
                    echo "<td>" . htmlspecialchars($status) . "</td>";

                    if ($status == "Active") {
                        echo "<td><a href='?action=resign&nric=" .
                            urlencode($row["STAFF_NRIC"]) .
                            $search_param_url .
                            "' class='btn-resign' onclick=\"return confirm('Are you sure you want to resign this staff?');\">Resign</a></td>";
                    } else {
                        echo "<td><a href='?action=rehire&nric=" .
                            urlencode($row["STAFF_NRIC"]) .
                            $search_param_url .
                            "' class='btn-rehire' onclick=\"return confirm('Rehire this staff?');\">Rehire</a></td>";
                    }

                    echo "</tr>";
                }
            } else {
                if (!empty($search_query)) {
                    echo "<tr><td colspan='8'>No staff found matching your search.</td></tr>";
                } else {
                    echo "<tr><td colspan='8'>No staff found</td></tr>";
                }
            }

            $stmt_select->close();
            $conn->close();
            ?>
        </table>

        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
