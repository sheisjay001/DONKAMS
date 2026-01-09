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
    <a href="#main-content" class="skip-link">Skip to content</a>
    <header>
        <nav class="navbar" role="navigation" aria-label="Main Navigation">
            <a href="index.php" class="logo">
                <!-- Using the uploaded image as logo -->
                <img src="images/logo_original.jpg" alt="DONKAMS Home">
                <span class="logo-text">DONKAMS</span>
            </a>
            
            <button class="hamburger" aria-label="Toggle navigation" aria-controls="nav-menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>

            <div class="search-bar">
                <form action="search.php" method="GET" role="search">
                    <input type="text" name="q" placeholder="Search products..." aria-label="Search products">
                    <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <ul class="nav-links" id="nav-menu">
                <!-- Mobile Search -->
                <li class="mobile-search" style="display: none;">
                    <form action="search.php" method="GET" role="search" style="display: flex;">
                        <input type="text" name="q" placeholder="Search..." aria-label="Search products mobile" style="width: 100%; padding: 0.5rem;">
                        <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                    </form>
                </li>

                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#products">Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li>
                    <a href="cart.php" class="cart-link" aria-label="Cart">
                        <i class="fas fa-shopping-cart" aria-hidden="true"></i> Cart 
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
                    <li><a href="logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem; color: white;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary" style="padding: 0.5rem 1rem; color: white;">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main id="main-content">
