<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Add New Product';

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
    
    // Generate slug if not provided
    if (!empty($_POST['slug'])) {
        $data['slug'] = sanitizeInput($_POST['slug']);
    } else {
        $data['slug'] = generateSlug($data['name']);
    }
    
    // Validate required fields
    if (empty($data['name']) || empty($data['price']) || $data['price'] <= 0) {
        $error = 'Product name and valid price are required';
    } else {
        // Create product
        $product_id = createProduct($data);
        
        if ($product_id) {
            logAdminActivity($current_admin['id'], $current_admin['username'], 'ADD_PRODUCT', "Added product: {$data['name']}", $_SERVER['REMOTE_ADDR']);
            header("Location: admin_products.php?success=Product added successfully");
            exit();
        } else {
            $error = 'Failed to add product. Please try again.';
        }
    }
}

require_once 'admin_header.php';
?>

<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .form-header {
        padding: 25px 30px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .form-header h2 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-content {
        padding: 30px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .form-group label .required {
        color: var(--danger-color);
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--border-color);
        transition: .4s;
        border-radius: 30px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--success-color);
    }

    input:checked + .slider:before {
        transform: translateX(30px);
    }

    .switch-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .image-preview {
        width: 200px;
        height: 200px;
        border: 2px dashed var(--border-color);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 10px;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--light-color);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
    }

    .upload-btn:hover {
        background: var(--border-color);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .form-group.full-width {
            grid-column: 1;
        }
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h2>
            <i class="fas fa-plus"></i>
            Add New Product
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
                           placeholder="Enter product name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="slug">URL Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" 
                           placeholder="auto-generated-slug">
                    <small style="color: var(--text-color); display: block; margin-top: 5px;">
                        Leave blank to auto-generate from product name
                    </small>
                </div>

                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (Rs.) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" class="form-control" 
                           step="0.01" min="0" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" 
                           min="0" value="0">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-group full-width">
                <label for="image_url">Product Image URL</label>
                <input type="file" id="image_url" name="image_url" class="form-control" 
                       placeholder="https://example.com/image.jpg">
                </small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="switch-label">
                        <span>Featured Product</span>
                        <label class="switch">
                            <input type="checkbox" name="is_featured" value="1">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <small style="color: var(--text-color);">
                        Featured products appear on homepage
                    </small>
                </div>

                <div class="form-group">
                    <div class="switch-label">
                        <span>Active Status</span>
                        <label class="switch">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <small style="color: var(--text-color);">
                        Inactive products won't be visible to customers
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Product
                </button>
                <a href="admin_products.php" class="btn" style="background: var(--border-color); color: var(--text-dark);">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate slug from product name
document.getElementById('name').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    if (!slugField.value) {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        slugField.value = slug;
    }
});

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