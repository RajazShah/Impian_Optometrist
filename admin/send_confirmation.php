<?php
// Make sure this path is correct
require "../vendor/autoload.php";
include "../db_connect.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get data from the URL
$order_id = $_GET["order_id"] ?? null;
$customer_email = $_GET["email"] ?? null;

if (!$order_id || !$customer_email) {
    header("Location: orders.php?error=Missing order data");
    exit();
}

// 1. Generate a unique confirmation token
$token = bin2hex(random_bytes(32)); // 64-character token

// 2. Store the token and update order status in the database
try {
    $stmt = $conn->prepare(
        "UPDATE orders SET order_status = 'Pending Confirmation', confirmation_token = ? WHERE order_id = ?",
    );
    $stmt->bind_param("si", $token, $order_id);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    // If this fails, redirect with an error
    header(
        "Location: orders.php?error=Database update failed: " .
            $e->getMessage(),
    );
    exit();
}

// 3. Send the confirmation email
$mail = new PHPMailer(true);

// Build the confirmation link
// **** IMPORTANT: Change 'yourwebsite.com' to your actual domain ****
$confirmation_link =
    "https://yourwebsite.com/admin/confirm_order.php?token=" . $token;

try {
    // --- SERVER SETTINGS ---
    // **** THIS IS THE SECTION YOU MUST EDIT ****
    $mail->isSMTP();
    $mail->Host = "smtp.example.com"; // e.g., 'smtp.gmail.com' or your host
    $mail->SMTPAuth = true;
    $mail->Username = "2024991451@student.uitm.edu.my"; // Your email username
    $mail->Password = "cncn wigs nvqy yhbr"; // Your email password (or Gmail App Password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587; // Use 587 for TLS, 465 for SSL

    // --- RECIPIENTS ---
    $mail->setFrom("no-reply@impianoptometrist.com", "Impian Optometrist");
    $mail->addAddress($customer_email); // Add the customer's email

    // --- CONTENT ---
    $mail->isHTML(true);
    $mail->Subject = "Please Confirm Your Order (" . $order_id . ")";
    $mail->Body =
        "
        <h2>Thank you for your order!</h2>
        <p>Please click the link below to confirm your order:</p>
        <p><a href='" .
        $confirmation_link .
        "'>Confirm My Order</a></p>
        <p>If you did not place this order, please ignore this email.</p>
    ";
    $mail->AltBody =
        "Please click the link to confirm your order: " . $confirmation_link;

    $mail->send();

    // Redirect admin back to orders page with a success message
    header("Location: orders.php?success=Confirmation email sent!");
} catch (Exception $e) {
    // If sending fails, print the error
    echo "<h1>Email Error:</h1>";
    echo "<p>The email could not be sent. The error is:</p>";
    echo "<pre>" . $mail->ErrorInfo . "</pre>";
    die(); // Stop the script
}

$conn->close();
?>
