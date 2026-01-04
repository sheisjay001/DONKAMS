<?php
require_once 'config/db.php';
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

// Fetch Related Products
$stmt_related = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$stmt_related->bind_param("ii", $product['category_id'], $product_id);
$stmt_related->execute();
$result_related = $stmt_related->get_result();

?>

<main class="product-details-page" style="padding-top: 2rem;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin-bottom: 20px; color: #7f8c8d;">
            <a href="index.php" style="color: inherit; text-decoration: none;">Home</a> / 
            <a href="index.php#category-<?php echo $product['category_slug']; ?>" style="color: inherit; text-decoration: none;"><?php echo $product['category_name']; ?></a> / 
            <span><?php echo $product['name']; ?></span>
        </div>

        <div class="product-detail-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 60px;">
            <!-- Product Image -->
            <div class="product-detail-image">
                <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);" onerror="this.src='https://via.placeholder.com/600x400?text=<?php echo urlencode($product['name']); ?>'">
            </div>

            <!-- Product Info -->
            <div class="product-detail-info">
                <h1 style="font-size: 2.5rem; margin-bottom: 10px; color: var(--primary-color);"><?php echo $product['name']; ?></h1>
                <div class="product-meta" style="margin-bottom: 20px;">
                    <span class="badge" style="background: var(--accent-color); color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.9rem;"><?php echo $product['category_name']; ?></span>
                    <span class="stock-status" style="color: #27ae60; margin-left: 10px;"><i class="fas fa-check-circle"></i> In Stock</span>
                </div>
                
                <div class="product-price" style="font-size: 2rem; font-weight: 700; color: var(--secondary-color); margin-bottom: 20px;">
                    ₦<?php echo number_format($product['price'], 2); ?>
                </div>

                <p class="product-description" style="line-height: 1.8; color: #555; margin-bottom: 30px;">
                    <?php echo nl2br($product['description']); ?>
                </p>

                <div class="product-actions" style="display: flex; gap: 15px;">
                    <button class="btn btn-lg add-to-cart" data-id="<?php echo $product['id']; ?>" style="flex: 1;">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline wishlist-btn" data-id="<?php echo $product['id']; ?>" style="border: 2px solid var(--secondary-color); background: transparent; color: var(--secondary-color);">
                        <i class="far fa-heart"></i>
                    </button>
                </div>

                <!-- Features/Specs Placeholder -->
                <div class="product-specs" style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
                    <h3 style="margin-bottom: 15px;">Key Features</h3>
                    <ul style="list-style-position: inside; color: #555;">
                        <li>Premium build quality</li>
                        <li>1 Year Official Warranty</li>
                        <li>Free Delivery within Lagos</li>
                        <li>7-day return policy</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if ($result_related->num_rows > 0): ?>
        <section class="related-products" style="margin-bottom: 60px;">
            <h2 class="section-title">Related Products</h2>
            <div class="product-grid">
                <?php while($related = $result_related->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="images/<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($related['name']); ?>'">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $related['name']; ?></h3>
                        <p class="price">₦<?php echo number_format($related['price'], 2); ?></p>
                        <a href="product.php?id=<?php echo $related['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php endif; ?>

    </div>
</main>

<script>
    // Simple Wishlist Toggle (Frontend only for now, backend to be implemented)
    document.querySelector('.wishlist-btn').addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (icon.classList.contains('far')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            // Check if user is logged in (you might need a global JS variable for this)
            // For now, just show toast
            if (typeof showToast === 'function') {
                showToast('Wishlist', 'Added to wishlist!', 'success');
            } else {
                alert('Added to wishlist!');
            }
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
