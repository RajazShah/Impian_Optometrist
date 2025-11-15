<?php
include "../db_connect.php";

// Get the current search query from the URL (using GET method)
$search_query = isset($_GET["search_query"]) ? $_GET["search_query"] : "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");

// Get URL without query params
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Details</title>
    <link rel="stylesheet" href="user_details.css">
</head>
<body>
    <div class="appointments-container">
        <h2>User Details</h2>

        <div class="search-container" style="margin-bottom: 20px;">
            <form method="GET" action="<?php echo $current_page_url; ?>">
                <label for="search_query">Search by First Name:</label>
                <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                    $search_query,
                ); ?>" placeholder="Enter first name...">
                <button type="submit">Search</button>
                <a href="<?php echo $current_page_url; ?>">Clear</a>
            </form>
        </div>

        <table class="appointments-table">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
            </tr>

            <?php
            $sql = "SELECT * FROM users";
            $params = [];
            $types = "";

            if (!empty($search_query)) {
                $sql .= " WHERE first_name LIKE ?";
                $search_term = "%" . $search_query . "%";
                $params[] = &$search_term; // Add as reference
                $types .= "s";
            }

            $stmt = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" .
                        htmlspecialchars($row["first_name"]) .
                        "</td>";
                    echo "<td>" . htmlspecialchars($row["last_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["phone_number"]) .
                        "</td>";
                    echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                if (!empty($search_query)) {
                    echo "<tr><td colspan='5'>No users found matching your search.</td></tr>";
                } else {
                    echo "<tr><td colspan->'5'>No users found</td></tr>";
                }
            }

            $stmt->close();
            $conn->close();
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
