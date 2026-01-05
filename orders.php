<?php
session_start();
require_once 'config.php';
require_once 'auth_functions.php';

$page_title = 'My Orders';
requireLogin($pdo);

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch user orders
$orders = getUserOrders($pdo, $user_id);

include 'header.php';
?>

<style>
.orders-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 60px 20px;
}

.page-title {
    text-align: center;
    margin-bottom: 40px;
}

.page-title h1 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: #4e4934;
    margin-bottom: 10px;
}

.page-title p {
    color: #635c55;
    font-size: 16px;
}

.profile-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 40px;
}

/* Profile sidebar styles */
.profile-sidebar {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    height: fit-content;
}

.profile-nav {
    display: flex;
    flex-direction: column;
}

.profile-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: #635c55;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.profile-nav a:hover,
.profile-nav a.active {
    background: #faf8f5;
    color: #4e4934;
}

/* Orders content */
.orders-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.no-orders {
    text-align: center;
    padding: 60px 20px;
    color: #635c55;
}

.no-orders img {
    width: 120px;
    opacity: 0.5;
    margin-bottom: 20px;
}

.no-orders h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #4e4934;
}

.shop-btn {
    display: inline-block;
    background: linear-gradient(45deg, #4e4934, #635c55);
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.shop-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(78, 73, 52, 0.2);
}

/* Orders list */
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.order-card {
    border: 1px solid #e8e4df;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.order-header {
    background: #faf8f5;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e8e4df;
}

.order-info {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.order-id {
    font-weight: 600;
    color: #4e4934;
}

.order-date {
    color: #635c55;
    font-size: 14px;
}

.order-status {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cce5ff;
    color: #004085;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

/* Order items */
.order-items {
    padding: 25px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: 500;
    color: #4e4934;
    margin-bottom: 5px;
}

.item-variant {
    color: #635c55;
    font-size: 14px;
}

.item-quantity {
    color: #635c55;
    font-size: 14px;
}

.item-price {
    font-weight: 600;
    color: #4e4934;
}

/* Order summary */
.order-summary {
    background: #faf8f5;
    padding: 20px 25px;
    border-top: 1px solid #e8e4df;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    color: #635c55;
    font-size: 14px;
}

.summary-row.total {
    font-weight: 600;
    color: #4e4934;
    font-size: 16px;
    border-top: 1px solid #e8e4df;
    padding-top: 15px;
    margin-top: 10px;
}

/* Order actions */
.order-actions {
    padding: 20px 25px;
    display: flex;
    gap: 15px;
    border-top: 1px solid #e8e4df;
}

.action-btn {
    padding: 10px 20px;
    border: 1px solid #e8e4df;
    background: white;
    border-radius: 6px;
    color: #635c55;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    border-color: #4e4934;
    color: #4e4934;
    background: #faf8f5;
}

.action-btn.primary {
    background: linear-gradient(45deg, #4e4934, #635c55);
    color: white;
    border: none;
}

.action-btn.primary:hover {
    box-shadow: 0 5px 15px rgba(78, 73, 52, 0.2);
}

/* Responsive */
@media (max-width: 992px) {
    .profile-layout {
        grid-template-columns: 1fr;
    }
    
    .profile-sidebar {
        order: 2;
    }
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .order-info {
        flex-direction: column;
        gap: 10px;
    }
    
    .order-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .item-details {
        width: 100%;
    }
    
    .order-actions {
        flex-wrap: wrap;
    }
}
</style>

<div class="orders-container">
    <div class="page-title">
        <h1>My Orders</h1>
        <p>Track and manage your orders</p>
    </div>
    
    <div class="profile-layout">
        <div class="profile-sidebar">
            <div class="profile-nav">
                <a href="profile.php"><i class="fas fa-user"></i> Personal Info</a>
                <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> My Orders</a>
                <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
            </div>
        </div>
        
        <div class="orders-content">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#4e4934" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <h3>No orders yet</h3>
                    <p>You haven't placed any orders with CHADET.</p>
                    <a href="products.php" class="shop-btn">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <!-- Order Header -->
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-id">Order #<?php echo $order['order_number']; ?></div>
                                    <div class="order-date">Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                                    <div class="order-total">Rs.<?php echo number_format($order['total_amount'], 2); ?></div>
                                </div>
                                <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="order-items">
                                <?php 
                                // Fetch order items for this order
                                $order_items = getOrderItems($pdo, $order['id']);
                                ?>
                                <?php foreach ($order_items as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                                        <div class="item-details">
                                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div class="item-variant">Color: <?php echo htmlspecialchars($item['variant'] ?? 'Standard'); ?></div>
                                        </div>
                                        <div class="item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                                        <div class="item-price">Rs.<?php echo number_format($item['price'], 2); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Subtotal:</span>
                                    <span>Rs.<?php echo number_format($order['subtotal'], 2); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Shipping:</span>
                                    <span><?php echo $order['shipping_fee'] > 0 ? 'Rs.' . number_format($order['shipping_fee'], 2) : 'FREE'; ?></span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total:</span>
                                    <span>Rs.<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                            </div>
                            
                            <!-- Order Actions -->
                            <div class="order-actions">
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="action-btn">View Details</a>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <a href="#" class="action-btn">Download Invoice</a>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
                                    <a href="#" class="action-btn primary">Track Order</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>