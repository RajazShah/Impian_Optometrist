<?php
session_start();

$response = [
    'success' => false,
    'new_cart_count' => 0
];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode($response);
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

    $response['success'] = true;
    $response['new_cart_count'] = array_sum($_SESSION['cart']);
    
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>