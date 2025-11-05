<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

    <div class="admin-container">
        <div class="admin-content">
            
            <div class="admin-title">
                <h1>Impian Optometrist</h1>
                <p>All Your Eyes Needs In 1 Place</p>
            </div>

            <nav class="admin-menu">
                <ul>
                    <li><a href="admin_orders.php">Orders</a></li>
                    <li><a href="admin_completed.php">Completed Orders</a></li>
                    <li><a href="admin_stock.php">Stock</a></li>
                </ul>
            </nav>

        </div>
    </div>

</body>
</html>