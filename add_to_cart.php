<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]++;
    } else {
        $_SESSION['cart'][$item_id] = 1;
    }

    header("Location: index.php");
    exit();
    
} else {
    header("Location: index.php");
    exit();
}
?>