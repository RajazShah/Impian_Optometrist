<?php
session_start();
include 'db_connect.php'; 

// --- 1. SERVER-SIDE VALIDATION ---
$errors = [];
$shipping_option = $_SESSION['shipping_option'] ?? 'delivery';

if ($shipping_option === 'delivery') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');

    // Regex for names/cities/states: Only letters, spaces, hyphens, and apostrophes
    $name_place_pattern = "/^[A-Za-z\s'-]+$/";

    // First Name (3-40 chars, no numbers)
    if (strlen($first_name) < 3 || strlen($first_name) > 40) {
        $errors['first_name'] = "First Name must be between 3 and 40 characters.";
    } elseif (!preg_match($name_place_pattern, $first_name)) {
        $errors['first_name'] = "First Name must only contain letters.";
    }

    // Last Name (3-40 chars, no numbers)
    if (strlen($last_name) < 3 || strlen($last_name) > 40) {
        $errors['last_name'] = "Last Name must be between 3 and 40 characters.";
    } elseif (!preg_match($name_place_pattern, $last_name)) {
        $errors['last_name'] = "Last Name must only contain letters.";
    }

    // Address (20-100 chars - numbers allowed)
    if (strlen($address) < 20 || strlen($address) > 100) {
        $errors['address'] = "Address must be between 20 and 100 characters.";
    }

    // City (4-20 chars, no numbers)
    if (strlen($city) < 4 || strlen($city) > 20) {
        $errors['city'] = "City must be between 4 and 20 characters.";
    } elseif (!preg_match($name_place_pattern, $city)) {
        $errors['city'] = "City must only contain letters.";
    }
    
    // State (3-20 chars, no numbers)
    if (strlen($state) < 3 || strlen($state) > 20) {
        $errors['state'] = "State must be between 3 and 20 characters.";
    } elseif (!preg_match($name_place_pattern, $state)) {
        $errors['state'] = "State must only contain letters.";
    }

    // Postcode (exactly 5 digits)
    if (!preg_match("/^\d{5}$/", $postcode)) {
        $errors['postcode'] = "Postcode must be exactly 5 digits.";
    }

    // REDIRECTION ON ERROR
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Keep existing data
        header("Location: checkout.php");
        exit();
    }
}

// --- Security & Cart Check ---
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// --- Recalculate Total (to be safe) ---
$cart = $_SESSION['cart'];
$item_ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($item_ids), '?'));
$types = str_repeat('s', count($item_ids));

$sql_items = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, ITEM_QTY FROM item WHERE ITEM_ID IN ($placeholders)";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, $types, ...$item_ids);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);

$subtotal = 0;

while ($row = mysqli_fetch_assoc($result_items)) {
    $id = $row['ITEM_ID'];
    $requested_qty = $cart[$id];
    $available_stock = $row['ITEM_QTY'];

    if ($available_stock < $requested_qty) {
        $item_name = $row['ITEM_BRAND'] . " " . $row['item_name'];

        $error_msg = urlencode("Only $available_stock left for $item_name. Please lower quantity.");
        header("Location: cart.php?error=" . $error_msg);
        exit();
    }

    $subtotal += $row['ITEM_PRICE'] * $requested_qty;
}
mysqli_stmt_close($stmt_items);
mysqli_close($conn);

$shipping = ($_SESSION['shipping_option'] === 'delivery') ? 10.00 : 0;
$total = $subtotal + $shipping;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Impian Optometrist</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="checkout.css"> 
    <link rel="stylesheet" href="cart-features.css">
</head>
<body class="page-background">

    <main class="checkout-container">
        <h1>Complete Your Payment</h1>
        
        <div class="checkout-layout">
            
            <form action="order-process.php" method="POST">
                <div class="checkout-details">
                    <h2>1. Scan to Pay</h2>
                    <p>Please scan the QR code below to pay RM <?php echo number_format($total, 2); ?>.</p>
                    
                    <div class="qr-code-container">
                        <img src="images/qr_code.jpg" alt="Scan QR Code to Pay">
                    </div>
                    
                    <h2>2. Confirm Your Payment</h2>
                    <p>After you have paid, click the button below to confirm your order.</p>

                    <button type="submit" class="btn-checkout">
                        I Have Paid, Confirm My Order
                    </button>
                </div>
                
                <?php if ($_SESSION['shipping_option'] === 'delivery'): ?>
                    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name']); ?>">
                    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name']); ?>">
                    <input type="hidden" name="address" value="<?php echo htmlspecialchars($_POST['address']); ?>">
                    <input type="hidden" name="city" value="<?php echo htmlspecialchars($_POST['city']); ?>">
                    <input type="hidden" name="state" value="<?php echo htmlspecialchars($_POST['state']); ?>">
                    <input type="hidden" name="postcode" value="<?php echo htmlspecialchars($_POST['postcode']); ?>">
                <?php endif; ?>
            </form>

            <div class="order-summary-checkout">
                <h2>Your Order</h2>
                <div class="summary-total">
                    <span>Total to Pay</span>
                    <span>RM <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            
        </div>
    </main>
    
</body>
</html>