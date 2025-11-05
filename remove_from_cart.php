<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Check if an 'id' was sent in the URL
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // If the cart exists and the item is in it, remove the item
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
    }
}

// Send the user back to the cart page
header("Location: cart.php");
exit();
?>