<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];
$user = null;

$sql = "SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);

if (!$user) {
    die("Error: User data not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="book-appointment-style.css"> 
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="register.css">
</head>
<body class="page-background">

    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN Optometrist</h1>
            <input type="search" name="search_query" placeholder="Search items..." class="search-box">
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php#frames-section">Frames</a></li>
                <li><a href="index.php#contact-section">Contact Lense</a></li>
                <li><a href="index.php#clip-section">Clip On</a></li>
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
            <h1>Edit Profile</h1>
            <p>Update your personal information below.</p>
            
            <form action="edit-profile-process.php" method="POST">
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($user['first_name']); ?>" 
                               required 
                               minlength="7" 
                               maxlength="40" 
                               title="Must be between 7 and 40 characters">
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($user['last_name']); ?>" 
                               required 
                               minlength="7" 
                               maxlength="40" 
                               title="Must be between 7 and 40 characters">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" 
                               required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_number" 
                               value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                               pattern="^(\+|0)[0-9\s-]{9,}"
                               title="Please enter a valid number (e.g., +60 10-840 6912 or 010-840 6912)">
                    </div>
                </div>

                <button type="submit" class="btn-submit">Save Changes</button>
                <a href="profile.php" class="btn-back-form">Cancel</a>

            </form>
            </div>
    </main>
    
    <div id="login-modal" class="modal-overlay">
        </div>
    
    <script src="script.js"></script>
</body>
</html>