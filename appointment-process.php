<?php
session_start();
include 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $doctor = $_POST['doctor'];
    $reason = $_POST['notes'];
    


    $sql_check_customer = "SELECT appointment_id FROM customer_appointments WHERE appointment_date = ? AND appointment_time = ? AND doctor = ?";
    if ($stmt_check_customer = mysqli_prepare($conn, $sql_check_customer)) {
        mysqli_stmt_bind_param($stmt_check_customer, "sss", $appointment_date, $appointment_time, $doctor);
        mysqli_stmt_execute($stmt_check_customer);
        mysqli_stmt_store_result($stmt_check_customer);
        if (mysqli_stmt_num_rows($stmt_check_customer) > 0) {
            mysqli_stmt_close($stmt_check_customer);
            mysqli_close($conn);
            echo "<script>alert('Sorry, that time slot with the selected doctor is already booked. Please choose another time or doctor.'); window.history.back();</script>";
            exit();
        }
        mysqli_stmt_close($stmt_check_customer);
    } else {
        mysqli_close($conn);
        die("Error checking customer appointments: " . mysqli_error($conn));
    }


    $sql_insert_customer = "INSERT INTO customer_appointments (user_id, appointment_date, appointment_time, doctor, reason) VALUES (?, ?, ?, ?, ?)";
    $success_customer = false;
    if ($stmt_insert_customer = mysqli_prepare($conn, $sql_insert_customer)) {
        mysqli_stmt_bind_param($stmt_insert_customer, "issss", $user_id, $appointment_date, $appointment_time, $doctor, $reason);
            if (mysqli_stmt_execute($stmt_insert_customer)) {
                $success_customer = true;
            } else {
                $error_customer = mysqli_stmt_error($stmt_insert_customer);
            }
        mysqli_stmt_close($stmt_insert_customer);
        } else {
            $error_customer = mysqli_error($conn);
        }

    mysqli_close($conn);

    if ($success_customer) { 
        header("Location: customer-appointment.php");
        exit();
    } else {
        $error_message = "There was an error saving the appointment.";
        if (isset($error_customer)) {
            $error_message .= " Customer error: " . $error_customer;
        }
        die($error_message);
    }

} else {
    header("Location: index.php");
    exit();
}
?>