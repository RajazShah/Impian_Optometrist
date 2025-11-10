<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];

$order_details = null;
$order_items = [];
$subtotal = 0;

$sql_order = "SELECT * FROM orders WHERE order_id = ?";
if ($stmt_order = mysqli_prepare($conn, $sql_order)) {
    mysqli_stmt_bind_param($stmt_order, "i", $order_id);
    mysqli_stmt_execute($stmt_order);
    $result_order = mysqli_stmt_get_result($stmt_order);
    $order_details = mysqli_fetch_assoc($result_order);
    mysqli_stmt_close($stmt_order);
}

if (!$order_details) {
    unset($_SESSION['last_order_id']);
    header("Location: index.php");
    exit();
}

$sql_items = "SELECT oi.quantity, oi.price_per_item, i.item_name, i.ITEM_BRAND
              FROM order_items AS oi
              JOIN item AS i ON oi.product_id = i.ITEM_ID
              WHERE oi.order_id = ?";
              
if ($stmt_items = mysqli_prepare($conn, $sql_items)) {
    mysqli_stmt_bind_param($stmt_items, "i", $order_id);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);
    while ($row = mysqli_fetch_assoc($result_items)) {
        $order_items[] = $row;
        $subtotal += $row['price_per_item'] * $row['quantity'];
    }
    mysqli_stmt_close($stmt_items);
}

$total_price = $order_details['total_price'];
$shipping_fee = $total_price - $subtotal; 

unset($_SESSION['last_order_id']);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed! - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="checkout.css"> 
    
    <link rel="stylesheet" href="order-success.css"> 

</head>
<body class="page-background">

    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <main class="success-container">
        <h1>Thank You For Your Order!</h1>
        <p>Your receipt for Order ID: <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>

        <div class="receipt-summary order-summary-checkout"> 
            <h2>Order Summary</h2>
            
            <div class="receipt-details">
                <?php if ($order_details['shipping_option'] === 'delivery'): ?>
                    <strong>Shipping To:</strong>
                    <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($order_details['shipping_address']); ?></p>
                <?php else: ?>
                    <strong>Pickup At:</strong>
                    <p>6, Jalan Suadamai 1/2, Tun Hussein Onn,<br>
                       43200 Cheras, Selangor</p>
                <?php endif; ?>
            </div>

            <div class="receipt-items">
                <?php foreach ($order_items as $item): ?>
                    <div class="summary-item">
                        <span>
                            <?php echo htmlspecialchars($item['ITEM_BRAND'] . ' ' . $item['item_name']); ?> 
                            (x<?php echo $item['quantity']; ?>)
                        </span>
                        <span>RM <?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>RM <?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>RM <?php echo number_format($shipping_fee, 2); ?></span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>RM <?php echo number_format($total_price, 2); ?></span>
                </div>
            </div>
        </div>
        
        <a href="index.php" class="btn-home">← Back to Homepage</a>
    </main>

    <script src="script.js"></script> 
</body>
</html>