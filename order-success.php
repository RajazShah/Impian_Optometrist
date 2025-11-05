<?php
session_start();

// Get the order ID we saved in the session
$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;

// Unset the variable so it doesn't show up again
unset($_SESSION['last_order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed! - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    
    <style>
        .page-background { background-color: #f7faff; }
        .success-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .success-container h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 32px;
            font-weight: normal;
            color: #16a34a; /* Green */
            margin-bottom: 20px;
        }
        .success-container p {
            font-size: 18px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-home {
            display: inline-block;
            background-color: #000;
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body class="page-background">

    <header class="main-header">
        </header>

    <main class="success-container">
        <h1>Thank You For Your Order!</h1>
        <?php if ($order_id): ?>
            <p>Your order has been confirmed. Your Order ID is: <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>
            <p>We've received your order and will begin processing it shortly.</p>
        <?php else: ?>
            <p>Your order has been confirmed! We've received it and will begin processing it shortly.</p>
        <?php endif; ?>
        
        <a href="index.php" class="btn-home">← Back to Homepage</a>
    </main>

    <script src="script.js"></script> 
</body>
</html>