<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Manage Orders';

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '';
$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : '';
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle status update action
if ($action === 'update_status' && $order_id && isset($_GET['new_status'])) {
    $new_status = sanitizeInput($_GET['new_status']);
    if (updateOrderStatus($order_id, $new_status)) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'UPDATE_ORDER_STATUS', "Updated order #$order_id status to $new_status", $_SERVER['REMOTE_ADDR']);
        header("Location: admin_orders.php?success=Order status updated successfully");
        exit();
    } else {
        header("Location: admin_orders.php?error=Failed to update order status");
        exit();
    }
}

// Handle delete action
if ($action === 'delete' && $order_id && hasPermission('admin')) {
    if (deleteOrder($order_id)) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'DELETE_ORDER', "Deleted order ID: $order_id", $_SERVER['REMOTE_ADDR']);
        header("Location: admin_orders.php?success=Order deleted successfully");
        exit();
    } else {
        header("Location: admin_orders.php?error=Failed to delete order");
        exit();
    }
}

// Get orders with pagination
$orders_data = getOrders($page, 15, $search, $status, $date_from, $date_to);
$orders = $orders_data['data'];
$total_pages = $orders_data['pages'];

// Get order statistics
$order_stats = getOrderStats();

require_once 'admin_header.php';
?>

<style>
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        text-align: center;
        border-left: 4px solid var(--primary-color);
        transition: var(--transition);
        cursor: pointer;
    }

    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .stat-item h4 {
        font-size: 12px;
        color: var(--text-color);
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-item .count {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .stat-item.pending { border-left-color: var(--warning-color); }
    .stat-item.processing { border-left-color: var(--info-color); }
    .stat-item.shipped { border-left-color: var(--primary-color); }
    .stat-item.delivered { border-left-color: var(--success-color); }
    .stat-item.cancelled { border-left-color: var(--danger-color); }

    .advanced-filters {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-group label {
        font-weight: 500;
        color: var(--text-dark);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .filter-control {
        padding: 10px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background: white;
        transition: var(--transition);
    }

    .filter-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning-color);
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-processing {
        background: rgba(23, 162, 184, 0.1);
        color: var(--info-color);
        border: 1px solid rgba(23, 162, 184, 0.3);
    }

    .status-shipped {
        background: rgba(78, 73, 52, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(78, 73, 52, 0.3);
    }

    .status-delivered {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-cancelled {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .payment-pending { background: #fff3cd; color: #856404; }
    .payment-paid { background: #d4edda; color: #155724; }
    .payment-failed { background: #f8d7da; color: #721c24; }
    .payment-refunded { background: #e2e3e5; color: #383d41; }

    .customer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, var(--accent-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    .customer-details h5 {
        margin: 0;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 14px;
    }

    .customer-details small {
        color: var(--text-color);
        font-size: 12px;
    }

    .order-items-preview {
        font-size: 12px;
        color: var(--text-color);
        max-width: 200px;
    }

    .order-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-dropdown {
        position: relative;
        display: inline-block;
    }

    .action-dropdown-btn {
        padding: 6px 12px;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-color);
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-dropdown-btn:hover {
        background: var(--light-color);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .action-dropdown-content {
        display: none;
        position: absolute;
        background: white;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-radius: 8px;
        z-index: 1000;
        padding: 5px 0;
        border: 1px solid var(--border-color);
        right: 0;
    }

    .action-dropdown-content a {
        display: block;
        padding: 8px 12px;
        text-decoration: none;
        color: var(--text-dark);
        font-size: 12px;
        transition: var(--transition);
    }

    .action-dropdown-content a:hover {
        background: var(--light-color);
        color: var(--primary-color);
    }

    .action-dropdown:hover .action-dropdown-content {
        display: block;
    }

    .quick-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 8px 15px;
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-color);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-action-btn:hover {
        background: var(--light-color);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .quick-action-btn.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .export-btn {
        padding: 8px 15px;
        background: var(--success-color);
        border: none;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .export-btn:hover {
        background: #218838;
        transform: translateY(-2px);
    }

    .total-amount {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 16px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 60px;
        color: var(--border-color);
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h4 {
        color: var(--text-color);
        margin-bottom: 10px;
        font-weight: 500;
    }

    .empty-state p {
        color: var(--text-color);
        font-size: 14px;
        margin-bottom: 20px;
    }

    .date-range {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .stats-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-row {
            grid-template-columns: 1fr;
        }

        .date-range {
            flex-direction: column;
        }

        .quick-actions {
            flex-direction: column;
        }

        .order-actions {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .stats-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="stats-summary">
    <div class="stat-item pending" onclick="applyFilter('pending')">
        <h4>Pending</h4>
        <div class="count"><?php echo number_format($order_stats['pending']); ?></div>
    </div>
    <div class="stat-item processing" onclick="applyFilter('processing')">
        <h4>Processing</h4>
        <div class="count"><?php echo number_format($order_stats['processing']); ?></div>
    </div>
    <div class="stat-item shipped" onclick="applyFilter('shipped')">
        <h4>Shipped</h4>
        <div class="count"><?php echo number_format($order_stats['shipped']); ?></div>
    </div>
    <div class="stat-item delivered" onclick="applyFilter('delivered')">
        <h4>Delivered</h4>
        <div class="count"><?php echo number_format($order_stats['delivered']); ?></div>
    </div>
    <div class="stat-item cancelled" onclick="applyFilter('cancelled')">
        <h4>Cancelled</h4>
        <div class="count"><?php echo number_format($order_stats['cancelled']); ?></div>
    </div>
</div>

<div class="quick-actions">
    <a href="javascript:void(0)" onclick="applyFilter('')" class="quick-action-btn <?php echo !$status ? 'active' : ''; ?>">
        <i class="fas fa-list"></i> All Orders
    </a>
    <a href="javascript:void(0)" onclick="applyFilter('pending')" class="quick-action-btn <?php echo $status == 'pending' ? 'active' : ''; ?>">
        <i class="fas fa-clock"></i> Pending
    </a>
    <a href="javascript:void(0)" onclick="applyFilter('processing')" class="quick-action-btn <?php echo $status == 'processing' ? 'active' : ''; ?>">
        <i class="fas fa-cog"></i> Processing
    </a>
    <a href="javascript:void(0)" onclick="applyFilter('shipped')" class="quick-action-btn <?php echo $status == 'shipped' ? 'active' : ''; ?>">
        <i class="fas fa-shipping-fast"></i> Shipped
    </a>
    <a href="javascript:void(0)" onclick="applyFilter('delivered')" class="quick-action-btn <?php echo $status == 'delivered' ? 'active' : ''; ?>">
        <i class="fas fa-check-circle"></i> Delivered
    </a>
    <a href="javascript:void(0)" onclick="exportOrders('csv')" class="export-btn">
        <i class="fas fa-download"></i> Export Orders
    </a>
</div>

<div class="advanced-filters">
    <form method="GET" action="" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Status Filter</label>
                <select name="status" class="filter-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $status == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $status == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Date Range</label>
                <div class="date-range">
                    <input type="date" name="date_from" class="filter-control" 
                           value="<?php echo htmlspecialchars($date_from); ?>" 
                           placeholder="From Date">
                    <span>to</span>
                    <input type="date" name="date_to" class="filter-control" 
                           value="<?php echo htmlspecialchars($date_to); ?>" 
                           placeholder="To Date">
                </div>
            </div>

            <div class="filter-group" style="align-items: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </div>
    </form>
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
            <i class="fas fa-shopping-cart"></i> 
            Orders 
            <span style="font-size: 14px; color: var(--text-color); font-weight: normal;">
                (<?php echo $orders_data['total']; ?> orders found)
            </span>
        </h3>
        <?php if ($search || $status || $date_from || $date_to): ?>
            <div style="font-size: 14px; color: var(--primary-color);">
                <i class="fas fa-filter"></i> 
                <?php
                $filters = [];
                if ($search) $filters[] = "Search: \"$search\"";
                if ($status) $filters[] = "Status: " . ucfirst($status);
                if ($date_from || $date_to) {
                    $date_filter = "Date: ";
                    if ($date_from) $date_filter .= "From $date_from ";
                    if ($date_to) $date_filter .= "To $date_to";
                    $filters[] = $date_filter;
                }
                echo implode(' • ', $filters);
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="table-content">
        <?php if (!empty($orders)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary-color);">#<?php echo $order['order_number']; ?></strong>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <?php echo strtoupper(substr($order['first_name'] ?? 'C', 0, 1)); ?>
                                    </div>
                                    <div class="customer-details">
                                        <h5><?php echo htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')); ?></h5>
                                        <small><?php echo htmlspecialchars($order['email'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="order-items-preview">
                                    <?php
                                    $items = getOrderItems($order['id']);
                                    $item_count = count($items);
                                    if ($item_count > 0) {
                                        $first_item = $items[0];
                                        echo $first_item['quantity'] . ' × ' . htmlspecialchars($first_item['product_name'] ?? 'Item');
                                        if ($item_count > 1) {
                                            echo ' + ' . ($item_count - 1) . ' more';
                                        }
                                    } else {
                                        echo 'No items';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?><br>
                                <small style="color: var(--text-color);">
                                    <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                                </small>
                            </td>
                            <td class="total-amount">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <i class="fas fa-<?php echo getStatusIcon($order['status']); ?>"></i>
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="payment-badge payment-<?php echo $order['payment_status']; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="order-actions">
                                    <a href="admin_orders_view.php?id=<?php echo $order['id']; ?>" class="action-btn view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="admin_orders_edit.php?id=<?php echo $order['id']; ?>" class="action-btn edit" title="Edit Order">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <div class="action-dropdown">
                                        <button class="action-dropdown-btn">
                                            <i class="fas fa-cog"></i> Status
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="action-dropdown-content">
                                            <a href="admin_orders.php?action=update_status&id=<?php echo $order['id']; ?>&new_status=pending">
                                                <i class="fas fa-clock"></i> Mark as Pending
                                            </a>
                                            <a href="admin_orders.php?action=update_status&id=<?php echo $order['id']; ?>&new_status=processing">
                                                <i class="fas fa-cog"></i> Mark as Processing
                                            </a>
                                            <a href="admin_orders.php?action=update_status&id=<?php echo $order['id']; ?>&new_status=shipped">
                                                <i class="fas fa-shipping-fast"></i> Mark as Shipped
                                            </a>
                                            <a href="admin_orders.php?action=update_status&id=<?php echo $order['id']; ?>&new_status=delivered">
                                                <i class="fas fa-check-circle"></i> Mark as Delivered
                                            </a>
                                            <a href="admin_orders.php?action=update_status&id=<?php echo $order['id']; ?>&new_status=cancelled">
                                                <i class="fas fa-times-circle"></i> Mark as Cancelled
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <?php if (hasPermission('admin')): ?>
                                        <a href="admin_orders.php?action=delete&id=<?php echo $order['id']; ?>" 
                                           class="action-btn delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
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
                <i class="fas fa-shopping-cart"></i>
                <h4>No Orders Found</h4>
                <p>
                    <?php 
                    if ($search || $status || $date_from || $date_to) {
                        echo 'No orders match your filter criteria.';
                    } else {
                        echo 'No orders have been placed yet.';
                    }
                    ?>
                </p>
                <?php if (!$search && !$status && !$date_from && !$date_to): ?>
                    <p>When customers place orders, they will appear here.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="admin_orders.php?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $date_from ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo $date_to ? '&date_to=' . urlencode($date_to) : ''; ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <a href="#" class="active"><?php echo $i; ?></a>
                <?php elseif ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="admin_orders.php?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $date_from ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo $date_to ? '&date_to=' . urlencode($date_to) : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <span>...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="admin_orders.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $date_from ? '&date_from=' . urlencode($date_from) : ''; ?><?php echo $date_to ? '&date_to=' . urlencode($date_to) : ''; ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function applyFilter(filter) {
    const url = new URL(window.location.href);
    if (filter) {
        url.searchParams.set('status', filter);
    } else {
        url.searchParams.delete('status');
    }
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

function exportOrders(format) {
    const url = new URL('export_orders.php', window.location.origin);
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    url.search = params.toString();
    window.open(url.toString(), '_blank');
}

function getStatusIcon(status) {
    const icons = {
        'pending': 'clock',
        'processing': 'cog',
        'shipped': 'shipping-fast',
        'delivered': 'check-circle',
        'cancelled': 'times-circle'
    };
    return icons[status] || 'question-circle';
}

// Auto-submit date filters when both are selected
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('change', function() {
        const dateFrom = document.querySelector('input[name="date_from"]').value;
        const dateTo = document.querySelector('input[name="date_to"]').value;
        if (dateFrom && dateTo) {
            document.getElementById('filterForm').submit();
        }
    });
});
</script>

<?php
require_once 'admin_footer.php';
?>