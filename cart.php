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

// Track appointment requirement
$requires_appointment = false;
$appointment_reason = "";
$has_frames = false;
$has_lenses = false;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    
    $item_ids = array_keys($_SESSION['cart']);

    // Handle empty cart gracefully
    if (!empty($item_ids)) {
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $types = str_repeat('s', count($item_ids));

        $sql = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image, CATEGORY_ID 
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
                
                // Check Categories
                if ($row['CATEGORY_ID'] === 'CAT001') {
                    $has_frames = true;
                }
                // We still detect lenses, but they won't trigger the appointment anymore
                if ($row['CATEGORY_ID'] === 'CAT002') {
                    $has_lenses = true;
                }

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
}
mysqli_close($conn);

// --- UPDATED LOGIC: Only Frames trigger the appointment ---
if ($has_frames) {
    $requires_appointment = true;
    $appointment_reason = "Frame check";
}
// Note: We removed the check for $has_lenses, so lenses alone are now free to checkout.
// ----------------------------------------------------------

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
            alert("<?php echo htmlspecialchars($_GET['error']); ?>");
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
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
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
                        <label for="radio-delivery">Delivery (RM 10.00)</label>
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

                <div class="checkout-actions">
                    <?php if (empty($cart_items)): ?>
                        <button type="button" class="btn-checkout" 
                                style="background-color: #ccc; cursor: not-allowed;"
                                onclick="alert('Your cart is empty.');">
                            Proceed to Checkout
                        </button>
                    <?php else: ?>
                        
                        <?php if ($requires_appointment): ?>
                            <div id="appointment-section" style="display: none;">
                                <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 14px;">
                                    <strong>Note:</strong> Delivery for frames requires an appointment.
                                </div>
                                <a href="book-appointment.php?cart_reason=<?php echo urlencode($appointment_reason); ?>" 
                                   class="btn-checkout" 
                                   style="text-align: center; text-decoration: none; display: block; background-color: #000;">
                                    Book Appointment to Continue
                                </a>
                            </div>
                        <?php endif; ?>

                        <button type="submit" id="btn-proceed" class="btn-checkout">Proceed to Checkout</button>

                    <?php endif; ?>
                </div>

            </form>

        </div>
    </main>
    
    <script src="script.js"></script> 

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get PHP status
            const requiresAppt = <?php echo $requires_appointment ? 'true' : 'false'; ?>;
            
            const radioDelivery = document.getElementById('radio-delivery');
            const radioPickup = document.getElementById('radio-pickup');
            
            const apptSection = document.getElementById('appointment-section');
            const btnProceed = document.getElementById('btn-proceed');

            function updateButtons() {
                // If cart has no Frames (requiresAppt is false), always allow checkout
                if (!requiresAppt) {
                    if(apptSection) apptSection.style.display = 'none';
                    if(btnProceed) btnProceed.style.display = 'block';
                    return;
                }

                // If cart HAS Frames:
                if (radioDelivery.checked) {
                    // Delivery = Show Appointment Button
                    if(apptSection) apptSection.style.display = 'block';
                    if(btnProceed) btnProceed.style.display = 'none';
                } else {
                    // Pickup = Show Proceed Button
                    if(apptSection) apptSection.style.display = 'none';
                    if(btnProceed) btnProceed.style.display = 'block';
                }
            }

            if (radioDelivery && radioPickup) {
                radioDelivery.addEventListener('change', updateButtons);
                radioPickup.addEventListener('change', updateButtons);
                updateButtons();
            }
        });
    </script>
</body>
</html>