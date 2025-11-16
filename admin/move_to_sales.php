<?php
include "../db_connect.php";

$order_id = $_GET["order_id"] ?? null;

if (!$order_id) {
    header("Location: orders.php?error=No Order ID provided.");
    exit();
}

// Start a database transaction for safety
$conn->begin_transaction();

try {
    // 1. Find the order in the 'orders' table
    $stmt_find = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt_find->bind_param("i", $order_id);
    $stmt_find->execute();
    $result = $stmt_find->get_result();

    if ($result->num_rows === 1) {
        $order = $result->fetch_assoc();

        // 2. Insert the order into the 'sales' table
        // *** IMPORTANT: Make sure these columns match your 'sales' table structure ***
        $stmt_insert = $conn->prepare("
            INSERT INTO sales (original_order_id, user_id, total_price, shipping_option, shipping_address, order_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        // Make sure these variables match the columns from your 'orders' table
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
        $stmt_delete = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt_delete->bind_param("i", $order["order_id"]);
        $stmt_delete->execute();

        // 4. If all good, commit the changes
        $conn->commit();

        // Redirect back to the orders page with a success message
        header(
            "Location: orders.php?success=Order " .
                $order_id .
                " confirmed and moved to sales.",
        );
    } else {
        // Order not found, roll back
        $conn->rollback();
        header("Location: orders.php?error=Order not found.");
    }
} catch (Exception $e) {
    // An error occurred, roll back all changes
    $conn->rollback();
    header("Location: orders.php?error=Database error: " . $e->getMessage());
}

// Close all database statements
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

?>
