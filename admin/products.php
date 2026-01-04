<?php
require_once '../config/db.php';
include 'includes/header.php';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Optional: Delete image file as well
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Product deleted successfully";
    } else {
        $error_msg = "Failed to delete product";
    }
}

// Fetch Products
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<div class="section-header">
    <h2>Products</h2>
    <a href="add_product.php" class="btn" style="background: var(--secondary-color); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;"><i class="fas fa-plus"></i> Add New Product</a>
</div>

<?php if (isset($success_msg)): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?php echo $error_msg; ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <img src="../images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" onerror="this.src='https://via.placeholder.com/50'">
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>₦<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="action-btn" style="color: var(--primary-color); margin-right: 10px;"><i class="fas fa-edit"></i></a>
                        <a href="products.php?delete=<?php echo $row['id']; ?>" class="action-btn" style="color: #e74c3c;" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No products found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
