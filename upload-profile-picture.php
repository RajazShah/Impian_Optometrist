<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: index.php"); // Not logged in
    exit();
}

$user_id = $_SESSION['id'];

// Check if a file was actually uploaded
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
    
    $file = $_FILES['profilePicture'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Get the file extension (e.g., "jpg")
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // List of allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB limit
                
                // Create a new, unique file name to prevent overwrites
                $newFileName = "profile_" . $user_id . "." . $fileExt;
                
                // Set the destination path
                $fileDestination = 'uploads/profiles/' . $newFileName;
                
                // Move the file from its temporary location to the permanent one
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    
                    // --- Database Update ---
                    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
                    
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "si", $newFileName, $user_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                    
                    // Success! Go back to the profile page
                    header("Location: profile.php?upload=success");
                    exit();

                } else {
                    header("Location: profile.php?error=Failed to move uploaded file.");
                    exit();
                }
            } else {
                header("Location: profile.php?error=File is too large (Max 5MB).");
                exit();
            }
        } else {
            header("Location: profile.php?error=There was an error uploading your file.");
            exit();
        }
    } else {
        header("Location: profile.php?error=You cannot upload files of this type.");
        exit();
    }
} else {
    // This is the error you saw in your screenshot
    header("Location: profile.php?error=No file was uploaded or an error occurred.");
    exit();
}

mysqli_close($conn);
?>