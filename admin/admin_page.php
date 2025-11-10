<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impian Optometrist Dashboard</title>
    <link rel="stylesheet" href="admin_page.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header-section">
        <h1 class="main-title">IMPIAN OPTOMETRIST DASHBOARD</h1>
        <p class="tagline">Welcome, <?php echo $_SESSION["first_name"]; ?></p>
    </div>

        <div class="card-section">
            <div class="action-card">
                <a href="orders.php" class="action-button">Orders</a>
                <a href="appointments.php" class="action-button">Appointments</a>
                <a href="stock.html" class="action-button">Stock</a>
                <a href="user_details.php" class="action-button">Users</a>
                <a href="staff_details.php" class="action-button">Staffs</a>
                <a href="analytics/analytics.php" class="action-button">Analytics</a>
                <a href="../index.php" class="action-button">Exit</a>
            </div>
    </div>
</body>
</html>
