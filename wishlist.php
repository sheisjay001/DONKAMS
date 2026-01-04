<?php
require_once 'config/db.php';
session_start();
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container' style='padding-top: 100px; text-align: center; min-height: 60vh;'>
            <i class='fas fa-lock' style='font-size: 3rem; color: var(--secondary-color); margin-bottom: 20px;'></i>
            <h2>Please Login</h2>
            <p>You need to be logged in to view your wishlist.</p>
            <a href='login.php' class='btn' style='margin-top: 20px;'>Login Now</a>
          </div>";
    include 'includes/footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Wishlist Items
$sql = "SELECT w.id as wishlist_id, p.*, c.name as category_name 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        JOIN categories c ON p.category_id = c.id 
        WHERE w.user_id = ? 
        ORDER BY w.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="wishlist-page" style="padding-top: 2rem; min-height: 80vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h1 class="section-title">My Wishlist</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card" id="wishlist-item-<?php echo $row['wishlist_id']; ?>">
                    <div class="product-image">
                        <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($row['name']); ?>'">
                        <button class="remove-wishlist" data-id="<?php echo $row['wishlist_id']; ?>" style="position: absolute; top: 10px; right: 10px; background: rgba(231, 76, 60, 0.9); color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $row['name']; ?></h3>
                        <p class="price">₦<?php echo number_format($row['price'], 2); ?></p>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button class="btn add-to-cart" data-id="<?php echo $row['id']; ?>" style="flex: 1;">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 50px;">
                <i class="far fa-heart" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                <p>Your wishlist is empty.</p>
                <a href="index.php" class="btn" style="margin-top: 15px;">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.querySelectorAll('.remove-wishlist').forEach(btn => {
    btn.addEventListener('click', function() {
        const wishlistId = this.getAttribute('data-id');
        if(confirm('Remove from wishlist?')) {
            // In a real app, use fetch to call an API
            // For now, let's just simulate removal from UI
            document.getElementById('wishlist-item-' + wishlistId).remove();
            showToast('Wishlist', 'Item removed', 'success');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
