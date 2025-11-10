<?php
include "../../db_connect.php";

$showAlert = false;
$alertMessage = "";
$updatedID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if this is a CREATE action
    if (isset($_POST["action_type"]) && $_POST["action_type"] == "create") {
        // --- HANDLE CREATE ---
        $item_id = $_POST["item_id_new"];
        $item_price = $_POST["item_price_new"];
        $item_qty = $_POST["item_qty_new"];
        $item_status = $_POST["item_status_new"];
        $category_id = "CAT005";

        $sql = "INSERT INTO item (ITEM_ID,ITEM_PRICE, ITEM_QTY, ITEM_STATUS, CATEGORY_ID)
                VALUES ('$item_id', '$item_price', '$item_qty', '$item_status', '$category_id')";

        if ($conn->query($sql)) {
            $alertMessage = "Product $item_id created successfully!";
        } else {
            $alertMessage = "Error creating product: " . $conn->error;
        }
        $showAlert = true;
    }
    // Check if it's an UPDATE action
    elseif (isset($_POST["item_id"])) {
        // --- HANDLE UPDATE ---
        $id = $_POST["item_id"];
        $newPrice = $_POST["new_price"];
        $newStatus = $_POST["new_status"];
        $action = isset($_POST["action"]) ? $_POST["action"] : "";

        // Get current quantity
        $res = $conn->query("SELECT ITEM_QTY FROM item WHERE ITEM_ID='$id'");
        $row = $res->fetch_assoc();
        $qty = $row["ITEM_QTY"];

        // Adjust quantity only if + or -
        if ($action == "plus") {
            $qty++;
        }
        if ($action == "minus" && $qty > 0) {
            $qty--;
        }

        // Update product
        $sql = "UPDATE item
                SET ITEM_PRICE='$newPrice', ITEM_QTY='$qty', ITEM_STATUS='$newStatus'
                WHERE ITEM_ID='$id'";
        $conn->query($sql);

        $alertMessage = "Product $id updated successfully!";
        $showAlert = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Lenses Stock View</title>
    <link rel="stylesheet" href="product_stocks.css">
</head>
<body>

<div class="stock-container">
    <div class="container-header">
        <h1>Contact Lenses Stock View</h1>
        <div class="header-buttons">
            <a href="../stock.html" class="btn-back">&larr; Back to Stock View</a>
            <button id="showCreateFormBtn" class="btn-create">+ Create New Product</button>
        </div>
    </div>

    <div id="createFormContainer" class="create-form-container" style="display:none;">
        <h3>Create New Product</h3>
        <form method="POST">
            <input type="hidden" name="action_type" value="create">
            <div class="form-group">
                <label for="item_id_new">Product ID:</label>
                <input type="text" id="item_id_new" name="item_id_new" required>
            </div>
            <div class="form-group">
                <label for="item_price_new">Price (RM):</label>
                <input type="number" id="item_price_new" name="item_price_new" step="0.01" value="0.00" required>
            </div>
            <div class="form-group">
                <label for="item_qty_new">Quantity:</label>
                <input type="number" id="item_qty_new" name="item_qty_new" value="0" required>
            </div>
            <div class="form-group">
                <label for="item_status_new">Status:</label>
                <select id="item_status_new" name="item_status_new">
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <button type="submit" class="btn-submit-create">Save New Product</button>
        </form>
    </div>
    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT005'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<h3>" . $row["ITEM_ID"] . "</h3>";
                echo "<form method='POST'>";

                echo "<p>Price: RM
                        <input type='number' name='new_price' value='{$row["ITEM_PRICE"]}' required>
                      </p>";

                echo "<p>Quantity: {$row["ITEM_QTY"]}
                        <button type='submit' name='action' value='plus'>+</button>
                        <button type='submit' name='action' value='minus'>-</button>
                      </p>";

                echo "<p>Status:
                        <select name='new_status'>
                            <option value='Available' " .
                    ($row["ITEM_STATUS"] == "Available" ? "selected" : "") .
                    ">Available</option>
                            <option value='Unavailable' " .
                    ($row["ITEM_STATUS"] == "Unavailable" ? "selected" : "") .
                    ">Unavailable</option>
                        </select>
                      </p>";

                echo "<input type='hidden' name='item_id' value='{$row["ITEM_ID"]}'>";

                // New update button
                echo "<button type='submit' name='action' value='update' class='btn-update'>Update</button>";

                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }

        $conn->close();

        // Alert after update or create
        if ($showAlert) {
            echo "<script>alert('$alertMessage'); window.location.href=window.location.href;</script>";
        }
        ?>
    </div>
</div>

<script>
    document.getElementById('showCreateFormBtn').addEventListener('click', function() {
        var form = document.getElementById('createFormContainer');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            this.textContent = 'Cancel';
        } else {
            form.style.display = 'none';
            this.textContent = '+ Create New Product';
        }
    });
</script>

</body>
</html>
