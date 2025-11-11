<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Impian Optometrist</title>
    
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="hero.css">
    <link rel="stylesheet" href="frames.css">
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="clip.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="appointment.css">
    <link rel="stylesheet" href="book-appointment.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <style>
        .search-results-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .search-results-container h2 {
            font-size: 28px;
            font-weight: normal;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .search-results-container .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            justify-content: center;
        }
        
        .no-results {
            text-align: center;
            font-size: 18px;
            color: #555;
            padding: 50px 0;
        }

        .search-results-container .product-card {
            background-color: #fff;
            padding: 20px;
            width: 100%; /* Changed from 250px to be responsive in a grid */
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
            transition: transform 0.3s ease;
        }
        .search-results-container .product-card:hover { 
            transform: translateY(-5px); 
        }
        .search-results-container .product-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .search-results-container .product-card h3 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 22px;
            margin-bottom: 8px;
        }
        .search-results-container .product-card p {
            font-size: 16px;
            color: #555;
        }
        .search-results-container .btn-add-to-cart {
            display: inline-block;
            width: 80%;
            background-color: #000;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .search-results-container .btn-add-to-cart:hover {
            background-color: #333;
        }

    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo-search-container"> 
            <h1>IMPIAN OPTOMETRIST</h1>
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="search_query" placeholder="Search items..." class="search-box" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
            </form>
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

    <main class="search-results-container">
        
        <?php
        include 'db_connect.php';

        $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
        
        $search_term = "%" . $search_query . "%";
        
        echo '<h2>Search Results for "' . htmlspecialchars($search_query) . '"</h2>';

        $sql_search = "SELECT ITEM_ID, ITEM_BRAND, item_name, ITEM_PRICE, item_image 
                       FROM item 
                       WHERE (item_name LIKE ? OR ITEM_BRAND LIKE ?)
                       AND item_name IS NOT NULL";
                       
        $stmt = $conn->prepare($sql_search);
        // "ss" means we are binding two string parameters
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $result_search = $stmt->get_result();

        if ($result_search && $result_search->num_rows > 0) {
            echo '<div class="product-grid">';
            while ($row = $result_search->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '    <img src="images/' . htmlspecialchars($row['item_image']) . '" alt="' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '">';
                echo '    <h3>' . htmlspecialchars($row['ITEM_BRAND']) . ' ' . htmlspecialchars($row['item_name']) . '</h3>';
                echo '    <p>RM ' . htmlspecialchars(number_format($row['ITEM_PRICE'], 0)) . '</p>';
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    echo '<form action="add_to_cart.php" method="POST" class="cart-form">';
                    echo '    <input type="hidden" name="item_id" value="' . htmlspecialchars($row['ITEM_ID']) . '">';
                    echo '    <button type="submit" class="btn-add-to-cart">Add to Cart</button>';
                    echo '</form>';
                } else {
                    echo '<a href="#" class="btn-add-to-cart login-trigger">Login to Add</a>';
                }
                echo '</div>';
            }
            echo '</div>'; 
        } else {
            echo '<p class="no-results">No products found matching your search.</p>';
        }

        $stmt->close();
        $conn->close();
        ?>
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
                    <a href="#" class="forgot-password">Forget Your Password?</a>
                    <button type="submit" class="btn-signin">SIGN IN</button>
                </form>
            </div>
            <div id="register-form" style="display: none;">
                <form action="register-process.php" method="POST">
                    <div class="form-row">
                        <div class="input-group"><input type="text" name="first_name" placeholder="First Name" required></div>
                        <div class="input-group"><input type="text" name="last_name" placeholder="Last Name" required></div>
                    </div>
                    <div class="input-group"><input type="email" name="email" placeholder="Email" required></div>
                    <div class="input-group"><input type="tel" name="phone_number" placeholder="Phone Number"></div>
                    <div class="input-group">
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>Gender *</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="input-group"><input type="password" name="password" placeholder="Password" required></div>
                    <div class="input-group"><input type="password" name="confirm_password" placeholder="Confirm Password" required></div>
                    <button type="submit" class="btn-signin">CREATE ACCOUNT NOW</button>
                </form> 
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    
</body>
</html>