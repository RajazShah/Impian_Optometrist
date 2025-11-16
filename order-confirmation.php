<?php
session_start();
include "db_connect.php";

require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["loggedin"]) || !isset($_SESSION["last_order_id"])) {
    // If not logged in OR no order was just placed, go home
    header("Location: index.php");
    exit();
}

$last_order_id = $_SESSION["last_order_id"];
$order = null;
$order_items = [];
$subtotal = 0;

$sql_order = "SELECT o.*, u.email
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.order_id = ?";

if ($stmt_order = mysqli_prepare($conn, $sql_order)) {
    mysqli_stmt_bind_param($stmt_order, "i", $last_order_id);
    mysqli_stmt_execute($stmt_order);
    $result_order = mysqli_stmt_get_result($stmt_order);
    $order = mysqli_fetch_assoc($result_order);
    mysqli_stmt_close($stmt_order);
}

if (!$order) {
    echo "Error: Could not find order.";
    exit();
}

$sql_items = "SELECT oi.quantity, oi.price_per_item, i.ITEM_BRAND, i.item_name
              FROM order_items oi
              JOIN item i ON oi.product_id = i.ITEM_ID
              WHERE oi.order_id = ?";

if ($stmt_items = mysqli_prepare($conn, $sql_items)) {
    mysqli_stmt_bind_param($stmt_items, "i", $last_order_id);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);

    while ($item = mysqli_fetch_assoc($result_items)) {
        $line_total = $item["price_per_item"] * $item["quantity"];
        $subtotal += $line_total;
        $order_items[] = [
            "name" => $item["ITEM_BRAND"] . " " . $item["item_name"],
            "quantity" => $item["quantity"],
            "line_total" => $line_total,
        ];
    }
    mysqli_stmt_close($stmt_items);
}

$shipping = $order["shipping_option"] === "delivery" ? 10.0 : 0;
$total = $order["total_price"];
$customer_email = $order["email"];

unset($_SESSION["last_order_id"]);
unset($_SESSION["shipping_option"]);

$email_body = "<!DOCTYPE html><html><head><style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    .container { width: 90%; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
    .header { background-color: #f4f4f4; padding: 20px; text-align: center; }
    .header h1 { margin: 0; color: #333; }
    .content { padding: 30px; }
    .content p { line-height: 1.6; }
    .items-table, .totals-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .items-table th, .items-table td { border-bottom: 1px solid #eee; padding: 10px; text-align: left; }
    .items-table th { background-color: #f9f9f9; }
    .totals-table td { padding: 5px; }
    .totals-table .total-row { font-weight: bold; font-size: 1.1em; border-top: 2px solid #ddd; }
    .footer { background-color: #f4f4f4; padding: 20px; text-align: center; color: #777; font-size: 0.9em; }
</style></head><body>";

$email_body .= "<div class='container'>
    <div class='header'><h1>Thank You For Your Order!</h1></div>
    <div class='content'>
        <p>Hi there, thank you for your purchase from Impian Optometrist. Your order has been confirmed.</p>
        <p><strong>Order ID:</strong> #{$last_order_id}</p>";

if (
    $order["shipping_option"] === "delivery" &&
    !empty($order["shipping_address"])
) {
    $email_body .=
        "<p><strong>Ship To:</strong><br>" .
        nl2br(htmlspecialchars($order["shipping_address"])) .
        "</p>";
}

$email_body .= "<table class='items-table'>
    <thead><tr><th>Description</th><th>Qty</th><th>Price</th></tr></thead>
    <tbody>";

foreach ($order_items as $item) {
    $email_body .=
        "<tr>
        <td>" .
        htmlspecialchars($item["name"]) .
        "</td>
        <td>" .
        $item["quantity"] .
        "</td>
        <td>RM " .
        number_format($item["line_total"], 2) .
        "</td>
    </tr>";
}

$email_body .=
    "</tbody></table>
    <table class='totals-table' style='width: 100%; margin-top: 20px;'>
        <tr><td style='width: 70%;'>Subtotal:</td><td>RM " .
    number_format($subtotal, 2) .
    "</td></tr>
        <tr><td>Shipping:</td><td>RM " .
    number_format($shipping, 2) .
    "</td></tr>
        <tr class='total-row' style='font-weight: bold; font-size: 1.1em; border-top: 2px solid #ddd;'>
            <td>Total:</td>
            <td>RM " .
    number_format($total, 2) .
    "</td>
        </tr>
    </table>
    </div>
    <div class='footer'>
        <p>IMPIAN OPTOMETRIST<br>6, Jalan Suadamai 1/2, Tun Hussein Onn<br>43200 Cheras, Selangor</p>
    </div>
</div>";
$email_body .= "</body></html>";

$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "2024991451@student.uitm.edu.my";
    $mail->Password = "cncn wigs nvqy yhbr";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom("2024991451@student.uitm.edu.my", "Impian Optometrist");
    $mail->addAddress($customer_email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Your Impian Optometrist Order Confirmation (ID: #{$last_order_id})";
    $mail->Body = $email_body;
    $mail->AltBody =
        "Thank you for your order (ID: #{$last_order_id}). Your total is RM " .
        number_format($total, 2);

    $mail->send();
    $email_success = true;
} catch (Exception $e) {
    $email_error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    $email_success = false;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Impian Optometrist</title>

    <link rel="stylesheet" href="receipt-style.css">
</head>
<body class="page-background">

    <div class="confirmation-container">
        <h1>Thank You For Your Order!</h1>
        <p>Your order (ID: #<?php echo $last_order_id; ?>) has been confirmed.</p>

        <?php if ($email_success): ?>
            <p class="email-success">A confirmation receipt has been sent to <strong><?php echo htmlspecialchars(
                $customer_email,
            ); ?></strong>.</p>
        <?php else: ?>
            <p class="email-error">We've confirmed your order, but could not send a confirmation email. Please print this page for your records.</p>
            <?php endif; ?>
        <div class="receipt-container">
            <div class="receipt-header">
                <h2>IMPIAN OPTOMETRIST</h2>
                <p>6, Jalan Suadamai 1/2, Tun Hussein Onn</p>
                <p>43200 Cheras, Selangor</p>
            </div>

            <div class="receipt-title">
                <p>***********************************</p>
                <p>SALES RECEIPT</p>
                <p>***********************************</p>
                <p>Invoice #: <?php echo $last_order_id; ?></p>
            </div>

            <div class="receipt-body">
                <?php if (
                    $order["shipping_option"] === "delivery" &&
                    !empty($order["shipping_address"])
                ): ?>
            <div class="shipping-address-info">
                <p><strong>SHIP TO:</strong></p>
                <p>
                    <?php echo nl2br(
                        htmlspecialchars($order["shipping_address"]),
                    ); ?>
                </p>
            </div>
            <p>-----------------------------------</p>
            <?php endif; ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(
                                $item["name"],
                            ); ?></td>
                            <td><?php echo $item["quantity"]; ?></td>
                            <td><?php echo number_format(
                                $item["line_total"],
                                2,
                            ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p>-----------------------------------</p>

                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td>RM <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Shipping:</td>
                        <td>RM <?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td>Total:</td>
                        <td>RM <?php echo number_format($total, 2); ?></td>
                    </tr>
                </table>
            </div>

            <div class="receipt-footer">
                <p>***********************************</p>
                <p>Thank You!</p>
            </div>
        </div>

        <button id="print-button" class="btn-print">Print Receipt</button>
        <a href="index.php" class="btn-back">Back to Home</a>
    </div>

    <script>
        document.getElementById('print-button').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
