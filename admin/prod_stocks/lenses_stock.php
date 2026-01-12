<?php
include "../../db_connect.php";

$showAlert = false;
$alertMessage = "";
$updatedID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- CREATE NEW LENS LOGIC ---
    if (isset($_POST["action_type"]) && $_POST["action_type"] == "create") {
        $upload_ok = 1;
        $target_path = "";

        // 1. Handle Image Upload
        if (
            isset($_FILES["item_image_new"]) &&
            $_FILES["item_image_new"]["error"] == 0
        ) {
            $upload_dir = "../../images/";
            $image_name = basename($_FILES["item_image_new"]["name"]);
            $file_extension = strtolower(
                pathinfo($image_name, PATHINFO_EXTENSION),
            );

            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($file_extension, $allowed_extensions)) {
                $alertMessage =
                    "Error: Only JPG, JPEG, PNG, & GIF files are allowed.";
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

        // 2. Auto-Generate ID (Prefix 'L' for Lenses)
        if ($upload_ok) {
            $prefix = "L"; 

            $stmt_get_last_id = $conn->prepare(
                "SELECT ITEM_ID FROM item WHERE ITEM_ID LIKE ? ORDER BY CAST(SUBSTRING(ITEM_ID, 2) AS UNSIGNED) DESC LIMIT 1",
            );
            $like_prefix = $prefix . "%";
            $stmt_get_last_id->bind_param("s", $like_prefix);
            $stmt_get_last_id->execute();
            $result = $stmt_get_last_id->get_result();

            $next_number = 1;
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_id = $row["ITEM_ID"];
                $number_part = intval(substr($last_id, strlen($prefix)));
                $next_number = $number_part + 1;
            }
            $stmt_get_last_id->close();

            $item_id = $prefix . str_pad($next_number, 3, "0", STR_PAD_LEFT);

            // Rename image to match ID
            $new_file_name = $item_id . "." . $file_extension;
            $target_path = $upload_dir . $new_file_name;
            $target_path_database = $new_file_name;

            if (
                !move_uploaded_file(
                    $_FILES["item_image_new"]["tmp_name"],
                    $target_path,
                )
            ) {
                $alertMessage =
                    "Error: There was an error uploading your file.";
                $upload_ok = 0;
            }
        }

        // 3. Insert into Database
        if ($upload_ok) {
            $item_name = $_POST["item_name_new"];
            $item_brand = $_POST["item_brand_new"];
            $item_price = $_POST["item_price_new"];
            $item_qty = $_POST["item_qty_new"];
            $item_status = $_POST["item_status_new"];
            $category_id = "CAT002"; // CAT002 for Lenses

            $stmt = $conn->prepare(
                "INSERT INTO item (ITEM_ID, ITEM_NAME, ITEM_BRAND, ITEM_PRICE, ITEM_QTY, ITEM_STATUS, CATEGORY_ID, ITEM_IMAGE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            );
            $stmt->bind_param(
                "sssdisss",
                $item_id,
                $item_name,
                $item_brand,
                $item_price,
                $item_qty,
                $item_status,
                $category_id,
                $target_path_database,
            );

            if ($stmt->execute()) {
                $alertMessage = "Lens $item_id created successfully!";
            } else {
                $alertMessage = "Error creating product: " . $stmt->error;
            }
            $stmt->close();
        }

        $showAlert = true;
    
    // --- UPDATE EXISTING LENS LOGIC ---
    } elseif (isset($_POST["item_id"])) {
        $id = $_POST["item_id"];
        $newPrice = $_POST["new_price"];
        $newStatus = $_POST["new_status"];
        $action = isset($_POST["action"]) ? $_POST["action"] : "";

        // Get current quantity
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
            $qty = 0;
        }
        $stmt_get_qty->close();

        // Calculate new quantity
        if ($action == "plus") {
            $qty++;
        }
        if ($action == "minus" && $qty > 0) {
            $qty--;
        }

        // Update database
        $stmt_update = $conn->prepare(
            "UPDATE item SET ITEM_PRICE = ?, ITEM_QTY = ?, ITEM_STATUS = ? WHERE ITEM_ID = ?",
        );
        $stmt_update->bind_param("diss", $newPrice, $qty, $newStatus, $id);

        if ($stmt_update->execute()) {
            $alertMessage = "Lens $id updated successfully!";
        } else {
            $alertMessage = "Error updating product: " . $stmt_update->error;
        }
        $stmt_update->close();
        $showAlert = true;
    }
}

$search_query = isset($_GET["search_query"]) ? $_GET["search_query"] : "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lens Stock View</title>
    <link rel="stylesheet" href="product_stocks.css">
</head>
<body>

<div class="stock-container">
    <div class="container-header">
        <h1>Lens Stock View</h1>
        <div class="header-buttons">
            <a href="../stock.html" class="btn-back">&larr; Back to Stock View</a>
            <button id="showCreateFormBtn" class="btn-create">+ Create New Lens</button>
        </div>
    </div>

    <div class="search-container">
        <form method="GET" action="<?php echo $current_page_url; ?>">
            <label for="search_query">Search Lens:</label>
            <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                $search_query,
            ); ?>" placeholder="Enter Lens ID or Name">
            <button type="submit">Search</button>
            <a href="<?php echo $current_page_url; ?>">Clear</a>
        </form>
    </div>

    <div id="createFormContainer" class="create-form-container" style="display:none;">
        <h3>Create New Lens</h3>

        <form method="POST" action="<?php echo $current_page_url; ?>" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="create">

            <div class="form-group">
                <label for="item_name_new">Lens Name/Model:</label>
                <input type="text" id="item_name_new" name="item_name_new" required>
            </div>
            <div class="form-group">
                <label for="item_brand_new">Brand:</label>
                <input type="text" id="item_brand_new" name="item_brand_new" required>
            </div>

            <div class="form-group">
                <label for="item_image_new">Image:</label>
                <input type="file" id="item_image_new" name="item_image_new" accept="image/png, image/jpeg, image/gif" required>
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
            <button type="submit" class="btn-submit-create">Save New Lens</button>
        </form>
    </div>

    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT002'"; // Filter for Lenses
        $params = [];
        $types = "";
        if (!empty($search_query)) {
            $sql .= " AND (ITEM_ID LIKE ? OR item_name LIKE ?)";
            $search_term = "%" . $search_query . "%";
            array_push($params, $search_term, $search_term);
            $types .= "ss";
        }

        // Sort by numeric part of ID (e.g., L1, L2, L10)
        $sql .= " ORDER BY CAST(SUBSTRING(ITEM_ID, 2) AS UNSIGNED) ASC";
        $stmt_select = $conn->prepare($sql);

        if (!empty($params)) {
            $stmt_select->bind_param($types, ...$params);
        }

        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $base_folder = "../../images/";
                $img_src = "";

                // Check DB image path first
                if (!empty($row["ITEM_IMAGE"])) {
                    $img_src = "../../" . $row["ITEM_IMAGE"];
                }

                // Fallback to ID-based naming if DB path is empty or file missing
                if (empty($img_src) || !file_exists($img_src)) {
                    $auto_path_jpg = $base_folder . $row["ITEM_ID"] . ".jpg";
                    $auto_path_png = $base_folder . $row["ITEM_ID"] . ".png";

                    if (file_exists($auto_path_jpg)) {
                        $img_src = $auto_path_jpg;
                    } elseif (file_exists($auto_path_png)) {
                        $img_src = $auto_path_png;
                    }
                }

                echo "<div class='product-card'>";

                if (!empty($img_src) && file_exists($img_src)) {
                    echo "<img src='" .
                        htmlspecialchars($img_src) .
                        "' alt='" .
                        htmlspecialchars($row["item_name"]) .
                        "' class='product-image'>";
                } else {
                    echo "<div class='product-image-placeholder'>No Image</div>";
                }

                echo "<h3>" .
                    $row["ITEM_ID"] .
                    " - " .
                    htmlspecialchars($row["item_name"]) .
                    "</h3>";
                echo "<p class='product-brand'>" .
                    "Brand: " .
                    htmlspecialchars($row["ITEM_BRAND"]) .
                    "</p>";

                // Update Form
                echo "<form method='POST' action='" .
                    htmlspecialchars(
                        $current_page_url .
                            (!empty($search_query)
                                ? "?search_query=" . urlencode($search_query)
                                : ""),
                    ) .
                    "'>";

                echo "<p>Price: RM <input type='number' name='new_price' value='{$row["ITEM_PRICE"]}' step='0.01' required></p>";

                echo "<p>Quantity: <span>{$row["ITEM_QTY"]}</span>
                        <span class='quantity-controls'>
                            <button type='submit' name='action' value='minus'>-</button>
                            <button type='submit' name='action' value='plus'>+</button>
                        </span>
                      </p>";

                $status_class =
                    $row["ITEM_STATUS"] == "Available"
                        ? "status-available"
                        : "status-unavailable";

                echo "<p>Status:
                        <select name='new_status' class='status-select " .
                    $status_class .
                    "' onchange='this.className=\"status-select \" + (this.value === \"Available\" ? \"status-available\" : \"status-unavailable\")'>
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
            echo "<p>No lenses found.</p>";
        }

        $stmt_select->close();
        $conn->close();

        if ($showAlert) {
            $jsAlertMessage = addslashes($alertMessage);
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
            this.textContent = '+ Create New Lens';
        }
    });
</script>

</body>
</html>