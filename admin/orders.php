<?php
include "../db_connect.php";

// Get search and filter values from the URL
$search_query = $_GET["search_query"] ?? "";
$filter_date = $_GET["filter_date"] ?? "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Order Details</h2>

        <div class="controls-container">
            <form action="orders.php" method="GET" class="search-filter-form">

                <div class="form-group search-group">
                    <label for="search_query">Search:</label>
                    <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                        $search_query,
                    ); ?>" placeholder="Order ID or Customer Name">
                </div>

                <div class="form-group filter-group">
                    <label for="filter_date">Filter by Date:</label>
                    <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars(
                        $filter_date,
                    ); ?>">
                </div>

                <div class="form-group button-group">
                    <button type="submit" class="btn-filter">Search / Filter</button>
                    <a href="<?php echo $current_page_url; ?>" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>
        <table class="appointments-table">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th> <th>Total Price</th>
                <th>Shipping Option</th>
                <th>Shipping Address</th>
                <th>Order Status</th>
                <th>Order Date</th>
            </tr>
            <?php
            // --- START: MODIFIED SQL QUERY ---
            // We JOIN users table to get the name
            $sql = "SELECT o.order_id, u.first_name, u.last_name, o.total_price, o.shipping_option, o.shipping_address, o.order_status, o.order_date
                    FROM orders o
                    JOIN users u ON o.user_id = u.id";

            $where_clauses = [];
            $params = [];
            $types = "";

            // Add search logic
            if (!empty($search_query)) {
                $where_clauses[] = "(o.order_id LIKE ? OR u.first_name LIKE ?)";
                $search_term = "%" . $search_query . "%";
                $params[] = $search_term;
                $params[] = $search_term;
                $types .= "ss";
            }

            // Add filter logic
            if (!empty($filter_date)) {
                $where_clauses[] = "o.order_date = ?";
                $params[] = $filter_date;
                $types .= "s";
            }

            if (!empty($where_clauses)) {
                $sql .= " WHERE " . implode(" AND ", $where_clauses);
            }

            // Sort by order date, newest first
            $sql .= " ORDER BY o.order_date DESC";

            $stmt_select = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt_select->bind_param($types, ...$params);
            }

            $stmt_select->execute();
            $result = $stmt_select->get_result();
            // --- END: MODIFIED SQL QUERY ---

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Create a status class (e.g., "status-pending")
                    $status_class =
                        "status-" .
                        strtolower(htmlspecialchars($row["order_status"]));

                    echo "<tr>";
                    echo "<td>" . $row["order_id"] . "</td>";
                    echo "<td>" .
                        htmlspecialchars(
                            $row["first_name"] . " " . $row["last_name"],
                        ) .
                        "</td>";
                    echo "<td>" . $row["total_price"] . "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["shipping_option"]) .
                        "</td>";
                    echo "<td>" .
                        htmlspecialchars($row["shipping_address"]) .
                        "</td>";
                    // Apply the status class here for styling
                    echo "<td><span class='status " .
                        $status_class .
                        "'>" .
                        htmlspecialchars($row["order_status"]) .
                        "</span></td>";
                    echo "<td>" . $row["order_date"] . "</td>";
                    echo "</tr>";
                }
            } else {
                // Fixed colspan to 7
                echo "<tr><td colspan='7'>No orders found.</td></tr>";
            }

            $stmt_select->close();
            $conn->close();
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
