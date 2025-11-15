<?php
session_start();

include 'db_connect.php';

if (isset($_SESSION['id']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    
    $user_id = $_SESSION['id'];
    $cart = $_SESSION['cart'];

    if ($conn) {
        $sql_delete = "DELETE FROM saved_cart WHERE user_id = ?";
        if ($stmt_del = mysqli_prepare($conn, $sql_delete)) {
            mysqli_stmt_bind_param($stmt_del, "i", $user_id);
            mysqli_stmt_execute($stmt_del);
            mysqli_stmt_close($stmt_del);
        }

        $sql_insert = "INSERT INTO saved_cart (user_id, item_id, quantity) VALUES (?, ?, ?)";
        if ($stmt_ins = mysqli_prepare($conn, $sql_insert)) {
            
            foreach ($cart as $item_id => $quantity) {
                mysqli_stmt_bind_param($stmt_ins, "isi", $user_id, $item_id, $quantity);
                mysqli_stmt_execute($stmt_ins);
            }
            mysqli_stmt_close($stmt_ins);
        }
        mysqli_close($conn);
    }
}
 
$_SESSION = array();
 
session_destroy();
 
header("Location: index.php");
exit();
?>