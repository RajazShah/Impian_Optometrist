<?php
session_start();
include 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Security Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Capture all inputs
    $user_id = $_SESSION['id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $doctor = $_POST['doctor'];
    $reason = $_POST['notes'];

    // --- START: NEW VALIDATION RULE ---
    // Check if any required field is empty
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($appointment_date) || empty($appointment_time) || empty($doctor)) {
        echo "<script>
                alert('Please fill in all required fields (Name, Email, Phone, Date, Time, and Doctor).'); 
                window.history.back();
              </script>";
        exit();
    }
    // --- END: NEW VALIDATION RULE ---

    // 3. Check Availability
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

    // 4. Insert Appointment
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
        // Redirect to the customer appointment dashboard
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