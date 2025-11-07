<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: customer-appointment.php");
    exit();
}

$appointment_id = $_GET['id'];
$user_id = $_SESSION['id'];


$sql = "UPDATE customer_appointments 
        SET status = 'Cancelled' 
        WHERE appointment_id = ? 
          AND user_id = ? 
          AND status = 'Upcoming'";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $user_id);
    
    mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

header("Location: customer-appointment.php");
exit();

?>