<?php
session_start();
include 'db_connect.php'; // Include your database connection

// --- SECURITY CHECK ---
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['last_order_id'])) {
    // If not logged in OR no order was just placed, go home
    header("Location: index.php");
    exit();
}

$last_order_id = $_SESSION['last_order_id'];
$order = null;
$order_items = [];
$subtotal = 0;

// 1. Get the main order details
$sql_order = "SELECT * FROM orders WHERE order_id = ?";
if ($stmt_order = mysqli_prepare($conn, $sql_order)) {
    mysqli_stmt_bind_param($stmt_order, "i", $last_order_id);
    mysqli_stmt_execute($stmt_order);
    $result_order = mysqli_stmt_get_result($stmt_order);
    $order = mysqli_fetch_assoc($result_order);
    mysqli_stmt_close($stmt_order);
}

if (!$order) {
    echo "Error: Could not find order.";
    exit();
}

// 2. Get the items for this order
$sql_items = "SELECT oi.quantity, oi.price_per_item, i.ITEM_BRAND, i.item_name 
              FROM order_items oi
              JOIN item i ON oi.product_id = i.ITEM_ID
              WHERE oi.order_id = ?";

if ($stmt_items = mysqli_prepare($conn, $sql_items)) {
    mysqli_stmt_bind_param($stmt_items, "i", $last_order_id);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);
    
    while ($item = mysqli_fetch_assoc($result_items)) {
        $line_total = $item['price_per_item'] * $item['quantity'];
        $subtotal += $line_total;
        $order_items[] = [
            'name' => $item['ITEM_BRAND'] . ' ' . $item['item_name'],
            'quantity' => $item['quantity'],
            'line_total' => $line_total
        ];
    }
    mysqli_stmt_close($stmt_items);
}
mysqli_close($conn);

$shipping = ($order['shipping_option'] === 'delivery') ? 10.00 : 0;
$total = $order['total_price'];

// 3. Clear the session variable so this receipt doesn't show up again
unset($_SESSION['last_order_id']);
unset($_SESSION['shipping_option']); // Also clear this

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Impian Optometrist</title>
    
    <link rel="stylesheet" href="receipt-style.css"> 
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-background">

    <div class="confirmation-container">
        <h1>Thank You For Your Order!</h1>
        <p>Your order (ID: #<?php echo $last_order_id; ?>) has been confirmed.</p>

        <div class="receipt-container">
            <div class="receipt-header">
                <h2>IMPIAN OPTOMETRIST</h2>
                <p>6, Jalan Suadamai 1/2, Tun Hussein Onn</p>
                <p>43200 Cheras, Selangor</p>
            </div>

            <div class="receipt-title">
                <p>********************************</p>
                <p>SALES RECEIPT</p>
                <p>********************************</p>
                <p>Invoice #: <?php echo $last_order_id; ?></p> 
            </div>

            <div class="receipt-body">
                <?php if ($order['shipping_option'] === 'delivery' && !empty($order['shipping_address'])): ?>
            <div class="shipping-address-info">
                <p><strong>SHIP TO:</strong></p>
                <p>
                    <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                </p>
            </div>
            <p>-----------------------------------</p>
            <?php endif; ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['line_total'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p>-----------------------------------</p>
                
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td>RM <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Shipping:</td>
                        <td>RM <?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td>Total:</td>
                        <td>RM <?php echo number_format($total, 2); ?></td>
                    </tr>
                </table>
            </div>

            <div class="receipt-footer">
                <p>***********************************</p>
                <p>Thank You!</p>
            </div>
        </div>

        <button id="print-button" class="btn-print">Print Receipt</button>
        <a href="index.php" class="btn-back">Back to Home</a>
    </div>

    <script>
        document.getElementById('print-button').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>