<?php 
session_start(); 
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impian Optometrist</title>
    
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="best-selling.css"> 
    <link rel="stylesheet" href="frames.css">
    <link rel="stylesheet" href="lenses.css">
    <link rel="stylesheet" href="hero.css">
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="clip.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="cart-features.css">
    <link rel="stylesheet" href="card-slider.css">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="search_query" placeholder="Search items..." class="search-box">
            </form>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#" class="main-nav-link" data-slide="1">FRAMES</a></li>
                <li><a href="#" class="main-nav-link" data-slide="2">LENSES</a></li> 
                <li><a href="#" class="main-nav-link" data-slide="3">CLIP-ON</a></li>
                <li><a href="#" class="main-nav-link" data-slide="4">CONTACT LENSE</a></li>
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

    <main class="hero-section">

        <button id="main-arrow-left" class="main-slider-arrow is-hidden">&larr;</button>

        <div class="main-card-container">
            
            <div class="main-slider-track">
                
                <section class="main-slider-page" data-title="BEST SELLERS">
                    <h2>BEST SELLERS</h2>
                    <div id="best-selling-section" class="best-selling-container">
                        <div class="slider-wrapper">
                            <a href="#" id="best-arrow-left" class="arrow left-arrow"><img src="images/back-button.png" alt="Previous"></a>
                            <div class="product-grid-window">
                                <div id="best-grid" class="product-grid">
                                    <?php 
                                    // Connect to database ONCE here
                                    include 'db_connect.php'; 
                                    
                                    if (!$conn) {
                                        die("Database connection failed: " . mysqli_connect_error());
                                    }

                                    $sql_best = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image FROM item WHERE item_name IS NOT NULL AND sales_count > 0 AND ITEM_STATUS = 'Available' ORDER BY sales_count DESC LIMIT 5";
                                    $result_best = $conn->query($sql_best);
                                    if ($result_best && $result_best->num_rows > 0) {
                                        while ($row = $result_best->fetch_assoc()) {
                                            echo '<div class="product-card">';
                                            echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '">';
                                            echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                                            echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                                            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                                echo '<button type="button" class="btn-add-to-cart" data-item-id="' . htmlspecialchars($row['ITEM_ID']) . '">Add to Cart</button>';
                                            } else {
                                                echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                                            }
                                            echo '</div>';
                                        }
                                    } else { echo '<p>No best sellers found yet.</p>'; }
                                    ?>
                                </div>
                            </div>
                            <a href="#" id="best-arrow-right" class="arrow right-arrow"><img src="images/next-button.png" alt="Next"></a>
                        </div>
                    </div>
                </section>

                <section class="main-slider-page" data-title="FRAMES">
                    <h2>FRAMES <a href="all-frames.php" class="btn-see-all">See All</a></h2>
                    <div id="frames-section" class="frames-container">
                        <div class="slider-wrapper">
                            <a href="#" id="frame-arrow-left" class="arrow left-arrow"><img src="images/back-button.png" alt="Previous"></a>
                            <div class="product-grid-window">
                                <div id="frame-grid" class="product-grid">
                                    <?php
                                    $sql_frames = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image 
                                                   FROM item 
                                                   WHERE CATEGORY_ID = 'CAT001' 
                                                   AND item_name IS NOT NULL 
                                                   AND ITEM_STATUS = 'Available' 
                                                   ORDER BY sales_count DESC, ITEM_BRAND ASC"; 
                                    
                                    $result_frames = $conn->query($sql_frames);
                                    
                                    if ($result_frames && $result_frames->num_rows > 0) {
                                        while ($row = $result_frames->fetch_assoc()) {
                                            echo '<div class="product-card">';
                                            echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . '">';
                                            echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                                            echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                                            
                                            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                                echo '<button type="button" class="btn-add-to-cart" data-item-id="' . htmlspecialchars($row['ITEM_ID']) . '">Add to Cart</button>';
                                            } else {
                                                echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                                            }
                                            echo '</div>';
                                        }
                                    } else { echo '<p>No frames found.</p>'; }
                                    ?>
                                </div>
                            </div>
                            <a href="#" id="frame-arrow-right" class="arrow right-arrow"><img src="images/next-button.png" alt="Next"></a>
                        </div>
                    </div>
                </section>

                <section class="main-slider-page" data-title="LENSES">
                    <h2>LENSES <a href="all-lenses.php" class="btn-see-all">See All</a></h2>
                    <div id="lenses-section" class="lenses-container"> 
                        <div class="slider-wrapper">
                            <a href="#" id="lenses-arrow-left" class="arrow left-arrow"><img src="images/back-button.png" alt="Previous"></a>
                            <div class="product-grid-window">
                                <div id="lenses-grid" class="product-grid">
                                    <?php
                                    $sql_lenses = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image 
                                                   FROM item 
                                                   WHERE CATEGORY_ID = 'CAT002' 
                                                   AND ITEM_STATUS = 'Available' 
                                                   ORDER BY sales_count DESC LIMIT 5";

                                    $result_lenses = $conn->query($sql_lenses);

                                    if ($result_lenses && $result_lenses->num_rows > 0) {
                                        while ($row = $result_lenses->fetch_assoc()) {
                                            $displayName = !empty($row['item_name']) ? $row['item_name'] : '';

                                            echo '<div class="product-card">';
                                            echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . '">';
                                            echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($displayName) . '</h3>';
                                            echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                                            
                                            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                                echo '<button type="button" class="btn-add-to-cart" data-item-id="' . htmlspecialchars($row['ITEM_ID']) . '">Add to Cart</button>';
                                            } else {
                                                echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                                            }
                                            echo '</div>';
                                        }
                                    } else { 
                                        echo '<p>No lenses found.</p>'; 
                                    }
                                    ?>
                                </div>
                            </div>
                            <a href="#" id="lenses-arrow-right" class="arrow right-arrow"><img src="images/next-button.png" alt="Next"></a>
                        </div>
                    </div>
                </section>

                <section class="main-slider-page" data-title="CLIP-ON">
                    <h2>CLIP-ON <a href="all-clip-ons.php" class="btn-see-all">See All</a></h2>
                    <div id="clip-section" class="clip-container">
                        <div class="slider-wrapper">
                            <a href="#" id="clip-arrow-left" class="arrow left-arrow"><img src="images/back-button.png" alt="Previous"></a>
                            <div class="product-grid-window">
                                <div id="clip-grid" class="product-grid">
                                    <?php
                                    $sql_clipons = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image FROM item WHERE CATEGORY_ID = 'CAT003' AND item_name IS NOT NULL AND ITEM_STATUS = 'Available' ORDER BY sales_count DESC, ITEM_BRAND ASC"; 
                                    $result_clipons = $conn->query($sql_clipons);
                                    if ($result_clipons && $result_clipons->num_rows > 0) {
                                        while ($row = $result_clipons->fetch_assoc()) {
                                            echo '<div class="product-card">';
                                            echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '">';
                                            echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                                            echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                                            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                                echo '<button type="button" class="btn-add-to-cart" data-item-id="' . htmlspecialchars($row['ITEM_ID']) . '">Add to Cart</button>';
                                            } else {
                                                echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                                            }
                                            echo '</div>';
                                        }
                                    } else { echo '<p>No clip-ons found.</p>'; }
                                    ?>
                                </div>
                            </div> 
                            <a href="#" id="clip-arrow-right" class="arrow right-arrow"><img src="images/next-button.png" alt="Next"></a>
                        </div>
                    </div>
                </section>

                <section class="main-slider-page" data-title="CONTACT LENSE">
                    <h2>CONTACT LENSE <a href="all-contact-lenses.php" class="btn-see-all">See All</a></h2>
                    <div id="contact-section" class="contact-container">
                        <div class="slider-wrapper">
                            <a href="#" id="contact-arrow-left" class="arrow left-arrow"><img src="images/back-button.png" alt="Previous"></a>
                            <div class="product-grid-window">
                                <div id="contact-grid" class="product-grid">
                                    <?php
                                    $sql_contacts = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image FROM item WHERE CATEGORY_ID = 'CAT005' AND item_name IS NOT NULL AND ITEM_STATUS = 'Available' ORDER BY sales_count DESC, ITEM_BRAND ASC";
                                    $result_contacts = $conn->query($sql_contacts);
                                    if ($result_contacts && $result_contacts->num_rows > 0) {
                                        while ($row = $result_contacts->fetch_assoc()) {
                                            echo '<div class="product-card">';
                                            echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '">';
                                            echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                                            echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                                            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                                echo '<button type="button" class="btn-add-to-cart" data-item-id="' . htmlspecialchars($row['ITEM_ID']) . '">Add to Cart</button>';
                                            } else {
                                                echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                                            }
                                            echo '</div>';
                                        }
                                    } else { echo '<p>No contact lenses found.</p>'; }
                                    ?>
                                </div>
                            </div> 
                            <a href="#" id="contact-arrow-right" class="arrow right-arrow"><img src="images/next-button.png" alt="Next"></a>
                        </div>
                    </div>
                </section>

                <?php 
                // Close the database connection at the end of all queries
                if(isset($conn)) { $conn->close(); } 
                ?>
                
            </div> </div> <button id="main-arrow-right" class="main-slider-arrow">&rarr;</button>

    </main>
    
    <div id="login-modal" class="modal-overlay">
        <div class="login-container">
            <div class="auth-toggle">
                <a href="#" id="login-toggle" class="active">LOGIN</a>
                <a href="#" id="register-toggle">REGISTER</a>
            </div>
            <div id="login-form">
                <form action="login-process.php" method="POST">
                    <div class="input-group"><input type="email" name="email" placeholder="Email" required></div>
                    <div class="input-group"><input type="password" name="password" placeholder="Password" required></div>
                    <button type="submit" class="btn-signin">SIGN IN</button>
                </form>
            </div>
            <div id="register-form" style="display: none;">
                <form action="register-process.php" method="POST">
                    <div class="form-row">
                        <div class="input-group"><input type="text" name="first_name" placeholder="First Name" required minlength="3" maxlength="40" title="Must be between 3 and 40 characters"></div>
                        <div class="input-group"><input type="text" name="last_name" placeholder="Last Name" required minlength="3" maxlength="40" title="Must be between 3 and 40 characters"></div>
                    </div>
                    <div class="input-group"><input type="email" name="email" placeholder="Email" required></div>
                    <div class="input-group">
                    <input type="tel" 
                           name="phone_number" 
                           placeholder="Phone Number" 
                           pattern="^(\+|0)[0-9\s-]{9,}"
                           title="Please enter a valid number (e.g., +60 10-840 6912 or 010-840 6912)">
                    </div>
                    <div class="input-group">
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>Gender *</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="password" 
                               name="password" 
                               placeholder="Password" 
                               required 
                               minlength="3" 
                               maxlength="15" 
                               title="Must be between 3 and 15 characters">
                    </div>
                    <div class="input-group">
                        <input type="password" 
                               name="confirm_password" 
                               placeholder="Confirm Password" 
                               required 
                               minlength="3" 
                               maxlength="15" 
                               title="Must be between 3 and 15 characters">
                    </div>
                    <button type="submit" class="btn-signin">CREATE ACCOUNT NOW</button>
                </form> 
            </div>
        </div>
    </div>
    
    <div id="toast-popup" class="toast-popup">
        Item added to cart!
    </div>
    
    <script src="script.js"></script>
    
</body>
</html>