<?php
    include '../../db_connect.php';

    $id = $_POST['id'];
    $newPrice = $_POST['price'];
    $qtyChange = (int)$_POST['qtyChange'];
    $status = $_POST['status'];

    // Get current quantity
    $sql = "SELECT ITEM_QTY FROM item WHERE ITEM_ID='$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $currentQty = $row['ITEM_QTY'];

    // Calculate new quantity
    $newQty = $currentQty + $qtyChange;
    if ($newQty < 0) $newQty = 0;
    if ($newQty == 0) $status = "Unavailable"; // auto-set when out of stock

    // Update database
    $update = "UPDATE item 
                SET ITEM_PRICE='$newPrice', ITEM_QTY='$newQty', ITEM_STATUS='$status' 
                WHERE ITEM_ID='$id'";
    $conn->query($update);

    // Return updated info as JSON
    echo json_encode([
        "newPrice" => $newPrice,
        "newQty" => $newQty,
        "newStatus" => $status
    ]);

    $conn->close();
?>
