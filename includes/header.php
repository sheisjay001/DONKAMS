<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DONKAMS - One click, multiple solution</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <!-- Using the uploaded image as logo -->
                <img src="images/logo_original.jpg" alt="DONKAMS Logo">
                <span class="logo-text">DONKAMS</span>
            </a>
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
            <div class="search-bar">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="Search products...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#products">Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li>
                    <a href="cart.php" class="cart-link">
                        <i class="fas fa-shopping-cart"></i> Cart 
                        <span class="cart-count" id="cart-count">
                            <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php" style="color: var(--accent-color); font-weight: bold;">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="logout.php" class="btn" style="padding: 0.5rem 1rem; color: white;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn" style="padding: 0.5rem 1rem; color: white;">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
