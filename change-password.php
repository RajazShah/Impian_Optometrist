<?php
session_start();

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="book-appointment-style.css"> <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="register.css">
</head>
<body class="page-background">

    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
            <input type="search" name="search_query" placeholder="Search items..." class="search-box">
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php#frames-section">FRAMES</a></li>
                <li><a href="index.php#lenses-section">LENSES</a></li>
                <li><a href="index.php#clip-section">CLIP-ON</a></li>
                <li><a href="index.php#contact-section">CONTACT LENSE</a></li>
            </ul>
        </nav>
        <div class="user-actions">
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            ?>
            <div class="user-icons-box">
                <a href="customer-appointment.php" title="Appointment"><img src="images/appointment-icon.png" alt="Appointment"></a>
                <a href="cart.php" title="Cart"><img src="images/bag-icon.png" alt="Cart"></a>
                <div class="profile-dropdown">
                    <a href="#" id="user-icon-link" title="Profile"><img src="images/user-icon.png" alt="User Profile"></a>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Sign Out</a> 
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <a href="#" id="login-link" class="login-signup-link">Login / Sign Up</a>
            <?php } ?>
        </div>
    </header>

    <main class="appointment-container">
        <div class="appointment-box">
            <h1>Change Password</h1>
            
            <form action="change-password-process.php" method="POST">
                
                <div class="input-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn-submit">Update Password</button>
                <a href="profile.php" class="btn-back-form">Cancel</a>

            </form>
        </div>
    </main>
    
    <div id="login-modal" class="modal-overlay">
        <div class="login-container">
            </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>