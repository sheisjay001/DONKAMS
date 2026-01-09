<?php 
require_once __DIR__ . '/config/db.php';
session_start(); 
require_once __DIR__ . '/includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DONKAMS</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Override for standalone page */
        body {
            background: #f8f9fa;
        }
        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(26, 37, 47, 0.85) 0%, rgba(44, 62, 80, 0.8) 100%), url('images/hero-bg-custom.jpg') center/cover;
        }
        .back-link {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10;
        }
        .back-link:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>

    <main class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <a href="index.php" style="text-decoration: none;">
                    <h2 style="margin-bottom: 0.5rem;">DONKAMS</h2>
                </a>
                <p style="color: #666; margin-bottom: 1.5rem;">Welcome back! Please login to your account.</p>
            </div>
            
            <?php
            $old_email = isset($_SESSION['old_input']['email']) ? $_SESSION['old_input']['email'] : '';
            // Clear old input
            if (isset($_SESSION['old_input'])) unset($_SESSION['old_input']);
            ?>

            <?php
            if (isset($_GET['error'])) {
                echo '<p class="error-msg" role="alert"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_GET['error']) . '</p>';
            }
            if (isset($_GET['success'])) {
                echo '<p class="success-msg" role="alert"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_GET['success']) . '</p>';
            }
            ?>
            <?php 
            // CSRF Included at top of file
            ?>
            <form action="auth_action.php" method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="redirect" value="<?php echo isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : ''; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($old_email); ?>" aria-required="true">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password" aria-required="true">
                </div>

                <button type="submit" class="btn btn-full">Login</button>
            </form>
            <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>
    
    <script src="js/script.js"></script>
</body>
</html>
