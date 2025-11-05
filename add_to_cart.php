<?php
// Always start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not, redirect to the homepage (which will show the login modal)
    header("Location: index.php");
    exit();
}

// Check if an item_id was sent via POST
if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // If the cart doesn't exist in the session, create it as an empty array
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the item is already in the cart
    if (isset($_SESSION['cart'][$item_id])) {
        // If yes, increment the quantity
        $_SESSION['cart'][$item_id]++;
    } else {
        // If no, add it to the cart with a quantity of 1
        $_SESSION['cart'][$item_id] = 1;
    }

    // Send the user back to the homepage
    // You can also redirect to 'cart.php' if you prefer
    header("Location: index.php");
    exit();
    
} else {
    // If no item_id was sent, just go back
    header("Location: index.php");
    exit();
}
?>