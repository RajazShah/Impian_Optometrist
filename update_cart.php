<?php
session_start();

// Security check: Make sure user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Check if the item_id and quantity were sent
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    
    $item_id = $_POST['item_id'];
    $new_quantity = (int)$_POST['quantity']; // Convert quantity to a number

    // Validate the quantity (must be 1 or more)
    if ($new_quantity < 1) {
        $new_quantity = 1;
    }

    // Check if the cart exists and if the item is in the cart
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$item_id])) {
        
        // Update the quantity for that item in the session
        $_SESSION['cart'][$item_id] = $new_quantity;
    }
}

// Send the user back to the cart page to see the changes
header("Location: cart.php");
exit();
?>