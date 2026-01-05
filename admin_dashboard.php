<?php
$page_title = 'Dashboard';
require_once 'admin_header.php';

// Get dashboard statistics
$stats = getDashboardStats();

// Get recent data
$pdo = getDBConnection();

// Recent orders
$recent_orders = [];
try {
    $stmt = $pdo->query("SELECT o.*, c.first_name, c.last_name 
                        FROM orders o 
                        JOIN customers c ON o.customer_id = c.id 
                        ORDER BY o.created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Recent orders error: " . $e->getMessage());
}

// Recent customers
$recent_customers = [];
try {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC LIMIT 5");
    $recent_customers = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Recent customers error: " . $e->getMessage());
}

// Low stock products
$low_stock = [];
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_quantity <= 10 AND is_active = TRUE ORDER BY stock_quantity ASC LIMIT 5");
    $low_stock = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Low stock error: " . $e->getMessage());
}

// Recent reviews
$recent_reviews = [];
try {
    $stmt = $pdo->query("SELECT r.*, p.name as product_name, c.first_name, c.last_name 
                        FROM product_reviews r 
                        JOIN products p ON r.product_id = p.id 
                        JOIN customers c ON r.customer_id = c.id 
                        ORDER BY r.created_at DESC LIMIT 5");
    $recent_reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    // Table might not exist yet
}
?>
<style>
    /* Dashboard Specific Styles */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: var(--shadow);
        border-left: 4px solid var(--primary-color);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(78, 73, 52, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: rgba(78, 73, 52, 0.05);
        border-radius: 0 0 0 100%;
    }

    .stat-card.customers {
        border-left-color: var(--success-color);
    }

    .stat-card.products {
        border-left-color: var(--info-color);
    }

    .stat-card.orders {
        border-left-color: var(--warning-color);
    }

    .stat-card.revenue {
        border-left-color: var(--primary-color);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .stat-card.customers .stat-icon {
        background: linear-gradient(45deg, var(--success-color), #218838);
    }

    .stat-card.products .stat-icon {
        background: linear-gradient(45deg, var(--info-color), #138496);
    }

    .stat-card.orders .stat-icon {
        background: linear-gradient(45deg, var(--warning-color), #e0a800);
    }

    .stat-card.revenue .stat-icon {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 5px;
        line-height: 1;
    }

    .stat-label {
        font-size: 14px;
        color: var(--text-color);
        margin-bottom: 10px;
        font-weight: 500;
    }

    .stat-change {
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-change.positive {
        color: var(--success-color);
    }

    .stat-change.negative {
        color: var(--danger-color);
    }

    .dashboard-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .dashboard-section {
        background: white;
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: var(--transition);
    }

    .dashboard-section:hover {
        box-shadow: 0 10px 25px rgba(78, 73, 52, 0.15);
    }

    .section-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(90deg, var(--light-color), white);
    }

    .section-header h3 {
        font-size: 18px;
        color: var(--text-dark);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header a {
        font-size: 14px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .section-header a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .section-content {
        padding: 20px;
        max-height: 300px;
        overflow-y: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        text-align: left;
        padding: 10px 0;
        border-bottom: 2px solid var(--border-color);
        color: var(--text-color);
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover td {
        background: var(--light-color);
    }

    .customer-name {
        font-weight: 500;
        color: var(--text-dark);
    }

    .customer-email {
        font-size: 12px;
        color: var(--text-color);
    }

    .product-name {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .stock-warning {
        color: var(--danger-color);
        font-weight: 500;
        font-size: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-color);
    }

    .empty-state i {
        font-size: 40px;
        margin-bottom: 15px;
        color: var(--border-color);
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 14px;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 30px;
    }

    .quick-action {
        background: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        box-shadow: var(--shadow);
        text-decoration: none;
        color: var(--text-dark);
        transition: var(--transition);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
    }

    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(78, 73, 52, 0.15);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .quick-action i {
        font-size: 28px;
        margin-bottom: 12px;
        color: var(--primary-color);
    }

    .quick-action h4 {
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .quick-action p {
        font-size: 12px;
        color: var(--text-color);
    }

    /* Rating stars */
    .rating-stars {
        color: #ffc107;
        font-size: 12px;
    }

    .review-text {
        font-size: 13px;
        color: var(--text-color);
        margin-top: 5px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar */
    .section-content::-webkit-scrollbar {
        width: 6px;
    }

    .section-content::-webkit-scrollbar-track {
        background: var(--light-color);
        border-radius: 3px;
    }

    .section-content::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    .section-content::-webkit-scrollbar-thumb:hover {
        background: var(--text-color);
    }

    @media (max-width: 768px) {
        .dashboard-sections {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .data-table {
            font-size: 13px;
        }
        
        .data-table th,
        .data-table td {
            padding: 8px 0;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="stats-grid">
    <div class="stat-card customers">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total_customers']); ?></div>
        <div class="stat-label">Total Customers</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 
            <?php echo number_format($stats['new_customers_today']); ?> new today
        </div>
    </div>
    
    <div class="stat-card products">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
        <div class="stat-label">Total Products</div>
        <div class="stat-change <?php echo $stats['low_stock'] > 0 ? 'negative' : 'positive'; ?>">
            <i class="fas fa-<?php echo $stats['low_stock'] > 0 ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
            <?php echo $stats['low_stock']; ?> low stock
        </div>
    </div>
    
    <div class="stat-card orders">
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
        <div class="stat-label">Total Orders</div>
        <div class="stat-change <?php echo $stats['pending_orders'] > 0 ? 'negative' : 'positive'; ?>">
            <i class="fas fa-<?php echo $stats['pending_orders'] > 0 ? 'clock' : 'check'; ?>"></i>
            <?php echo $stats['pending_orders']; ?> pending
        </div>
    </div>
    
    <div class="stat-card revenue">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-value">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            $<?php echo number_format($stats['today_revenue'], 2); ?> today
        </div>
    </div>
</div>

<div class="dashboard-sections">
    <div class="dashboard-section">
        <div class="section-header">
            <h3><i class="fas fa-shopping-cart"></i> Recent Orders</h3>
            <a href="admin_orders.php">View All</a>
        </div>
        <div class="section-content">
            <?php if (!empty($recent_orders)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_number']; ?></td>
                                <td>
                                    <div class="customer-name"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></div>
                                    <div class="customer-email"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php echo getStatusBadge($order['status'], 'order'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No recent orders</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dashboard-section">
        <div class="section-header">
            <h3><i class="fas fa-users"></i> Recent Customers</h3>
            <a href="admin_customers.php">View All</a>
        </div>
        <div class="section-content">
            <?php if (!empty($recent_customers)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="customer-name"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo date('M d', strtotime($customer['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user"></i>
                    <p>No recent customers</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="dashboard-sections">
    <div class="dashboard-section">
        <div class="section-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h3>
            <a href="admin_products.php">View All</a>
        </div>
        <div class="section-content">
            <?php if (!empty($low_stock)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                </td>
                                <td>
                                    <span class="stock-warning"><?php echo $product['stock_quantity']; ?> left</span>
                                </td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php echo getStatusBadge($product['is_active'] ? 'active' : 'inactive'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>All products are well stocked</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($recent_reviews)): ?>
    <div class="dashboard-section">
        <div class="section-header">
            <h3><i class="fas fa-star"></i> Recent Reviews</h3>
            <a href="admin_reviews.php">View All</a>
        </div>
        <div class="section-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_reviews as $review): ?>
                        <tr>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($review['product_name']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></td>
                            <td>
                                <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-empty'; ?>"></i>
                                    <?php endfor; ?>
                                    <span style="color: var(--text-color); font-size: 11px; margin-left: 5px;">
                                        (<?php echo $review['rating']; ?>.0)
                                    </span>
                                </div>
                                <?php if (!empty($review['title'])): ?>
                                    <div class="review-text">"<?php echo htmlspecialchars($review['title']); ?>"</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d', strtotime($review['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="quick-actions">
    <a href="admin_products_add.php" class="quick-action">
        <i class="fas fa-plus-circle"></i>
        <h4>Add New Product</h4>
        <p>Create new product listing</p>
    </a>
    
    <a href="admin_orders.php" class="quick-action">
        <i class="fas fa-shopping-cart"></i>
        <h4>Manage Orders</h4>
        <p>View and process orders</p>
    </a>
    
    <a href="admin_customers_add.php" class="quick-action">
        <i class="fas fa-user-plus"></i>
        <h4>Add Customer</h4>
        <p>Create new customer account</p>
    </a>
</div>

<?php
require_once 'admin_footer.php';
?>