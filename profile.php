<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
include 'db_connect.php'; // Include your database connection

// --- SECURITY CHECK ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];
$user = null; 

$sql = "SELECT first_name, last_name, email, phone_number, profile_image, eye_power_left, eye_power_right FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result); 
    mysqli_stmt_close($stmt);
}

// --- PAGINATION LOGIC ---
$items_per_page = 5;
// Get current page from URL, default to 1 if not set
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $items_per_page;

// 1. Get total number of orders to calculate total pages
$total_orders = 0;
$sql_count = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
if ($stmt_count = mysqli_prepare($conn, $sql_count)) {
    mysqli_stmt_bind_param($stmt_count, "i", $user_id);
    mysqli_stmt_execute($stmt_count);
    $result_count = mysqli_stmt_get_result($stmt_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $total_orders = $row_count['total'];
    mysqli_stmt_close($stmt_count);
}
$total_pages = ceil($total_orders / $items_per_page);

// 2. Get the specific 5 orders for this page
$orders = []; 
$sql_orders = "SELECT order_id, order_date, total_price, order_status 
               FROM orders 
               WHERE user_id = ? 
               ORDER BY order_date DESC 
               LIMIT ? OFFSET ?"; 

if ($stmt_orders = mysqli_prepare($conn, $sql_orders)) {
    // "iii" means three integers: user_id, limit, and offset
    mysqli_stmt_bind_param($stmt_orders, "iii", $user_id, $items_per_page, $offset);
    
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
    <link rel="stylesheet" href="cart-features.css">
</head>
<body>

    <header class="main-header">
        <div class="logo-search-container">
            <h1>IMPIAN OPTOMETRIST</h1>
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="search_query" placeholder="Search items..." class="search-box">
            </form>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php#frames-section">FRAMES</a></li>
                <li><a href="index.php#lenses-section">LENSES</a></li>
                <li><a href="index.php#clip-section">CLIP-ON</a></li>
                <li><a href="index.php#contact-section">CONTACT LENSE</a></li>
            </ul>
        </nav>
        <div class="user-actions"><?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?><div class="user-icons-box"><a href="customer-appointment.php" title="Appointment"><img src="images/appointment-icon.png" alt="Appointment"></a><a href="cart.php" title="Cart"><img src="images/bag-icon.png" alt="Cart"></a><div class="profile-dropdown"><a href="#" id="user-icon-link" title="Profile"><img src="images/user-icon.png" alt="User Profile"></a><div class="dropdown-content"><a href="profile.php">My Profile</a><a href="logout.php">Sign Out</a></div></div></div><?php } else { ?><a href="#" id="login-link" class="login-signup-link">Login / Sign Up</a><?php } ?></div>
    </header>

    <main class="profile-container">
        <div class="profile-layout">

            <section class="profile-section profile-picture-section">
            <h2>Profile Picture</h2>
            
            <form action="upload-profile-picture.php" method="POST" enctype="multipart/form-data">
                <div class="profile-picture-container">
                    
                    <?php
                    $profile_image_path = 'images/default_profile_pic.jpg'; // Default
                    if (!empty($user['profile_image'])) {
                        $profile_image_path = 'uploads/profiles/' . htmlspecialchars($user['profile_image']);
                    }
                    ?>
                    <img src="<?php echo $profile_image_path; ?>" alt="Profile Picture" class="profile-img" id="profileImagePreview">
                    
                    <input type="file" id="profilePictureInput" name="profilePicture" accept="image/*" style="display: none;">
                    
                    <label for="profilePictureInput" class="btn btn-secondary">Upload Picture</label>
                </div>
                
                <button type="submit" class="btn btn-secondary">Save Picture</button>
                
            </form>
            </section>

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
                    <div class="detail-row">
                        <span class="detail-label">Left Eye (OS):</span>
                        <span class="detail-value"><?php echo htmlspecialchars(!empty($user['eye_power_left']) ? $user['eye_power_left'] : 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Right Eye (OD):</span>
                        <span class="detail-value"><?php echo htmlspecialchars(!empty($user['eye_power_right']) ? $user['eye_power_right'] : 'N/A'); ?></span>
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
                    <div class="pagination" style="margin-top: 20px; display: flex; gap: 10px;">
                        <?php if ($page > 1): ?>
                            <a href="profile.php?page=<?php echo $page - 1; ?>" class="btn btn-secondary">&laquo; Previous</a>
                        <?php endif; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="profile.php?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        
        </div> </main> 
        <script src="script.js"></script> 
</body>
</html>