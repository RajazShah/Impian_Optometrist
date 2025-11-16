<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
include 'db_connect.php'; 

$sort_option = $_GET['sort'] ?? 'default';

$order_by = "ORDER BY ITEM_BRAND ASC"; 

if ($sort_option === 'price_asc') {
    $order_by = "ORDER BY ITEM_PRICE ASC";
} else if ($sort_option === 'best_selling') {
    $order_by = "ORDER BY sales_count DESC";
} else if ($sort_option === 'price_desc') {
    $order_by = "ORDER BY ITEM_PRICE DESC";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Clip-Ons - Impian Optometrist</title> 
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="all-frames.css"> 
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="cart-features.css">
</head>
<body class="page-background"> 
    
    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="search_query" placeholder="Search items..." class="search-box">
            </form>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php#frames-section">Frames</a></li>
                <li><a href="index.php#contact-section">Contact Lense</a></li>
                <li><a href="index.php#clip-section">Clip On</a></li>
            </ul>
        </nav>
        <div class="user-actions">
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            ?>
            <div class="user-icons-box">
                <a href="customer-appointment.php" title="Appointment"><img src="images/appointment-icon.png" alt="Appointment"></a>
                <a href="cart.php" title="Cart"><img src="images/bag-icon.png" alt="Cart"></a>
                <div class="profile-dropdown">
                    <a href="#" id="user-icon-link" title="Profile"><img src="images/user-icon.png" alt="User Profile"></a>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Sign Out</a> 
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <a href="#" id="login-link" class="login-signup-link">Login / Sign Up</a>
            <?php } ?>
        </div>
    </header>

    <main class="all-frames-container">
        <h1>All Clip-Ons</h1>
        <a href="index.php#clip-section" class="btn-back">← Back to Home</a>

        <form action="all-clip-ons.php" method="GET" class="filter-form">
            <label for="sort-select">Sort by:</label>
            <select name="sort" id="sort-select" onchange="this.form.submit()">
                <option value="default" <?php if ($sort_option === 'default') echo 'selected'; ?>>
                    Default
                </option>
                <option value="best_selling" <?php if ($sort_option === 'best_selling') echo 'selected'; ?>>
                    Best Selling
                </option>
                <option value="price_asc" <?php if ($sort_option === 'price_asc') echo 'selected'; ?>>
                    Price: Low to High
                </option>
                <option value="price_desc" <?php if ($sort_option === 'price_desc') echo 'selected'; ?>>
                    Price: High to Low
                </option>
            </select>
        </form>

        <div class="all-frames-grid">
            <?php
            // 4. CHANGED CATEGORY_ID
            $sql = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image 
                    FROM item 
                    WHERE CATEGORY_ID = 'CAT003' AND item_name IS NOT NULL AND ITEM_STATUS = 'Available'
                    $order_by"; 
            
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { 
                    echo '<div class="product-card">';
                    echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '">';
                    echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                    echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                        echo '<form action="add_to_cart.php" method="POST" class="cart-form">';
                        echo '    <input type="hidden" name="item_id" value="' . htmlspecialchars($row['ITEM_ID']) . '">';
                        echo '    <button type="submit" class="btn-add-to-cart">Add to Cart</button>';
                        echo '</form>';
                    } else {
                        echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p>No clip-ons found.</p>';
            }
            
            $conn->close();
            ?>
        </div>
    </main>

    <div id="login-modal" class="modal-overlay">
        </div>
    
    <script src="script.js"></script>
    
</body>
</html>