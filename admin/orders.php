<?php
require_once '../config/db.php';
include 'includes/header.php';

// Fetch Orders
$sql = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<div class="section-header">
    <h2>Orders</h2>
</div>

<div class="table-container">
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
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['username']); ?><br>
                        <small style="color: #777;"><?php echo htmlspecialchars($row['email']); ?></small>
                    </td>
                    <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                    <td>₦<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['id']; ?>" class="action-btn" style="color: var(--primary-color);"><i class="fas fa-eye"></i> View</a>
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
