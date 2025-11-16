<?php
include "../db_connect.php";

$token = $_GET["token"] ?? null;
$message = "";

if (!$token) {
    $message = "Error: No confirmation token provided. This link is invalid.";
} else {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // 1. Find the order with this token
        $stmt_find = $conn->prepare(
            "SELECT * FROM orders WHERE confirmation_token = ?",
        );
        $stmt_find->bind_param("s", $token);
        $stmt_find->execute();
        $result = $stmt_find->get_result();

        if ($result->num_rows === 1) {
            $order = $result->fetch_assoc();

            // 2. Insert the order into the 'sales' table
            // **** IMPORTANT: Make sure these columns match your 'sales' table ****
            $stmt_insert = $conn->prepare("
                INSERT INTO sales (original_order_id, user_id, total_price, shipping_option, shipping_address, order_date)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt_insert->bind_param(
                "iidsss",
                $order["order_id"],
                $order["user_id"],
                $order["total_price"],
                $order["shipping_option"],
                $order["shipping_address"],
                $order["order_date"],
            );
            $stmt_insert->execute();

            // 3. Delete the order from the 'orders' table
            $stmt_delete = $conn->prepare(
                "DELETE FROM orders WHERE order_id = ?",
            );
            $stmt_delete->bind_param("i", $order["order_id"]);
            $stmt_delete->execute();

            // 4. Commit the transaction
            $conn->commit();

            $message =
                "<h2>Thank You!</h2><p>Your order (ID: " .
                $order["order_id"] .
                ") has been confirmed and is now being processed.</p>";
        } else {
            // Token not found or already used
            $message =
                "Error: This confirmation link is invalid or has already expired.";
        }
    } catch (Exception $e) {
        // An error occurred, roll back the changes
        $conn->rollback();
        $message =
            "An error occurred while confirming your order. Please try again later. Error: " .
            $e->getMessage();
    }

    // Close all statements
    if (isset($stmt_find)) {
        $stmt_find->close();
    }
    if (isset($stmt_insert)) {
        $stmt_insert->close();
    }
    if (isset($stmt_delete)) {
        $stmt_delete->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        .container { background: #fff; border: 1px solid #ccc; border-radius: 8px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $message; ?>
        <p><a href="https://yourwebsite.com">Back to Homepage</a></p>
    </div>
</body>
</html>
