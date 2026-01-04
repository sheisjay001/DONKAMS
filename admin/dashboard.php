<?php
require_once __DIR__ . '/../config/db.php';
include 'includes/header.php';

// Fetch Statistics
// Total Sales (only completed orders for accuracy, or all? Let's show all for now or filtering)
// Assuming we want to show potential revenue from pending + actual from completed
$sales_query = "SELECT SUM(total_amount) as total_sales FROM orders"; 
$sales_result = $conn->query($sales_query);
$total_sales = $sales_result->fetch_assoc()['total_sales'] ?? 0;

// Total Orders
$orders_query = "SELECT COUNT(*) as total_orders FROM orders";
$orders_result = $conn->query($orders_query);
$total_orders = $orders_result->fetch_assoc()['total_orders'] ?? 0;

// Total Products
$products_query = "SELECT COUNT(*) as total_products FROM products";
$products_result = $conn->query($products_query);
$total_products = $products_result->fetch_assoc()['total_products'] ?? 0;

// Total Users
$users_query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'customer'";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total_users'] ?? 0;

// Recent Orders
$recent_orders_query = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = $conn->query($recent_orders_query);
?>

<div class="section-header">
    <h2>Dashboard Overview</h2>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h3>₦<?php echo number_format($total_sales, 2); ?></h3>
            <p>Total Sales</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-box-open"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_products; ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="section-header">
        <h3>Recent Orders</h3>
        <a href="orders.php" class="btn btn-sm" style="background: var(--primary-color); color: white;">View All</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recent_orders->num_rows > 0): ?>
                <?php while($order = $recent_orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>₦<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="action-btn" style="color: var(--primary-color);"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
