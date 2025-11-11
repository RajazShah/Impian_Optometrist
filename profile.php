<?php
// Always start the session
session_start();
include 'db_connect.php'; // Include your database connection

// --- SECURITY CHECK ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];
$user = null; 

$sql = "SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result); 
    mysqli_stmt_close($stmt);
}

$orders = []; 
$sql_orders = "SELECT order_id, order_date, total_price, order_status 
               FROM orders 
               WHERE user_id = ? 
               ORDER BY order_date DESC"; 

if ($stmt_orders = mysqli_prepare($conn, $sql_orders)) {
    mysqli_stmt_bind_param($stmt_orders, "i", $user_id);
    
    mysqli_stmt_execute($stmt_orders);

    $result_orders = mysqli_stmt_get_result($stmt_orders);
    
    while ($order = mysqli_fetch_assoc($result_orders)) {
        $orders[] = $order;
    }
    
    mysqli_stmt_close($stmt_orders);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="profile-style.css"> 
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

    <header class="main-header">
        <div class="logo"><h1>IMPIAN OPTOMETRIST</h1><input type="search" name="search_query" placeholder="Search frames..." class="search-box"></div>
        <nav class="main-nav"><ul><li><a href="index.php#frames-section">Frames</a></li><li><a href="index.php#contact-section">Contact Lense</a></li><li><a href="index.php#clip-section">Clip On</a></li></ul></nav>
        <div class="user-actions"><?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?><div class="user-icons-box"><a href="customer-appointment.php" title="Appointment"><img src="images/appointment-icon.png" alt="Appointment"></a><a href="cart.php" title="Cart"><img src="images/bag-icon.png" alt="Cart"></a><div class="profile-dropdown"><a href="#" id="user-icon-link" title="Profile"><img src="images/user-icon.png" alt="User Profile"></a><div class="dropdown-content"><a href="profile.php">My Profile</a><a href="logout.php">Sign Out</a></div></div></div><?php } else { ?><a href="#" id="login-link" class="login-signup-link">Login / Sign Up</a><?php } ?></div>
    </header>

    <main class="profile-container">
        <h1>My Profile</h1>

        <div class="profile-layout">

            <section class="profile-section account-details">
                <h2>Account Details</h2>
                <?php if ($user): // Check if user data was fetched ?>
                    <div class="detail-row">
                        <span class="detail-label">First Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['first_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Last Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['last_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['phone_number'] ? $user['phone_number'] : 'Not Provided'); ?></span>
                    </div>
                    <div class="action-buttons">
                        <a href="edit-profile.php" class="btn btn-secondary">Edit Profile</a>
                        <a href="change-password.php" class="btn btn-secondary">Change Password</a>
                    </div>
                <?php else: ?>
                    <p>Could not load user details.</p>
                <?php endif; ?>
            </section>

            <section class="profile-section order-history">
                <h2>Order History</h2>
                <?php if (empty($orders)): ?>
                    <p>You have not placed any orders yet.</p>
                <?php else: ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($order['order_date']))); ?></td>
                                    <td>RM <?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
            
        </div>
    </main>

    <script src="script.js"></script> 
</body>
</html>