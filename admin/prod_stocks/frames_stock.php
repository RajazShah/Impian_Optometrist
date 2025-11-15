<?php
include "../../db_connect.php";

$showAlert = false;
$alertMessage = "";
$updatedID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action_type"]) && $_POST["action_type"] == "create") {
        $prefix = "F";

        $stmt_get_last_id = $conn->prepare(
            "SELECT ITEM_ID FROM item WHERE ITEM_ID LIKE ?
             ORDER BY CAST(SUBSTRING(ITEM_ID, 2) AS UNSIGNED) DESC
             LIMIT 1",
        );
        $like_prefix = $prefix . "%";
        $stmt_get_last_id->bind_param("s", $like_prefix);
        $stmt_get_last_id->execute();
        $result = $stmt_get_last_id->get_result();

        $next_number = 1;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row["ITEM_ID"];
            $number_part = intval(substr($last_id, 1));
            $next_number = $number_part + 1;
        }
        $stmt_get_last_id->close();

        $item_id = $prefix . str_pad($next_number, 3, "0", STR_PAD_LEFT);

        $item_name = $_POST["item_name_new"];
        $item_brand = $_POST["item_brand_new"];
        $item_price = $_POST["item_price_new"];
        $item_qty = $_POST["item_qty_new"];
        $item_status = $_POST["item_status_new"];
        $category_id = "CAT001";

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

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $qty = $row["ITEM_QTY"];
        } else {
            $qty = 0; // Default qty if item not found (though it should be)
        }
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

// Get the current search query from the URL (for GET request)
$search_query = isset($_GET["search_query"]) ? $_GET["search_query"] : "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");

// Get URL without query params
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-R">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frames Stock View</title>
    <link rel="stylesheet" href="product_stocks.css">
</head>
<body>

<div class="stock-container">
    <div class="container-header">
        <h1>Frames Stock View</h1>
        <div class="header-buttons">
            <a href="../stock.html" class="btn-back">&larr; Back to Stock View</a>
            <button id="showCreateFormBtn" class="btn-create">+ Create New Product</button>
        </div>
    </div>

    <!-- START: SEARCH FORM -->
    <div class="search-container">
        <form method="GET" action="<?php echo $current_page_url; ?>">
            <label for="search_query">Search Product:</label>
            <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                $search_query,
            ); ?>" placeholder="Enter Product ID or Name">
            <button type="submit">Search</button>
            <a href="<?php echo $current_page_url; ?>">Clear</a>
        </form>
    </div>
    <!-- END: SEARCH FORM -->

    <div id="createFormContainer" class="create-form-container" style="display:none;">
        <h3>Create New Product</h3>
        <form method="POST" action="<?php echo $current_page_url; ?>">
            <input type="hidden" name="action_type" value="create">

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
        // --- START: MODIFIED SELECT QUERY ---

        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT001'";
        $params = [];
        $types = "";

        // Add search conditions if a search query is provided
        if (!empty($search_query)) {
            $sql .= " AND (ITEM_ID LIKE ? OR ITEM_NAME LIKE ?)";
            $search_term = "%" . $search_query . "%";
            array_push($params, $search_term, $search_term);
            $types .= "ss";
        }

        // Add the sorting
        // Sorts by the number part of the ID (e.g., F001, F002...)
        $sql .= " ORDER BY CAST(SUBSTRING(ITEM_ID, 2) AS UNSIGNED) ASC";

        // Prepare and execute the query
        $stmt_select = $conn->prepare($sql);

        if (!empty($params)) {
            $stmt_select->bind_param($types, ...$params);
        }

        $stmt_select->execute();
        $result = $stmt_select->get_result();

        // --- END: MODIFIED SELECT QUERY ---

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
                // Form action URL now includes the search query to stay on the same filtered page
                echo "<form method='POST' action='" .
                    htmlspecialchars(
                        $current_page_url .
                            (!empty($search_query)
                                ? "?search_query=" . urlencode($search_query)
                                : ""),
                    ) .
                    "'>";

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

        $stmt_select->close(); // Close the prepared statement
        $conn->close(); // Close the connection

        if ($showAlert) {
            $jsAlertMessage = addslashes($alertMessage);
            // Reload the page, preserving the search query if it exists
            $reload_url =
                $current_page_url .
                (!empty($search_query)
                    ? "?search_query=" . urlencode($search_query)
                    : "");
            echo "<script>alert('$jsAlertMessage'); window.location.href='$reload_url';</script>";
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
