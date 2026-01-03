<?php
session_start();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

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
    <title>Book Appointment - Impian Optometrist</title>
    
    <link rel="stylesheet" href="header.css"> 
    <link rel="stylesheet" href="book-appointment-style.css"> 
    <link rel="stylesheet" href="cart-features.css">
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <header class="main-header">
        <div class="logo">
            <h1>IMPIAN OPTOMETRIST</h1>
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="search_query" placeholder="Search items..." class="search-box">
            </form>
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
                <div class="cart-icon-wrapper">
                    <a href="cart.php" title="Cart"><img src="images/bag-icon.png" alt="Cart"></a>
                    <?php if ($cart_count > 0): ?>
                        <div id="cart-badge-count" class="cart-badge"><?php echo $cart_count; ?></div>
                    <?php else: ?>
                        <div id="cart-badge-count" class="cart-badge" style="display: none;">0</div>
                    <?php endif; ?>
                </div>
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
        <a href="customer-appointment.php" class="back-link">&larr; Back to Appointments</a>
        <div class="appointment-box">
            <h1>Book an Appointment</h1>
            <p>Please fill in the form below to schedule your appointment.</p>
            
            <form action="appointment-process.php" method="POST">
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_number" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="date">Appointment Date</label>
                        <input type="date" id="date" name="appointment_date" required>
                    </div>
                    <div class="input-group">
                        <label for="time">Appointment Time</label>
                        <select id="time" name="appointment_time" required>
                            <option value="" disabled selected>Select a time</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                            <option value="16:00">04:00 PM</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="doctor">Doctor</label>
                    <select id="doctor" name="doctor" required>
                        <option value="" disabled selected>Select a doctor</option>
                        <option value="Dr. Liana">Dr. Liana</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="notes">Reason for Visit (Optional)</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="E.g., Annual check-up, blurry vision..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Book Appointment</button>

            </form>
        </div>
    </main>
    
    <script src="script.js"></script>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var firstName = document.getElementById('first_name').value.trim();
            var lastName = document.getElementById('last_name').value.trim();
            var email = document.getElementById('email').value.trim();
            var phone = document.getElementById('phone').value.trim();
            var date = document.getElementById('date').value;
            var time = document.getElementById('time').value;
            var doctor = document.getElementById('doctor').value;

            if (!firstName || !lastName || !email || !phone || !date || !time || !doctor) {
                event.preventDefault(); 
                alert("Please fill in all required fields before booking.");
            }
        });
    </script>
</body>
</html>