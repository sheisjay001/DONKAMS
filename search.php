<?php
require_once 'config/db.php';
include 'includes/header.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<main>
    <section class="products" style="padding-top: 120px; min-height: 60vh;">
        <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        <div class="product-grid">
            <?php
            if ($query) {
                // Use prepared statement for security
                $search_term = "%" . $query . "%";
                $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?");
                $stmt->bind_param("sss", $search_term, $search_term, $search_term);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($row['name']); ?>'">
                                <span class="category-tag"><?php echo $row['category_name']; ?></span>
                            </div>
                            <div class="product-info">
                                <h3><?php echo $row['name']; ?></h3>
                                <p class="price">₦<?php echo number_format($row['price'], 2); ?></p>
                                <p class="description"><?php echo substr($row['description'], 0, 50) . '...'; ?></p>
                                <button class="btn add-to-cart" data-id="<?php echo $row['id']; ?>">Add to Cart</button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div style='grid-column: 1/-1; text-align: center; padding: 2rem;'>
                            <i class='fas fa-search' style='font-size: 3rem; color: #ddd; margin-bottom: 1rem;'></i>
                            <p>No products found matching your search.</p>
                            <a href='index.php' class='btn' style='margin-top: 1rem;'>Browse All Products</a>
                          </div>";
                }
                $stmt->close();
            } else {
                echo "<p>Please enter a search term.</p>";
            }
            ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
