<?php
// Include your database connection
include 'db_connect.php';

// Show all errors (for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number']; 
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(strlen($first_name) > 7 || strlen($first_name) < 20){
        echo "<script>alert('First name must be between 7 and 20 characters.'); window.history.back();</script>";
        exit();
    }

    if(strlen($last_name) > 7 || strlen($last_name) < 20){
        echo "<script>alert('Last name must be between 7 and 20 characters.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit();
    }

    $phone_regex = "/^(\+|0)[0-9\s-]{9,}$/";
    if (!empty($phone_number) && !preg_match($phone_regex, $phone_number)) {
        echo "<script>alert('Please enter a valid phone number (e.g., +60 12-345 6789 or 012-345 6789).'); window.history.back();</script>";
        exit();
    }

    if (strlen($password) < 5 || strlen($password) > 15) {
        echo "<script>alert('Password must be between 5 and 15 characters.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "<script>alert('This email is already registered. Please login.'); window.history.back();</script>";
            mysqli_stmt_close($stmt);
            exit();
        }
        mysqli_stmt_close($stmt);
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, email, phone_number, gender, password)
            VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        
        mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $email, $phone_number, $gender, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            // Success!
            echo "<script>alert('Registration Successful! Please Login.'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>