<?php
// update_product.php
include '../db_connect.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $newPrice = $_POST['price'];
    $newQty = (int)$_POST['qty'];
    $status = $_POST['status'];

    if ($newQty <= 0) {
        $newQty = 0;
        $status = "Unavailable";
    }

    $stmt = $conn->prepare("UPDATE item SET ITEM_PRICE=?, ITEM_QTY=?, ITEM_STATUS=? WHERE ITEM_ID=?");
    $stmt->bind_param("diss", $newPrice, $newQty, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }

    $stmt->close();
    $conn->close();
}
?>