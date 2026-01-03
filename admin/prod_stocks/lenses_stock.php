<?php
// FILE: lenses_stock.php
include "../../db_connect.php"; // Adjust path if needed

$showAlert = false;
$alertMessage = "";
$updatedID = "";

// --- 1. HANDLE FORM SUBMISSION (Create New Lense) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action_type"]) && $_POST["action_type"] == "create") {
        $upload_ok = 1;
        $target_path = "";

        // Handle Image Upload
        if (isset($_FILES["item_image_new"]) && $_FILES["item_image_new"]["error"] == 0) {
            $upload_dir = "../../images/"; // Make sure this path is correct
            $image_name = basename($_FILES["item_image_new"]["name"]);
            $file_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($file_extension, $allowed_extensions)) {
                $alertMessage = "Error: Only JPG, JPEG, PNG, & GIF files are allowed.";
                $upload_ok = 0;
            }

            if ($upload_ok && $_FILES["item_image_new"]["size"] > 5000000) {
                $alertMessage = "Error: Your file is too large (Max 5MB).";
                $upload_ok = 0;
            }
        } else {
            $alertMessage = "Error: An image file is required.";
            $upload_ok = 0;
        }

        if ($upload_ok) {
            $target_path = $upload_dir . $image_name;
            if (move_uploaded_file($_FILES["item_image_new"]["tmp_name"], $target_path)) {
                // Prepare Database Insert
                $id = $_POST["item_id_new"];
                $brand = $_POST["item_brand_new"];
                $name = $_POST["item_name_new"];
                $price = $_POST["item_price_new"];
                $qty = $_POST["item_qty_new"];
                // IMPORTANT: CAT002 is for Lenses
                $category = "CAT002"; 
                // You can set a default staff NRIC or get it from session if logged in
                $staff = "940715102384"; 

                $sql = "INSERT INTO item (ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, ITEM_QTY, ITEM_IMAGE, CATEGORY_ID, STAFF_NRIC, ITEM_STATUS) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssssisss", $id, $brand, $name, $price, $qty, $image_name, $category, $staff);
                    if ($stmt->execute()) {
                        $showAlert = true;
                        $alertMessage = "New Lens created successfully!";
                    } else {
                        $showAlert = true;
                        $alertMessage = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $showAlert = true;
                    $alertMessage = "Database error: " . $conn->error;
                }
            } else {
                $showAlert = true;
                $alertMessage = "Error: Failed to upload image.";
            }
        } else {
            $showAlert = true;
        }
    }
}

// --- 2. PREPARE SEARCH QUERY ---
$search_query = "";
if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
}

$current_page_url = strtok($_SERVER["REQUEST_URI"], '?');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lenses Stock</title>
    <link rel="stylesheet" href="product_stocks.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="stock-container">
    <div class="container-header">
        <h1>Lenses Stock (CAT002)</h1>
        
        <form method="GET" action="" class="search-form">
            <input type="text" name="search_query" placeholder="Search ID, Brand or Name" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
            <?php if (!empty($search_query)): ?>
                <a href="<?php echo $current_page_url; ?>" class="clear-search">Clear</a>
            <?php endif; ?>
        </form>

        <button id="showCreateFormBtn" class="btn-create">+ Create New Lens</button>
    </div>

    <div id="createFormContainer" style="display: none; background: #f9f9f9; padding: 20px; border-bottom: 1px solid #eee;">
        <h3>Add New Lens</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="create">
            <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="item_id_new" placeholder="Item ID (e.g. L005)" required>
                <input type="text" name="item_brand_new" placeholder="Brand (e.g. Hoya)" required>
                <input type="text" name="item_name_new" placeholder="Model/Name" required>
            </div>
            <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="number" step="0.01" name="item_price_new" placeholder="Price (RM)" required>
                <input type="number" name="item_qty_new" placeholder="Quantity" required>
                <input type="file" name="item_image_new" accept="image/*" required>
            </div>
            <button type="submit" class="btn-update">Save New Lens</button>
        </form>
    </div>

    <div class="products-grid">
        <?php
        // --- 3. DISPLAY LENSES (CAT002) ---
        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT002'";
        
        if (!empty($search_query)) {
            $search_term = "%" . $conn->real_escape_string($search_query) . "%";
            $sql .= " AND (ITEM_ID LIKE '$search_term' OR ITEM_BRAND LIKE '$search_term' OR item_name LIKE '$search_term')";
        }
        
        $sql .= " ORDER BY ITEM_ID ASC";
        
        $stmt_select = $conn->prepare($sql);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Use a fallback image if database image is missing
                $imgSrc = !empty($row["item_image"]) ? "../../images/" . $row["item_image"] : "../../images/default.jpg";
                
                echo "<div class='product-card' id='card-{$row["ITEM_ID"]}'>";
                echo "<form id='form-{$row["ITEM_ID"]}' onsubmit='return updateProduct(event, \"{$row["ITEM_ID"]}\")'>";
                
                echo "<img src='$imgSrc' alt='{$row["item_name"]}'>";
                
                echo "<h3>{$row["ITEM_BRAND"]} {$row["item_name"]}</h3>";
                echo "<p class='item-id'>ID: {$row["ITEM_ID"]}</p>";

                echo "<p class='editable-field'>
                        <label>Price (RM):</label>
                        <input type='number' step='0.01' name='price' value='{$row["ITEM_PRICE"]}'>
                      </p>";

                echo "<p class='editable-field'>
                        <label>Stock:</label>
                        <div class='quantity-controls'>
                            <button type='button' onclick='adjustQty(\"{$row["ITEM_ID"]}\", -1)'>-</button>
                            <input type='number' id='qty-{$row["ITEM_ID"]}' value='{$row["ITEM_QTY"]}' readonly>
                            <button type='button' onclick='adjustQty(\"{$row["ITEM_ID"]}\", 1)'>+</button>
                        </div>
                        <input type='hidden' name='qtyChange' id='qtyChange-{$row["ITEM_ID"]}' value='0'>
                      </p>";

                echo "<p class='editable-field'>
                        <label>Status:</label>
                        <select name='status'>
                            <option value='Available' " . ($row["ITEM_STATUS"] == "Available" ? "selected" : "") . ">Available</option>
                            <option value='Unavailable' " . ($row["ITEM_STATUS"] == "Unavailable" ? "selected" : "") . ">Unavailable</option>
                        </select>
                      </p>";

                echo "<input type='hidden' name='item_id' value='{$row["ITEM_ID"]}'>";
                echo "<button type='submit' class='btn-update'>Update</button>";

                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p style='padding: 20px;'>No lenses found in database (CAT002).</p>";
        }

        $stmt_select->close();
        $conn->close();

        // Display Alert if set
        if ($showAlert) {
            $jsAlertMessage = addslashes($alertMessage);
            $reload_url = $current_page_url . (!empty($search_query) ? "?search_query=" . urlencode($search_query) : "");
            echo "<script>alert('$jsAlertMessage'); window.location.href='$reload_url';</script>";
        }
        ?>
    </div>
</div>

<script>
    // Toggle Create Form
    document.getElementById('showCreateFormBtn').addEventListener('click', function() {
        var form = document.getElementById('createFormContainer');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            this.textContent = 'Cancel';
        } else {
            form.style.display = 'none';
            this.textContent = '+ Create New Lens';
        }
    });

    // Helper for Quantity Buttons
    function adjustQty(id, change) {
        let qtyInput = document.getElementById('qty-' + id);
        let changeInput = document.getElementById('qtyChange-' + id);
        
        let currentQty = parseInt(qtyInput.value);
        let currentChange = parseInt(changeInput.value);
        
        let newQty = currentQty + change;
        if (newQty < 0) newQty = 0; // Prevent negative stock visual

        qtyInput.value = newQty;
        changeInput.value = currentChange + change; 
    }

    // AJAX Update Function
    function updateProduct(event, id) {
        event.preventDefault(); // Stop normal form submission

        let form = document.getElementById('form-' + id);
        let formData = new FormData(form);
        
        // We manually add the ID because it might not be picked up if disabled
        formData.append('id', id);

        $.ajax({
            url: 'update_product.php', // Uses your existing generic update file
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                alert('Product updated successfully!');
                // Reset the "change" counter to 0 so subsequent clicks calculate correctly
                document.getElementById('qtyChange-' + id).value = 0;
            },
            error: function(xhr, status, error) {
                alert('Error updating product: ' + error);
            }
        });
        return false;
    }
</script>

</body>
</html>