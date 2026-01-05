<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Edit Product';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    header("Location: admin_products.php?error=Product ID required");
    exit();
}

$product = getProductById($product_id);

if (!$product) {
    header("Location: admin_products.php?error=Product not found");
    exit();
}

// Get categories for dropdown
$categories = getCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => sanitizeInput($_POST['name'] ?? ''),
        'description' => $_POST['description'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'image_url' => sanitizeInput($_POST['image_url'] ?? '')
    ];
    
    // Validate required fields
    if (empty($data['name']) || empty($data['price']) || $data['price'] <= 0) {
        $error = 'Product name and valid price are required';
    } else {
        // Update product
        if (updateProduct($product_id, $data)) {
            logAdminActivity($current_admin['id'], $current_admin['username'], 'UPDATE_PRODUCT', "Updated product: {$data['name']}", $_SERVER['REMOTE_ADDR']);
            header("Location: admin_products.php?success=Product updated successfully");
            exit();
        } else {
            $error = 'Failed to update product. Please try again.';
        }
    }
}

require_once 'admin_header.php';
?>

<style>
    /* Same styles as add.php, just different header color */
    .form-header {
        background: linear-gradient(90deg, var(--warning-color), #e67e22);
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h2>
            <i class="fas fa-edit"></i>
            Edit Product: <?php echo htmlspecialchars($product['name']); ?>
        </h2>
    </div>

    <div class="form-content">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="productForm">
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="name">Product Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="slug">URL Slug</label>
                    <input type="text" id="slug" class="form-control" 
                           value="<?php echo htmlspecialchars($product['slug']); ?>" readonly>
                    <small style="color: var(--text-color); display: block; margin-top: 5px;">
                        Slug cannot be changed after creation
                    </small>
                </div>

                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price ($) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" class="form-control" 
                           step="0.01" min="0" required 
                           value="<?php echo number_format($product['price'], 2); ?>">
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" 
                           min="0" value="<?php echo $product['stock_quantity']; ?>">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" 
                          rows="6"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group full-width">
                <label for="image_url">Product Image URL</label>
                <input type="url" id="image_url" name="image_url" class="form-control" 
                       value="<?php echo htmlspecialchars($product['image_url']); ?>"
                       placeholder="https://example.com/image.jpg">
                <div id="imagePreview" class="image-preview">
                    <?php if ($product['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="Product Image" 
                             onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\"fas fa-image\" style=\"font-size: 48px; color: var(--border-color);\"></i>';">
                    <?php else: ?>
                        <i class="fas fa-image" style="font-size: 48px; color: var(--border-color);"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="switch-label">
                        <span>Featured Product</span>
                        <label class="switch">
                            <input type="checkbox" name="is_featured" value="1" 
                                <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="switch-label">
                        <span>Active Status</span>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" 
                                <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="admin_products.php" class="btn" style="background: var(--border-color); color: var(--text-dark);">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <a href="admin_products_view.php?id=<?php echo $product_id; ?>" class="btn" style="background: var(--info-color); color: white;">
                    <i class="fas fa-eye"></i> View Product
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('image_url').addEventListener('input', function() {
    const preview = document.getElementById('imagePreview');
    if (this.value) {
        preview.innerHTML = `<img src="${this.value}" alt="Preview" onerror="this.onerror=null; this.src='';">`;
    } else {
        preview.innerHTML = '<i class="fas fa-image" style="font-size: 48px; color: var(--border-color);"></i>';
    }
});

// Form validation
document.getElementById('productForm').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    if (price <= 0) {
        e.preventDefault();
        alert('Price must be greater than 0');
        return false;
    }
    
    const category = document.getElementById('category_id').value;
    if (!category) {
        e.preventDefault();
        alert('Please select a category');
        return false;
    }
    
    return true;
});
</script>

<?php
require_once 'admin_footer.php';
?>