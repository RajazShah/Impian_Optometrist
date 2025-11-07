<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Start the transaction
mysqli_begin_transaction($conn);

try {
    // 1. Get items from cart and check stock
    $subtotal = 0;
    $cart = $_SESSION['cart'];
    $item_ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('s', count($item_ids));
    
    // Get item data and lock the rows for the transaction
    $sql_items = "SELECT ITEM_ID, ITEM_PRICE, ITEM_QTY FROM item WHERE ITEM_ID IN ($placeholders) FOR UPDATE";
    $stmt_items = mysqli_prepare($conn, $sql_items);
    mysqli_stmt_bind_param($stmt_items, $types, ...$item_ids);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);
    
    $item_data = []; 
    while ($row = mysqli_fetch_assoc($result_items)) {
        $item_data[$row['ITEM_ID']] = [
            'price' => $row['ITEM_PRICE'],
            'stock' => $row['ITEM_QTY']
        ];
    }
    mysqli_stmt_close($stmt_items);
    
    // Loop to check stock and calculate subtotal
    foreach ($cart as $item_id => $quantity) {
        if (!isset($item_data[$item_id])) {
            throw new Exception("An item in your cart ($item_id) could not be found.");
        }
        if ($item_data[$item_id]['stock'] < $quantity) {
            // Not enough stock, throw an error
            throw new Exception("Sorry, there is not enough stock for item $item_id. Only " . $item_data[$item_id]['stock'] . " available.");
        }
        // If stock is OK, add to subtotal
        $subtotal += $item_data[$item_id]['price'] * $quantity;
    }

    // 2. Calculate totals and get shipping info
    $shipping_option = $_SESSION['shipping_option'];
    $shipping_fee = ($shipping_option === 'delivery') ? 10.00 : 0;
    $total_price = $subtotal + $shipping_fee;

    $user_id = $_SESSION['id'];
    $shipping_address = NULL;
    
    if ($shipping_option === 'delivery') {
        $shipping_address = $_POST['first_name'] . " " . $_POST['last_name'] . "\n" .
                            $_POST['address'] . "\n" .
                            $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['postcode'];
    }

    // 3. Create the main 'orders' entry
    $sql_order = "INSERT INTO orders (user_id, total_price, shipping_option, shipping_address) VALUES (?, ?, ?, ?)";
    $stmt_order = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param($stmt_order, "idss", $user_id, $total_price, $shipping_option, $shipping_address);
    mysqli_stmt_execute($stmt_order);
    
    $new_order_id = mysqli_insert_id($conn); // Get the new order ID
    mysqli_stmt_close($stmt_order);

    // 4. Prepare statements for 'order_items' and stock 'item' update
    $sql_items_insert = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
    $stmt_items_insert = mysqli_prepare($conn, $sql_items_insert);
    
    $sql_stock_update = "UPDATE item SET ITEM_QTY = ITEM_QTY - ? WHERE ITEM_ID = ?";
    $stmt_stock_update = mysqli_prepare($conn, $sql_stock_update);
    
    // 5. Loop through cart: insert order items AND update stock
    foreach ($cart as $item_id => $quantity) {
        
        // A: Insert the order item
        $price = $item_data[$item_id]['price']; 
        mysqli_stmt_bind_param($stmt_items_insert, "isid", $new_order_id, $item_id, $quantity, $price);
        
        if (!mysqli_stmt_execute($stmt_items_insert)) {
            throw new Exception("Error: Failed to save order item. " . mysqli_stmt_error($stmt_items_insert));
        }
        
        // B: Update the stock
        mysqli_stmt_bind_param($stmt_stock_update, "is", $quantity, $item_id); 
        
        if (!mysqli_stmt_execute($stmt_stock_update)) {
            throw new Exception("Error: Failed to update stock. " . mysqli_stmt_error($stmt_stock_update));
        }
    }
    
    // Close prepared statements
    mysqli_stmt_close($stmt_items_insert);
    mysqli_stmt_close($stmt_stock_update); 
    
    // 6. All good! Commit the transaction
    mysqli_commit($conn);
    
    // 7. Clear the cart and redirect
    unset($_SESSION['cart']);
    unset($_SESSION['shipping_option']);
    $_SESSION['last_order_id'] = $new_order_id; 
    
    mysqli_close($conn);
    header("Location: order-success.php");
    exit();

} catch (Exception $e) {
    // 8. Something went wrong! Roll back all changes
    mysqli_rollback($conn); 
    mysqli_close($conn);
    
    // Save the error message and send user back to cart
    $_SESSION['cart_error'] = "Error: " . $e->getMessage();
    header("Location: cart.php");
    exit();
}
?>