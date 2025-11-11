<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $user_id = $_SESSION['id'];


    if (strlen($first_name) < 7 || strlen($first_name) > 40 || strlen($last_name) < 7 || strlen($last_name) > 40) {
        echo "<script>alert('First and Last Name must each be between 7 and 40 characters.'); window.history.back();</script>";
        exit();
    }
    

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit();
    }

    $phone_regex = "/^(\+|0)[0-9\s-]{9,}$/";
    if (!empty($phone_number) && !preg_match($phone_regex, $phone_number)) {
        echo "<script>alert('Please enter a valid phone number (e.g., +60 10-840 6912 or 010-840 6912).'); window.history.back();</script>";
        exit();
    }
    

    $sql_check = "SELECT id FROM users WHERE email = ? AND id != ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            echo "<script>alert('Error: This email is already in use by another account.'); window.history.back();</script>";
            exit();
        }
        mysqli_stmt_close($stmt_check);
    }

    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?";
    
    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
        mysqli_stmt_bind_param($stmt_update, "ssssi", $first_name, $last_name, $email, $phone_number, $user_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['first_name'] = $first_name;
            $_SESSION['email'] = $email;
            
            header("Location: profile.php");
            exit();
        } else {
            echo "Error: Could not update profile. " . mysqli_stmt_error($stmt_update);
        }
        mysqli_stmt_close($stmt_update);
    } else {
        echo "Error: Could not prepare profile update. " . mysqli_error($conn);
    }
    mysqli_close($conn);
    
} else {
    header("Location: index.php");
    exit();
}
?>