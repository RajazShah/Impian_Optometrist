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

if (isset($_POST['shipping_option'])) {
    $_SESSION['shipping_option'] = $_POST['shipping_option'];
} else {
    if (!isset($_SESSION['shipping_option'])) {
        $_SESSION['shipping_option'] = 'delivery';
    }
}
$shipping_option = $_SESSION['shipping_option'];


$cart_items = [];
$subtotal = 0;
$shipping = ($shipping_option === 'delivery') ? 10.00 : 0; 

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
                'name' => $row['ITEM_BRAND'] . ' ' . $row['item_name'],
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
    <title>Checkout - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="checkout.css"> 
    <link rel="stylesheet" href="cart-features.css">
</head>
<body class="page-background">

    <header class="main-header">
        </header>

    <main class="checkout-container">
        <h1>Checkout</h1>
        
        <div class="checkout-layout">
            
            <form class="checkout-details" id="checkout-form" action="order-process.php" method="POST">
                <?php if ($shipping_option === 'delivery'): ?>
                    <h2>Shipping Address</h2>
                    
                    <div class="form-row">
                        <div class="input-group"><label for="first_name">First Name</label><input type="text" id="first_name" name="first_name" required></div>
                        <div class="input-group"><label for="last_name">Last Name</label><input type="text" id="last_name" name="last_name" required></div>
                    </div>
                    <div class="input-group"><label for="address">Address</label><input type="text" id="address" name="address" required></div>
                    <div class="input-group"><label for="city">City</label><input type="text" id="city" name="city" required></div>
                    <div class="form-row">
                        <div class="input-group"><label for="state">State</label><input type="text" id="state" name="state" required></div>
                        <div class="input-group"><label for="postcode">Postcode</label><input type="text" id="postcode" name="postcode" required></div>
                    </div>
                    
                <?php else: ?>
                    <h2>Pickup Details</h2>
                    <p>You've selected in-store pickup.</p>
                    <p><strong>Address:</strong><br>
                       6, Jalan Suadamai 1/2, Tun Hussein Onn,<br>
                       43200 Cheras, Selangor</p>
                    <p><strong>Hours:</strong><br>
                       Monday-Sunday (Closes on Friday), 10:00 AM - 8:00 PM</p>
                <?php endif; ?>
            </form>

            <div class="order-summary-checkout">
                <h2>Your Order</h2>
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>RM <?php echo number_format($item['line_total'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>RM <?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <?php if ($shipping_option === 'delivery'): ?>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>RM <?php echo number_format($shipping, 2); ?></span>
                </div>
                <?php endif; ?>

                <div class="summary-total">
                    <span>Total</span>
                    <span>RM <?php echo number_format($total, 2); ?></span>
                </div>
                <button type="submit" form="checkout-form" class="btn-checkout">Confirm & Pay</button>
            </div>
            
        </div>
    </main>
    
    <script src="script.js"></script> 
</body>
</html>