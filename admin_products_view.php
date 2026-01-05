<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'View Product';

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

// Get product variants and images if needed
$pdo = getDBConnection();
$variants = [];
$images = [];

try {
    // Get variants
    $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY type, name");
    $stmt->execute([$product_id]);
    $variants = $stmt->fetchAll();
    
    // Get images
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, is_primary DESC");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll();
    
    // Get order count for this product
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total_sold FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $sales_data = $stmt->fetch();
    
    // Get total revenue from this product
    $stmt = $pdo->prepare("SELECT SUM(total_price) as total_revenue FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $revenue_data = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Get product details error: " . $e->getMessage());
}

require_once 'admin_header.php';
?>

<style>
    .product-detail-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-header {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        padding: 30px;
        border-radius: 15px 15px 0 0;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .product-image-large {
        width: 150px;
        height: 150px;
        border-radius: 10px;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .no-image-large {
        width: 150px;
        height: 150px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.5);
        border: 4px solid rgba(255, 255, 255, 0.3);
        font-size: 40px;
    }

    .product-header-info h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
    }

    .product-header-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .product-content {
        background: white;
        border-radius: 0 0 15px 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .product-tabs {
        display: flex;
        background: var(--light-color);
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 15px 25px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 500;
        color: var(--text-color);
        cursor: pointer;
        transition: var(--transition);
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }

    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.5);
        color: var(--primary-color);
    }

    .tab-btn.active {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background: white;
    }

    .tab-content {
        padding: 30px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .info-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 25px;
        transition: var(--transition);
    }

    .info-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .info-card h3 {
        margin: 0 0 20px 0;
        color: var(--text-dark);
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--light-color);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--light-color);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: var(--text-color);
        font-weight: 500;
    }

    .info-value {
        color: var(--text-dark);
        font-weight: 600;
        text-align: right;
    }

    .badge-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: rgba(46, 204, 113, 0.1);
        color: var(--success-color);
    }

    .status-inactive {
        background: rgba(149, 165, 166, 0.1);
        color: #95a5a6;
    }

    .status-featured {
        background: rgba(241, 196, 15, 0.1);
        color: var(--warning-color);
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .stock-high { background: #d4edda; color: #155724; }
    .stock-medium { background: #fff3cd; color: #856404; }
    .stock-low { background: #f8d7da; color: #721c24; }
    .stock-out { background: #f8f9fa; color: #6c757d; }

    .variants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .variant-item {
        background: var(--light-color);
        padding: 15px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .variant-item h4 {
        margin: 0 0 10px 0;
        color: var(--text-dark);
        font-size: 14px;
    }

    .variant-details {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-color);
    }

    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .image-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .image-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: var(--primary-color);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .product-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .product-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .variants-grid,
        .images-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .variants-grid,
        .images-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons a {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="product-detail-container">
    <div class="product-header">
        <?php if ($product['image_url']): ?>
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="product-image-large">
        <?php else: ?>
            <div class="no-image-large">
                <i class="fas fa-box"></i>
            </div>
        <?php endif; ?>
        
        <div class="product-header-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p><i class="fas fa-tag"></i> #<?php echo $product['id']; ?> • <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
            <p style="margin-top: 10px; font-size: 14px; opacity: 0.8;">
                <span class="badge-status <?php echo $product['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                </span>
                <?php if ($product['is_featured']): ?>
                    <span class="badge-status status-featured" style="margin-left: 10px;">
                        <i class="fas fa-star"></i> Featured
                    </span>
                <?php endif; ?>
                <span style="margin-left: 10px; opacity: 0.8;">
                    <i class="fas fa-calendar-alt"></i> 
                    Added <?php echo date('F d, Y', strtotime($product['created_at'])); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="product-content">
        <div class="product-tabs">
            <button class="tab-btn active" onclick="showTab('overview')">
                <i class="fas fa-info-circle"></i> Overview
            </button>
            <button class="tab-btn" onclick="showTab('inventory')">
                <i class="fas fa-warehouse"></i> Inventory
            </button>
            <?php if (!empty($variants)): ?>
                <button class="tab-btn" onclick="showTab('variants')">
                    <i class="fas fa-palette"></i> Variants (<?php echo count($variants); ?>)
                </button>
            <?php endif; ?>
            <?php if (!empty($images)): ?>
                <button class="tab-btn" onclick="showTab('images')">
                    <i class="fas fa-images"></i> Images (<?php echo count($images); ?>)
                </button>
            <?php endif; ?>
            <button class="tab-btn" onclick="showTab('sales')">
                <i class="fas fa-chart-line"></i> Sales Data
            </button>
        </div>

        <div id="overviewTab" class="tab-content">
            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fas fa-tag"></i> Product Information</h3>
                    <div class="info-item">
                        <span class="info-label">Product ID</span>
                        <span class="info-value">#<?php echo $product['id']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($product['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Slug</span>
                        <span class="info-value"><?php echo htmlspecialchars($product['slug']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category</span>
                        <span class="info-value"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Price</span>
                        <span class="info-value" style="color: var(--primary-color); font-size: 18px;">
                            $<?php echo number_format($product['price'], 2); ?>
                        </span>
                    </div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-cogs"></i> Product Status</h3>
                    <div class="info-item">
                        <span class="info-label">Active Status</span>
                        <span class="info-value">
                            <span class="badge-status <?php echo $product['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Featured</span>
                        <span class="info-value">
                            <?php if ($product['is_featured']): ?>
                                <span class="badge-status status-featured">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-color);">No</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value"><?php echo date('F d, Y', strtotime($product['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Updated</span>
                        <span class="info-value">
                            <?php echo isset($product['updated_at']) && $product['updated_at'] != $product['created_at'] 
                                ? date('F d, Y', strtotime($product['updated_at'])) 
                                : 'Never'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (!empty($product['description'])): ?>
                <div class="info-card" style="margin-top: 25px;">
                    <h3><i class="fas fa-file-alt"></i> Description</h3>
                    <div style="padding: 15px; background: var(--light-color); border-radius: 8px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div id="inventoryTab" class="tab-content" style="display: none;">
            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fas fa-boxes"></i> Stock Information</h3>
                    <div class="info-item">
                        <span class="info-label">Current Stock</span>
                        <span class="info-value">
                            <?php
                            $stock_class = 'stock-high';
                            if ($product['stock_quantity'] == 0) {
                                $stock_class = 'stock-out';
                            } elseif ($product['stock_quantity'] <= 10) {
                                $stock_class = 'stock-low';
                            } elseif ($product['stock_quantity'] <= 25) {
                                $stock_class = 'stock-medium';
                            }
                            ?>
                            <span class="stock-badge <?php echo $stock_class; ?>">
                                <?php if ($stock_class == 'stock-low'): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                <?php elseif ($stock_class == 'stock-out'): ?>
                                    <i class="fas fa-times-circle"></i>
                                <?php else: ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php endif; ?>
                                <?php echo number_format($product['stock_quantity']); ?> units
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Stock Status</span>
                        <span class="info-value">
                            <?php if ($product['stock_quantity'] > 25): ?>
                                <span style="color: var(--success-color); font-weight: 600;">In Stock</span>
                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                <span style="color: var(--warning-color); font-weight: 600;">Low Stock</span>
                            <?php else: ?>
                                <span style="color: var(--danger-color); font-weight: 600;">Out of Stock</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($variants)): ?>
        <div id="variantsTab" class="tab-content" style="display: none;">
            <div class="info-card">
                <h3><i class="fas fa-palette"></i> Product Variants (<?php echo count($variants); ?>)</h3>
                <div class="variants-grid">
                    <?php foreach ($variants as $variant): ?>
                        <div class="variant-item">
                            <h4><?php echo htmlspecialchars($variant['name']); ?></h4>
                            <div class="variant-details">
                                <span>Type: <?php echo ucfirst($variant['type']); ?></span>
                                <span>Value: <?php echo htmlspecialchars($variant['value']); ?></span>
                            </div>
                            <div class="variant-details" style="margin-top: 5px;">
                                <span>Stock: <?php echo $variant['stock_quantity']; ?></span>
                                <span>
                                    <?php if ($variant['price_adjustment'] != 0): ?>
                                        <?php echo $variant['price_adjustment'] > 0 ? '+' : ''; ?>
                                        $<?php echo number_format($variant['price_adjustment'], 2); ?>
                                    <?php else: ?>
                                        Base Price
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($images)): ?>
        <div id="imagesTab" class="tab-content" style="display: none;">
            <div class="info-card">
                <h3><i class="fas fa-images"></i> Product Images (<?php echo count($images); ?>)</h3>
                <div class="images-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-item">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Product Image'); ?>">
                            <?php if ($image['is_primary']): ?>
                                <span class="image-badge">Primary</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div id="salesTab" class="tab-content" style="display: none;">
            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fas fa-chart-line"></i> Sales Performance</h3>
                    <div class="info-item">
                        <span class="info-label">Total Units Sold</span>
                        <span class="info-value"><?php echo number_format($sales_data['total_sold'] ?? 0); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Revenue</span>
                        <span class="info-value" style="color: var(--success-color);">
                            $<?php echo number_format($revenue_data['total_revenue'] ?? 0, 2); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="admin_products_edit.php?id=<?php echo $product['id']; ?>" class="btn" style="background: var(--warning-color); color: white;">
                <i class="fas fa-edit"></i> Edit Product
            </a>
            <a href="admin_products.php" class="btn" style="background: var(--border-color); color: var(--text-dark);">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
            <a href="admin_products.php?action=toggle_status&id=<?php echo $product['id']; ?>" 
               class="btn" style="background: <?php echo $product['is_active'] ? 'var(--secondary-color)' : 'var(--success-color)'; ?>; color: white;"
               onclick="return confirm('Are you sure you want to <?php echo $product['is_active'] ? 'deactivate' : 'activate'; ?> this product?')">
                <i class="fas fa-<?php echo $product['is_active'] ? 'times' : 'check'; ?>"></i>
                <?php echo $product['is_active'] ? 'Deactivate' : 'Activate'; ?> Product
            </a>
            <?php if (hasPermission('admin')): ?>
                <a href="admin_products.php?action=delete&id=<?php echo $product['id']; ?>" 
                   class="btn" style="background: var(--danger-color); color: white;"
                   onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                    <i class="fas fa-trash"></i> Delete Product
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').style.display = 'block';
    
    // Add active class to clicked button
    event.target.classList.add('active');
}
</script>

<?php
require_once 'admin_footer.php';
?>