<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

$cart_items = [];
$subtotal = 0;
$shipping = 0.00; 

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    
    $item_ids = array_keys($_SESSION['cart']);

    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('s', count($item_ids));

    $sql = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image 
            FROM item 
            WHERE ITEM_ID IN ($placeholders)";
            
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, $types, ...$item_ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $item_id = $row['ITEM_ID'];
            $quantity = $_SESSION['cart'][$item_id]; 
            $line_total = $row['ITEM_PRICE'] * $quantity;
            $subtotal += $line_total;
            $cart_items[] = [
                'id' => $item_id,
                'image' => $row['item_image'],
                'name' => $row['ITEM_BRAND'] . ' ' . $row['item_name'],
                'price' => $row['ITEM_PRICE'],
                'quantity' => $quantity,
                'line_total' => $line_total
            ];
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);

$total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="cart-style.css">
    <link rel="stylesheet" href="checkout.css">
</head>
<body class="cart-page">
    <?php if (isset($_GET['error'])): ?>
        <script>
            // This creates a standard browser popup alert
            alert("<?php echo htmlspecialchars($_GET['error']); ?>");
            
            // Optional: Clean the URL after showing the alert so it doesn't show again on refresh
            window.history.replaceState(null, null, window.location.pathname);
        </script>
    <?php endif; ?>
    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
            <input type="search" name="search_query" placeholder="Search items..." class="search-box">
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php#frames-section">FRAMES</a></li>
                <li><a href="index.php#lenses-section">LENSES</a></li>
                <li><a href="index.php#clip-section">CLIP-ON</a></li>
                <li><a href="index.php#contact-section">CONTACT LENSE</a></li>
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

    <main class="cart-container">
        <a href="index.php" class="btn-back-shopping">← Continue Shopping</a>
        <h1>Your Shopping Cart</h1>

        <div class="cart-layout">
            
            <div class="cart-items">
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cart_items)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">Your cart is empty.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>

                                    <a href="remove_from_cart.php?id=<?php echo htmlspecialchars($item['id']); ?>" 
                                       class="remove-item" 
                                       onclick="return confirm('Are you sure you want to remove this item?');">
                                       Remove
                                    </a>
                            
                                </td>
                                <td>RM <?php echo number_format($item['price'], 2); ?></td>

                                <td>
                                    <form action="update_cart.php" method="POST" class="quantity-form">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <input type="number" name="quantity" class="quantity-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
                                </td>
                            
                                <td>RM <?php echo number_format($item['line_total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <form class="order-summary" action="checkout.php" method="POST">
                <h2>Order Summary</h2>
                
                <div class="shipping-options">
                    <div class="shipping-option">
                        <input type="radio" name="shipping_option" id="radio-delivery" value="delivery">
                        <label for="radio-delivery">Delivery</label>
                    </div>
                    <div class="shipping-option">
                        <input type="radio" name="shipping_option" id="radio-pickup" value="pickup" checked>
                        <label for="radio-pickup">Pickup (Free)</label>
                    </div>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal-price" data-value="<?php echo $subtotal; ?>">
                        RM <?php echo number_format($subtotal, 2); ?>
                    </span>
                </div>
                
                <div class="summary-row" id="shipping-row">
                    <span>Shipping</span>
                    <span id="shipping-fee" data-value="<?php echo $shipping; ?>">
                        RM <?php echo number_format($shipping, 2); ?>
                    </span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span id="total-price">
                        RM <?php echo number_format($total, 2); ?>
                    </span>
                </div>

                <?php if (empty($cart_items)): ?>
                    <button type="button" class="btn-checkout" 
                            onclick="alert('Your cart is empty. Please add items before checking out.');" 
                            style="background-color: #ccc; cursor: not-allowed;">
                        Proceed to Checkout
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn-checkout">Proceed to Checkout</button>
                <?php endif; ?>

            </form>

        </div>
    </main>
    
    <script src="script.js"></script> 
</body>
</html>