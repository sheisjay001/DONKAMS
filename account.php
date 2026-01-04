<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Details
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch Orders
$order_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$orders = $order_stmt->get_result();

include 'includes/header.php';
?>

<main class="account-page" style="padding-top: 2rem; min-height: 80vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h1 class="section-title">My Account</h1>
        
        <div class="dashboard-grid" style="display: grid; grid-template-columns: 1fr 3fr; gap: 30px;">
            
            <!-- Sidebar -->
            <div class="dashboard-sidebar" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); height: fit-content;">
                <div class="user-profile" style="text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <div class="avatar" style="width: 80px; height: 80px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 10px;">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p style="color: #7f8c8d; font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <ul class="dashboard-menu" style="list-style: none;">
                    <li style="margin-bottom: 10px;">
                        <a href="#orders" class="active" style="display: block; padding: 10px; background: #f8f9fa; color: var(--primary-color); border-radius: 5px; text-decoration: none; font-weight: 500;">
                            <i class="fas fa-box-open" style="margin-right: 10px;"></i> Order History
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#profile" style="display: block; padding: 10px; color: var(--text-color); text-decoration: none;">
                            <i class="fas fa-user-edit" style="margin-right: 10px;"></i> Edit Profile
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="wishlist.php" style="display: block; padding: 10px; color: var(--text-color); text-decoration: none;">
                            <i class="fas fa-heart" style="margin-right: 10px;"></i> Wishlist
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" style="display: block; padding: 10px; color: #e74c3c; text-decoration: none;">
                            <i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="dashboard-content">
                
                <!-- Order History Section -->
                <div id="orders-section">
                    <h2 style="margin-bottom: 20px; border-bottom: 2px solid var(--secondary-color); display: inline-block; padding-bottom: 5px;">Order History</h2>
                    
                    <?php if ($orders->num_rows > 0): ?>
                        <div class="order-list">
                            <?php while($order = $orders->fetch_assoc()): ?>
                                <div class="order-card" style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid var(--primary-color);">
                                    <div class="order-header" style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                                        <div>
                                            <span style="font-weight: 700;">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                            <span style="color: #7f8c8d; font-size: 0.9rem; margin-left: 10px;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                        </div>
                                        <div>
                                            <span class="badge" style="background: <?php echo $order['status'] == 'completed' ? '#27ae60' : ($order['status'] == 'pending' ? '#f39c12' : '#e74c3c'); ?>; color: white; padding: 4px 10px; border-radius: 15px; font-size: 0.85rem; text-transform: capitalize;">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="order-body" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="order-total">
                                            <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 2px;">Total Amount</p>
                                            <p style="font-weight: 700; font-size: 1.1rem;">₦<?php echo number_format($order['total_amount'], 2); ?></p>
                                        </div>
                                        <button class="btn btn-sm" style="padding: 5px 15px; font-size: 0.9rem;">View Items</button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 40px; background: white; border-radius: 10px;">
                            <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                            <p>You haven't placed any orders yet.</p>
                            <a href="index.php" class="btn" style="margin-top: 15px;">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
