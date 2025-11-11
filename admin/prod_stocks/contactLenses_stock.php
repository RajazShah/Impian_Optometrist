<?php
include "../../db_connect.php";

$showAlert = false;
$alertMessage = "";
$updatedID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action_type"]) && $_POST["action_type"] == "create") {
        $item_id = $_POST["item_id_new"];
        $item_name = $_POST["item_name_new"];
        $item_brand = $_POST["item_brand_new"];
        $item_price = $_POST["item_price_new"];
        $item_qty = $_POST["item_qty_new"];
        $item_status = $_POST["item_status_new"];
        $category_id = "CAT005";

        $checkStmt = $conn->prepare(
            "SELECT ITEM_ID FROM item WHERE ITEM_ID = ?",
        );
        $checkStmt->bind_param("s", $item_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $alertMessage = "Error: Product ID '$item_id' already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO item (ITEM_ID, ITEM_NAME, ITEM_BRAND, ITEM_PRICE, ITEM_QTY, ITEM_STATUS, CATEGORY_ID)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssdiss",
                $item_id,
                $item_name,
                $item_brand,
                $item_price,
                $item_qty,
                $item_status,
                $category_id,
            );

            if ($stmt->execute()) {
                $alertMessage = "Product $item_id created successfully!";
            } else {
                $alertMessage = "Error creating product: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
        $showAlert = true;
    } elseif (isset($_POST["item_id"])) {
        $id = $_POST["item_id"];
        $newPrice = $_POST["new_price"];
        $newStatus = $_POST["new_status"];
        $action = isset($_POST["action"]) ? $_POST["action"] : "";

        $stmt_get_qty = $conn->prepare(
            "SELECT ITEM_QTY FROM item WHERE ITEM_ID = ?",
        );
        $stmt_get_qty->bind_param("s", $id);
        $stmt_get_qty->execute();
        $res = $stmt_get_qty->get_result();
        $row = $res->fetch_assoc();
        $qty = $row["ITEM_QTY"];
        $stmt_get_qty->close();

        if ($action == "plus") {
            $qty++;
        }
        if ($action == "minus" && $qty > 0) {
            $qty--;
        }

        $stmt_update = $conn->prepare("UPDATE item
                                       SET ITEM_PRICE = ?, ITEM_QTY = ?, ITEM_STATUS = ?
                                       WHERE ITEM_ID = ?");
        $stmt_update->bind_param("diss", $newPrice, $qty, $newStatus, $id);

        if ($stmt_update->execute()) {
            $alertMessage = "Product $id updated successfully!";
        } else {
            $alertMessage = "Error updating product: " . $stmt_update->error;
        }
        $stmt_update->close();
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
                <label for="item_name_new">Product Name:</label>
                <input type="text" id="item_name_new" name="item_name_new" required>
            </div>
            <div class="form-group">
                <label for="item_brand_new">Product Brand:</label>
                <input type="text" id="item_brand_new" name="item_brand_new" required>
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
                echo "<h3>" .
                    $row["ITEM_ID"] .
                    " - " .
                    htmlspecialchars($row["item_name"]) .
                    "</h3>";
                echo "<p class='product-brand'>" .
                    "Brand: " .
                    htmlspecialchars($row["ITEM_BRAND"]) .
                    "</p>";

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

                echo "<button type='submit' name='action' value='update' class='btn-update'>Update</button>";

                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }

        $conn->close();

        if ($showAlert) {
            $jsAlertMessage = addslashes($alertMessage);
            echo "<script>alert('$jsAlertMessage'); window.location.href=window.location.href.split('?')[0];</script>";
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
