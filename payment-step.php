<?php
session_start();
include 'db_connect.php'; 

// --- Security & Cart Check ---
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// --- Recalculate Total (to be safe) ---
$cart = $_SESSION['cart'];
$item_ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($item_ids), '?'));
$types = str_repeat('s', count($item_ids));

$sql_items = "SELECT ITEM_ID, ITEM_PRICE FROM item WHERE ITEM_ID IN ($placeholders)";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, $types, ...$item_ids);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);

$subtotal = 0;
while ($row = mysqli_fetch_assoc($result_items)) {
    $subtotal += $row['ITEM_PRICE'] * $cart[$row['ITEM_ID']];
}
mysqli_stmt_close($stmt_items);
mysqli_close($conn);

$shipping = ($_SESSION['shipping_option'] === 'delivery') ? 10.00 : 0;
$total = $subtotal + $shipping;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Impian Optometrist</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="checkout.css"> 
    <link rel="stylesheet" href="cart-features.css">
</head>
<body class="page-background">

    <main class="checkout-container">
        <h1>Complete Your Payment</h1>
        
        <div class="checkout-layout">
            
            <form action="order-process.php" method="POST">
                <div class="checkout-details">
                    <h2>1. Scan to Pay</h2>
                    <p>Please scan the QR code below to pay RM <?php echo number_format($total, 2); ?>.</p>
                    
                    <div class="qr-code-container">
                        <img src="images/qr_code.jpg" alt="Scan QR Code to Pay">
                    </div>
                    
                    <h2>2. Confirm Your Payment</h2>
                    <p>After you have paid, click the button below to confirm your order.</p>

                    <button type="submit" class="btn-checkout">
                        I Have Paid, Confirm My Order
                    </button>
                </div>
                
                <?php if ($_SESSION['shipping_option'] === 'delivery'): ?>
                    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name']); ?>">
                    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name']); ?>">
                    <input type="hidden" name="address" value="<?php echo htmlspecialchars($_POST['address']); ?>">
                    <input type="hidden" name="city" value="<?php echo htmlspecialchars($_POST['city']); ?>">
                    <input type="hidden" name="state" value="<?php echo htmlspecialchars($_POST['state']); ?>">
                    <input type="hidden" name="postcode" value="<?php echo htmlspecialchars($_POST['postcode']); ?>">
                <?php endif; ?>
            </form>

            <div class="order-summary-checkout">
                <h2>Your Order</h2>
                <div class="summary-total">
                    <span>Total to Pay</span>
                    <span>RM <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            
        </div>
    </main>
    
</body>
</html>