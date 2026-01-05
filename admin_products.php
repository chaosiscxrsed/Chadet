<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Manage Products';

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : '';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle delete action
if ($action === 'delete' && $product_id && hasPermission('admin')) {
    if (deleteProduct($product_id)) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'DELETE_PRODUCT', "Deleted product ID: $product_id", $_SERVER['REMOTE_ADDR']);
        header("Location: admin_products.php?success=Product deleted successfully");
        exit();
    } else {
        header("Location: admin_products.php?error=Failed to delete product");
        exit();
    }
}

// Handle toggle status
if ($action === 'toggle_status' && $product_id) {
    $product = getProductById($product_id);
    if ($product) {
        $new_status = $product['is_active'] ? 0 : 1;
        if (updateProduct($product_id, ['is_active' => $new_status])) {
            $status_text = $new_status ? 'activated' : 'deactivated';
            logAdminActivity($current_admin['id'], $current_admin['username'], 'UPDATE_PRODUCT', "Product $status_text: {$product['name']}", $_SERVER['REMOTE_ADDR']);
            header("Location: admin_products.php?success=Product $status_text successfully");
            exit();
        }
    }
    header("Location: admin_products.php?error=Failed to update product status");
    exit();
}

// Get products with pagination - USING SIMPLE VERSION FIRST
$products_data = getProductsSimple($page, 10, $search, $category);
$products = $products_data['data'];
$total_pages = $products_data['pages'];

// Get categories for filter
$categories = getCategories();

// Get statistics
$pdo = getDBConnection();
$total_products = $pdo->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
$active_products = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = TRUE")->fetch()['count'];
$featured_products = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_featured = TRUE AND is_active = TRUE")->fetch()['count'];
$out_of_stock = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0 AND is_active = TRUE")->fetch()['count'];

require_once 'admin_header.php';
?>

<style>
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        border-left: 4px solid #4e4934;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .stat-item h4 {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-item .count {
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    .filter-actions {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-form {
        display: flex;
        gap: 10px;
        flex: 1;
    }

    .search-box {
        padding: 10px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        flex: 1;
        min-width: 300px;
        transition: all 0.3s ease;
    }

    .search-box:focus {
        outline: none;
        border-color: #4e4934;
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    .btn-search {
        padding: 10px 20px;
        background: #4e4934;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        background: #3a3627;
        transform: translateY(-2px);
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #4e4934;
        color: white;
    }

    .btn-primary:hover {
        background: #3a3627;
        transform: translateY(-2px);
    }

    .select-box {
        padding: 10px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        min-width: 200px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .select-box:focus {
        outline: none;
        border-color: #4e4934;
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-top: 20px;
    }

    .table-header {
        padding: 20px;
        background: linear-gradient(90deg, #f8f9fa, white);
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        color: #333;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #eee;
        white-space: nowrap;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .table tr:hover {
        background: rgba(78, 73, 52, 0.02);
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #eee;
    }

    .no-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        border: 1px solid #eee;
    }

    .stock-indicator {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .stock-high {
        background: #d4edda;
        color: #155724;
    }

    .stock-medium {
        background: #fff3cd;
        color: #856404;
    }

    .stock-low {
        background: #f8d7da;
        color: #721c24;
    }

    .stock-out {
        background: #f8f9fa;
        color: #6c757d;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    .status-active {
        background: #28a745;
        color: white;
    }

    .status-inactive {
        background: #6c757d;
        color: white;
    }

    .featured-star {
        font-size: 18px;
        color: #ffc107;
    }

    .product-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-btn.view {
        background: #17a2b8;
        color: white;
    }

    .action-btn.edit {
        background: #ffc107;
        color: white;
    }

    .action-btn.delete {
        background: #dc3545;
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 60px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: #666;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .empty-state p {
        color: #666;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px;
        gap: 10px;
    }

    .pagination a {
        padding: 8px 12px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #4e4934;
        color: white;
        border-color: #4e4934;
    }

    .pagination .active {
        background: #4e4934;
        color: white;
        border-color: #4e4934;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-group label {
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .stats-summary {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filter-actions {
            flex-direction: column;
        }
        
        .search-form {
            width: 100%;
        }
        
        .search-box {
            min-width: auto;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            min-width: 800px;
        }
    }

    @media (max-width: 480px) {
        .stats-summary {
            grid-template-columns: 1fr;
        }
        
        .product-actions {
            flex-direction: column;
        }
    }
</style>

<div class="stats-summary">
    <div class="stat-item">
        <h4>Total Products</h4>
        <div class="count"><?php echo number_format($total_products); ?></div>
    </div>
    <div class="stat-item">
        <h4>Active Products</h4>
        <div class="count"><?php echo number_format($active_products); ?></div>
    </div>
    <div class="stat-item">
        <h4>Featured</h4>
        <div class="count"><?php echo number_format($featured_products); ?></div>
    </div>
    <div class="stat-item">
        <h4>Out of Stock</h4>
        <div class="count"><?php echo number_format($out_of_stock); ?></div>
    </div>
</div>

<div class="filter-actions">

    <div class="filter-group">
        <label><i class="fas fa-filter"></i> Category:</label>
        <select class="select-box" onchange="if(this.value) window.location.href='admin_products.php?category=' + this.value">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['slug']); ?>" <?php echo $category == $cat['slug'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <a href="admin_products_add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <div class="table-header">
        <h3>
            <i class="fas fa-box"></i> 
            Products 
            <span style="font-size: 14px; color: #666; font-weight: normal;">
                (<?php echo $products_data['total']; ?> total)
            </span>
        </h3>
        <?php if ($search): ?>
            <div style="font-size: 14px; color: #4e4934;">
                <i class="fas fa-search"></i> Search results for: "<?php echo htmlspecialchars($search); ?>"
            </div>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <?php if (!empty($products)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    <div style="font-weight: 500; color: #333; margin-bottom: 5px;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">
                                        <?php 
                                        $desc = strip_tags($product['description'] ?? '');
                                        echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                        ?>
                                    </div>
                                    <div style="font-size: 11px; color: #999;">
                                        SKU: #<?php echo $product['id']; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($product['category_name'])): ?>
                                    <span style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">Uncategorized</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #333;">
                                    $<?php echo number_format($product['price'] ?? 0, 2); ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $stock_quantity = $product['stock_quantity'] ?? 0;
                                $stock_class = 'stock-high';
                                $stock_text = $stock_quantity . ' in stock';
                                
                                if ($stock_quantity == 0) {
                                    $stock_class = 'stock-out';
                                    $stock_text = 'Out of stock';
                                } elseif ($stock_quantity <= 10) {
                                    $stock_class = 'stock-low';
                                    $stock_text = $stock_quantity . ' left (Low)';
                                } elseif ($stock_quantity <= 25) {
                                    $stock_class = 'stock-medium';
                                    $stock_text = $stock_quantity . ' left';
                                }
                                ?>
                                <span class="stock-indicator <?php echo $stock_class; ?>">
                                    <?php if ($stock_class == 'stock-low'): ?>
                                        <i class="fas fa-exclamation-triangle"></i>
                                    <?php elseif ($stock_class == 'stock-out'): ?>
                                        <i class="fas fa-times-circle"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle"></i>
                                    <?php endif; ?>
                                    <?php echo $stock_text; ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_products.php?action=toggle_status&id=<?php echo $product['id']; ?>" 
                                   class="status-badge <?php echo ($product['is_active'] ?? 0) ? 'status-active' : 'status-inactive'; ?>"
                                   onclick="return confirm('Are you sure you want to <?php echo ($product['is_active'] ?? 0) ? 'deactivate' : 'activate'; ?> this product?')"
                                   title="<?php echo ($product['is_active'] ?? 0) ? 'Deactivate' : 'Activate'; ?>">
                                    <i class="fas fa-<?php echo ($product['is_active'] ?? 0) ? 'check' : 'times'; ?>"></i>
                                    <?php echo ($product['is_active'] ?? 0) ? 'Active' : 'Inactive'; ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($product['is_featured'] ?? 0): ?>
                                    <i class="fas fa-star featured-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star" style="color: #ddd;"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="product-actions">
                                    <a href="admin_products_view.php?id=<?php echo $product['id']; ?>" class="action-btn view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="admin_products_edit.php?id=<?php echo $product['id']; ?>" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (hasPermission('admin')): ?>
                                        <a href="admin_products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                           class="action-btn delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box"></i>
                <h4>No Products Found</h4>
                <p><?php echo $search ? 'No products match your search criteria.' : 'No products in the database yet.'; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="admin_products.php?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <a href="#" class="active"><?php echo $i; ?></a>
                <?php elseif ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="admin_products.php?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <span>...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="admin_products.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'admin_footer.php';
?>