<?php
session_start();
include 'db_connect.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $user_id = $_SESSION['id'];

    // Check if the new email already exists for *another* user
    $sql_check = "SELECT id FROM users WHERE email = ? AND id != ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            // Email is already taken by someone else
            echo "<script>alert('Error: This email is already in use by another account.'); window.history.back();</script>";
            exit();
        }
        mysqli_stmt_close($stmt_check);
    }

    // Update the user's information
    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?";
    
    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
        mysqli_stmt_bind_param($stmt_update, "ssssi", $first_name, $last_name, $email, $phone_number, $user_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            // Update session variables
            $_SESSION['first_name'] = $first_name;
            $_SESSION['email'] = $email;
            
            // Redirect back to profile page
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