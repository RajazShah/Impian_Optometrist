<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

mysqli_begin_transaction($conn);

try {
    $subtotal = 0;
    $cart = $_SESSION['cart'];
    $item_ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('s', count($item_ids));
    
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
    
    foreach ($cart as $item_id => $quantity) {
        if (!isset($item_data[$item_id])) {
            throw new Exception("An item in your cart ($item_id) could not be found.");
        }
        if ($item_data[$item_id]['stock'] < $quantity) {
            throw new Exception("Sorry, there is not enough stock for item $item_id. Only " . $item_data[$item_id]['stock'] . " available.");
        }
        $subtotal += $item_data[$item_id]['price'] * $quantity;
    }

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

    $sql_order = "INSERT INTO orders (user_id, total_price, shipping_option, shipping_address) VALUES (?, ?, ?, ?)";
    $stmt_order = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param($stmt_order, "idss", $user_id, $total_price, $shipping_option, $shipping_address);
    mysqli_stmt_execute($stmt_order);
    
    $new_order_id = mysqli_insert_id($conn); 
    mysqli_stmt_close($stmt_order);

    $sql_items_insert = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
    $stmt_items_insert = mysqli_prepare($conn, $sql_items_insert);
    
    $sql_stock_update = "UPDATE item SET ITEM_QTY = ITEM_QTY - ? WHERE ITEM_ID = ?";
    $stmt_stock_update = mysqli_prepare($conn, $sql_stock_update);
    
    foreach ($cart as $item_id => $quantity) {
        
        $price = $item_data[$item_id]['price']; 
        mysqli_stmt_bind_param($stmt_items_insert, "isid", $new_order_id, $item_id, $quantity, $price);
        
        if (!mysqli_stmt_execute($stmt_items_insert)) {
            throw new Exception("Error: Failed to save order item. " . mysqli_stmt_error($stmt_items_insert));
        }

        mysqli_stmt_bind_param($stmt_stock_update, "is", $quantity, $item_id); 
        
        if (!mysqli_stmt_execute($stmt_stock_update)) {
            throw new Exception("Error: Failed to update stock. " . mysqli_stmt_error($stmt_stock_update));
        }
    }
    
    mysqli_stmt_close($stmt_items_insert);
    mysqli_stmt_close($stmt_stock_update); 

    mysqli_commit($conn);

    unset($_SESSION['cart']);
    unset($_SESSION['shipping_option']);
    $_SESSION['last_order_id'] = $new_order_id; 
    
    mysqli_close($conn);
    header("Location: order-success.php");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn); 
    mysqli_close($conn);

    $_SESSION['cart_error'] = "Error: " . $e->getMessage();
    header("Location: cart.php");
    exit();
}
?>