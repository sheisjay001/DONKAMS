<?php
require_once '../config/db.php';
include 'includes/header.php';

$sql = "SELECT w.id, w.created_at, u.username, u.email, p.name as product_name, p.price 
        FROM wishlist w 
        JOIN users u ON w.user_id = u.id 
        JOIN products p ON w.product_id = p.id 
        ORDER BY w.created_at DESC";
$result = $conn->query($sql);
?>

<div class="section-header">
    <h2>Wishlists</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Product</th>
                <th>Price</th>
                <th>Added</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>₦<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No wishlist entries found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

