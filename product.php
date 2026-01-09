<?php
require_once 'config/db.php';
session_start();
include 'includes/header.php';

// Get Product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch Product Details
$stmt = $conn->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div style='text-align:center; padding: 50px;'><h2>Product not found</h2><a href='index.php' class='btn'>Back to Home</a></div>";
    include 'includes/footer.php';
    exit();
}

$product = $result->fetch_assoc();

$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $pid = (int)$product['id'];
    $chk = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $chk->bind_param("ii", $uid, $pid);
    $chk->execute();
    $in_wishlist = (bool)$chk->get_result()->fetch_assoc();
}

// Fetch Related Products
$stmt_related = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$stmt_related->bind_param("ii", $product['category_id'], $product_id);
$stmt_related->execute();
$result_related = $stmt_related->get_result();

?>


<div class="container" style="max-width: var(--container-width); margin: 0 auto; padding: var(--space-xl) 5%;">
    
    <!-- Breadcrumb -->
    <div class="breadcrumb" style="margin-bottom: var(--space-lg); color: var(--text-light);">
        <a href="index.php" style="color: inherit; text-decoration: none;">Home</a> / 
        <a href="index.php#category-<?php echo $product['category_slug']; ?>" style="color: inherit; text-decoration: none;"><?php echo $product['category_name']; ?></a> / 
        <span style="color: var(--primary-color); font-weight: 500;"><?php echo $product['name']; ?></span>
    </div>

    <div class="grid grid-2" style="margin-bottom: var(--space-2xl);">
        <!-- Product Image -->
        <div class="product-detail-image">
            <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-md);" onerror="this.src='https://via.placeholder.com/600x400?text=<?php echo urlencode($product['name']); ?>'">
        </div>

        <!-- Product Info -->
        <div class="product-detail-info">
            <h1 style="margin-bottom: var(--space-sm);"><?php echo $product['name']; ?></h1>
            <div class="product-meta" style="margin-bottom: var(--space-md);">
                <span class="badge" style="background: var(--accent-color); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.875rem; font-weight: 600;"><?php echo $product['category_name']; ?></span>
                <span class="stock-status" style="color: var(--success-color); margin-left: var(--space-sm); font-weight: 500;"><i class="fas fa-check-circle"></i> In Stock</span>
            </div>
            
            <div class="product-price" style="font-size: 2rem; font-weight: 700; color: var(--secondary-color); margin-bottom: var(--space-md);">
                ₦<?php echo number_format($product['price'], 2); ?>
            </div>

            <p class="product-description" style="line-height: 1.8; color: var(--text-color); margin-bottom: var(--space-lg);">
                <?php echo nl2br($product['description']); ?>
            </p>

            <div class="product-actions" style="display: flex; gap: var(--space-md);">
                <button class="btn btn-lg add-to-cart" data-id="<?php echo $product['id']; ?>" style="flex: 1;">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button class="btn wishlist-btn" data-id="<?php echo $product['id']; ?>" style="border: 2px solid var(--secondary-color); background: transparent; color: var(--secondary-color); padding: 0 1.5rem;" aria-label="Add to Wishlist">
                    <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                </button>
            </div>

            <!-- Features/Specs Placeholder -->
            <div class="product-specs" style="margin-top: var(--space-xl); border-top: 1px solid var(--border-color); padding-top: var(--space-md);">
                <h3>Key Features</h3>
                <ul style="list-style-position: inside; color: var(--text-color); margin-top: var(--space-sm);">
                    <li style="margin-bottom: var(--space-xs);">Premium build quality</li>
                    <li style="margin-bottom: var(--space-xs);">1 Year Official Warranty</li>
                    <li style="margin-bottom: var(--space-xs);">Free Delivery within Lagos</li>
                    <li style="margin-bottom: var(--space-xs);">7-day return policy</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if ($result_related->num_rows > 0): ?>
    <section class="related-products">
        <h2 class="section-title">Related Products</h2>
        <div class="grid grid-4">
            <?php while($related = $result_related->fetch_assoc()): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="images/<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($related['name']); ?>'">
                </div>
                <div class="product-info">
                    <h3><?php echo $related['name']; ?></h3>
                    <p class="price">₦<?php echo number_format($related['price'], 2); ?></p>
                    <a href="product.php?id=<?php echo $related['id']; ?>" class="btn" style="width: 100%;">View Details</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>

</div>

<script></script>

<?php include 'includes/footer.php'; ?>
