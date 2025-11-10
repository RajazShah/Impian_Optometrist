<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Details</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Order Details</h2>
        <table class="appointments-table">
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Total Price</th>
                <th>Shipping Option</th>
                <th>Shipping Address</th>
                <th>Order Status</th>
                <th>Order Date</th>
            </tr>
            <?php
            include "../db_connect.php";

            $sql = "SELECT * FROM orders";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["order_id"] . "</td>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["total_price"] . "</td>";
                    echo "<td>" . $row["shipping_option"] . "</td>";
                    echo "<td>" . $row["shipping_address"] . "</td>";
                    echo "<td>" . $row["order_status"] . "</td>";
                    echo "<td>" . $row["order_date"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
            }
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
