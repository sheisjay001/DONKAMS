<?php
require_once '../config/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header("Location: products.php");
    exit();
}

// Fetch Existing Product Data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Fetch Categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    // Image Upload (Optional)
    $image = $product['image']; // Default to existing
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $new_filename;
            // Optionally delete old image if needed
        } else {
            $error = "Failed to upload new image.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("ssdsii", $name, $description, $price, $image, $category_id, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully'); window.location.href='products.php';</script>";
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}
?>

<div class="section-header">
    <h2>Edit Product</h2>
    <a href="products.php" class="btn btn-sm" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px;">Back to List</a>
</div>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 800px;">
    <?php if (isset($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php 
                if ($categories) {
                    while($cat = $categories->fetch_assoc()) {
                        $selected = ($cat['id'] == $product['category_id']) ? 'selected' : '';
                        echo "<option value='" . $cat['id'] . "' $selected>" . $cat['name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Price (₦)</label>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Image</label><br>
            <img src="../images/<?php echo $product['image']; ?>" style="max-width: 100px; margin-bottom: 10px; border-radius: 5px;">
        </div>

        <div class="form-group">
            <label>New Image (Leave blank to keep current)</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn" style="background: var(--secondary-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Update Product</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
