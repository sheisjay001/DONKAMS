<?php
require_once '../config/db.php';
include 'includes/header.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id == 0) {
    header("Location: orders.php");
    exit();
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        $success_msg = "Order status updated successfully";
    } else {
        $error_msg = "Failed to update status";
    }
}

// Fetch Order Details
$stmt = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch Order Items
$stmt_items = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>

<div class="section-header">
    <h2>Order Details #<?php echo $order['id']; ?></h2>
    <a href="orders.php" class="btn btn-sm" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px;">Back to Orders</a>
</div>

<?php if (isset($success_msg)): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Order Items -->
    <div class="table-container">
        <h3>Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <img src="../images/<?php echo $item['image']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td>₦<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="3" style="text-align: right;">Total Amount:</td>
                    <td>₦<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Order Info & Status -->
    <div>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3>Customer Info</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3>Update Status</h3>
            <form method="POST">
                <input type="hidden" name="update_status" value="1">
                <div class="form-group">
                    <label>Current Status: <span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></label>
                    <select name="status" class="form-control" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn" style="background: var(--primary-color); color: white; width: 100%; padding: 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Update Status</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
