<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frmes Stock View</title>
    <link rel="stylesheet" href="product_stocks.css">
</head>
<body>  

<div class="stock-container">
    <div class="container-header">
        <h1>Frames Stock View</h1>
        <a href="../stock.html" class="btn-back">&larr; Back to Stock View</a>
    </div>

    <div class="product-grid">
        <?php
        include '../db_connect.php';

        $showAlert = false;
        $updatedID = "";

        // Handle updates if submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['item_id'];
            $brand = $_POST['item_brand'];
            $name = $_POST['item_name'];
            $newPrice = $_POST['new_price'];
            $newStatus = $_POST['new_status'];
            $action = isset($_POST['action']) ? $_POST['action'] : '';

            // Get current quantity
            $res = $conn->query("SELECT ITEM_QTY FROM item WHERE ITEM_ID='$id'");
            $row = $res->fetch_assoc();
            $qty = $row['ITEM_QTY'];

            // Adjust quantity only if + or -
            if ($action == 'plus') $qty++;
            if ($action == 'minus' && $qty > 0) $qty--;

            // Update product
            $sql = "UPDATE item 
                    SET ITEM_PRICE='$newPrice', ITEM_QTY='$qty', ITEM_STATUS='$newStatus' 
                    WHERE ITEM_ID='$id'";
            $conn->query($sql);

            $showAlert = true;
            $updatedID = $id;
        }

        // Fetch products
        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT001'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<h3>" . $row['ITEM_ID'] . "</h3>";
                echo "<h4>" . $row['ITEM_BRAND'] . "</h4>";
                echo "<h4>" . $row['item_name'] . "</h4>";
                echo "<form method='POST'>";
                echo "<p>Price: RM 
                        <input type='number' name='new_price' value='{$row['ITEM_PRICE']}' required 
                        onchange='this.form.submit()'>
                      </p>";
                echo "<p>Quantity: {$row['ITEM_QTY']} 
                        <button type='submit' name='action' value='plus'>+</button>
                        <button type='submit' name='action' value='minus'>-</button>
                      </p>";
                echo "<p>Status: 
                        <select name='new_status' onchange='this.form.submit()'>
                            <option value='Available' ".($row['ITEM_STATUS']=='Available'?'selected':'').">Available</option>
                            <option value='Unavailable' ".($row['ITEM_STATUS']=='Unavailable'?'selected':'').">Unavailable</option>
                        </select>
                      </p>";
                echo "<input type='hidden' name='item_id' value='{$row['ITEM_ID']}'>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }

        $conn->close();

        // Alert after update
        if ($showAlert) {
            echo "<script>alert('Product $updatedID updated successfully!'); window.location.href=window.location.href;</script>";
        }
        ?>
    </div>
</div>

</body>
</html>
