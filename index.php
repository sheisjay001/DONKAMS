<?php 
require_once 'config/db.php';
include 'includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-subtitle">Welcome to</span>
            <h1>DONKAMS</h1>
            <div class="hero-divider"></div>
            <p class="hero-motto">One click, multiple solutions</p>
            <p class="hero-desc">Your premium destination for Electronics, Gaming, and Tech Essentials.</p>
            <a href="#products" class="btn btn-lg">Start Shopping <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    <!-- Product Categories Section -->
    <section id="categories" class="categories">
        <h2 class="section-title">Our Categories</h2>
        <div class="category-grid">
            <?php
            $cat_sql = "SELECT * FROM categories";
            $cat_result = $conn->query($cat_sql);

            if ($cat_result->num_rows > 0) {
                while($row = $cat_result->fetch_assoc()) {
                    echo '<div class="category-card">';
                    echo '<div class="category-icon"><i class="fas ' . $row["icon"] . '"></i></div>';
                    echo '<h3>' . $row["name"] . '</h3>';
                    echo '<a href="#category-' . $row["slug"] . '" class="btn">Explore</a>';
                    echo '</div>';
                }
            } else {
                echo "No categories found.";
            }
            ?>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="products" class="products">
        <h2 class="section-title">Featured Products</h2>
        <div class="product-grid">
            <?php
            $prod_sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8";
            $prod_result = $conn->query($prod_sql);

            if ($prod_result->num_rows > 0) {
                while($row = $prod_result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <!-- Placeholder image logic if file doesn't exist -->
                            <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($row['name']); ?>'">
                            <span class="category-tag"><?php echo $row['category_name']; ?></span>
                        </div>
                        <div class="product-info">
                            <a href="product.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <h3><?php echo $row['name']; ?></h3>
                            </a>
                            <p class="price">₦<?php echo number_format($row['price'], 2); ?></p>
                            <p class="description"><?php echo substr($row['description'], 0, 50) . '...'; ?></p>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <button class="btn add-to-cart" data-id="<?php echo $row['id']; ?>" style="flex: 1;">Add to Cart</button>
                                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn" style="background: transparent; border: 1px solid var(--secondary-color); color: var(--secondary-color); padding: 0.8rem 1rem;"><i class="fas fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
