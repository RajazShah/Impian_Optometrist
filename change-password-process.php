<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['id'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Error: New passwords do not match.'); window.history.back();</script>";
        exit();
    }

    $sql_get = "SELECT password FROM users WHERE id = ?";
    if ($stmt_get = mysqli_prepare($conn, $sql_get)) {
        mysqli_stmt_bind_param($stmt_get, "i", $user_id);
        mysqli_stmt_execute($stmt_get);
        $result = mysqli_stmt_get_result($stmt_get);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt_get);
        
        $hashed_password_from_db = $user['password'];

        if (password_verify($current_password, $hashed_password_from_db)) {
            
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $sql_update = "UPDATE users SET password = ? WHERE id = ?";
            if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
                mysqli_stmt_bind_param($stmt_update, "si", $new_password_hash, $user_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                
                echo "<script>alert('Password updated successfully!'); window.location='profile.php';</script>";
                exit();
            } else {
                echo "Error: Could not prepare password update. " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Error: Your current password was incorrect.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "Error: Could not fetch user data. " . mysqli_error($conn);
    }
    mysqli_close($conn);
    
} else {
    header("Location: index.php");
    exit();
}
?>