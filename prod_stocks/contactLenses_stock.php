<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Lenses Stock View</title>
    <link rel="stylesheet" href="product_stocks.css">
    <style>
        .qty-control { display: inline-flex; align-items: center; gap: 5px; }
        .qty-btn { cursor: pointer; padding: 5px 10px; background: #eee; border: 1px solid #ccc; }
        .update-btn { margin-top: 10px; width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px;}
        .update-btn:hover { background: #0056b3; }
    </style>
</head>
<body>  

<div class="stock-container">
    <div class="container-header">
        <h1>Contact Lenses Stock View</h1>
        <a href="../stock.html" class="btn-back">&larr; Back to Stock View</a>
    </div>

    <div class="product-grid">
        <?php
        include '../db_connect.php';

        $sql = "SELECT * FROM item WHERE CATEGORY_ID = 'CAT005'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['ITEM_ID'];
                ?>
                <div class="product-card" id="card-<?php echo $id; ?>">
                    <h3><?php echo $id; ?></h3>
                    
                    <?php if(!empty($row['item_image'])): ?>
                        <img src="../../images/<?php echo $row['item_image']; ?>" alt="Item Image" style="width:100px; height:auto; display:block; margin:0 auto;">
                    <?php endif; ?>

                    <h4><?php echo htmlspecialchars($row['ITEM_BRAND']); ?></h4>
                    <h4><?php echo htmlspecialchars($row['item_name']); ?></h4>

                    <div class="form-group">
                        <label>Price: RM</label>
                        <input type="number" id="price-<?php echo $id; ?>" value="<?php echo $row['ITEM_PRICE']; ?>" step="0.01">
                    </div>

                    <div class="form-group">
                        <label>Quantity:</label>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" onclick="changeQty('<?php echo $id; ?>', -1)">-</button>
                            <input type="number" id="qty-<?php echo $id; ?>" value="<?php echo $row['ITEM_QTY']; ?>" readonly style="width: 50px; text-align: center;">
                            <button type="button" class="qty-btn" onclick="changeQty('<?php echo $id; ?>', 1)">+</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status:</label>
                        <select id="status-<?php echo $id; ?>">
                            <option value="Available" <?php if($row['ITEM_STATUS']=='Available') echo 'selected'; ?>>Available</option>
                            <option value="Unavailable" <?php if($row['ITEM_STATUS']=='Unavailable') echo 'selected'; ?>>Unavailable</option>
                        </select>
                    </div>

                    <button type="button" class="update-btn" onclick="updateProduct('<?php echo $id; ?>')">Update</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found.</p>";
        }
        $conn->close();
        ?>
    </div>
</div>

<script>
    function changeQty(id, change) {
        const qtyInput = document.getElementById('qty-' + id);
        let currentQty = parseInt(qtyInput.value);
        let newQty = currentQty + change;
        
        if (newQty < 0) newQty = 0; 
        qtyInput.value = newQty;
    }

    function updateProduct(id) {
        const price = document.getElementById('price-' + id).value;
        const qty = document.getElementById('qty-' + id).value;
        const status = document.getElementById('status-' + id).value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('price', price);
        formData.append('qty', qty);
        formData.append('status', status);

        fetch('update_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product ' + id + ' updated successfully!');
            } else {
                alert('Error updating product: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
</script>

</body>
</html>