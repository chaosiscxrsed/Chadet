<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Manage Customers';

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : '';
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle delete action
// In your admin_customers.php, update the delete section:
if ($action === 'delete' && $customer_id && hasPermission('admin')) {
    // Check if customer has orders first
    $order_count = getCustomerOrdersCount($customer_id);
    
    if ($order_count > 0) {
        header("Location: admin_customers.php?error=Cannot delete customer with existing orders");
        exit();
    }
    
    if (deleteCustomer($customer_id)) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'DELETE_CUSTOMER', "Deleted customer ID: $customer_id", $_SERVER['REMOTE_ADDR']);
        header("Location: admin_customers.php?success=Customer deleted successfully");
        exit();
    } else {
        header("Location: admin_customers.php?error=Failed to delete customer");
        exit();
    }
}

// Get customers with pagination
$customers_data = getCustomers($page, 10, $search);
$customers = $customers_data['data'];
$total_pages = $customers_data['pages'];

require_once 'admin_header.php';
?>

<style>
    .table-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        justify-content: space-between;
        align-items: center;
    }

    .search-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-box {
        padding: 10px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        min-width: 300px;
        transition: var(--transition);
    }

    .search-box:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    .btn-search {
        padding: 10px 20px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-search:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-top: 20px;
    }

    .table-header {
        padding: 20px;
        background: linear-gradient(90deg, var(--light-color), white);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        color: var(--text-dark);
        font-size: 18px;
        font-weight: 600;
    }

    .table-content {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--light-color);
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: var(--text-dark);
        border-bottom: 2px solid var(--border-color);
        white-space: nowrap;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tr:hover {
        background: rgba(78, 73, 52, 0.02);
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, var(--accent-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 16px;
    }

    .customer-info {
        margin-left: 15px;
    }

    .customer-name {
        font-weight: 500;
        color: var(--text-dark);
    }

    .customer-email {
        font-size: 12px;
        color: var(--text-color);
        margin-top: 2px;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: var(--transition);
    }

    .action-btn.view {
        background: var(--info-color);
        color: white;
    }

    .action-btn.edit {
        background: var(--warning-color);
        color: white;
    }

    .action-btn.delete {
        background: var(--danger-color);
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-color);
        text-decoration: none;
        transition: var(--transition);
    }

    .pagination a:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .pagination .active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
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

    @media (max-width: 768px) {
        .table-actions {
            flex-direction: column;
            gap: 15px;
        }

        .search-form {
            width: 100%;
        }

        .search-box {
            min-width: auto;
            flex: 1;
        }

        .table {
            min-width: 700px;
        }

        .table-content {
            overflow-x: auto;
        }
    }
</style>

<div class="table-actions">
    <a href="admin_customers_add.php" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add New Customer
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
            <i class="fas fa-users"></i> 
            Customers 
            <span style="font-size: 14px; color: var(--text-color); font-weight: normal;">
                (<?php echo $customers_data['total']; ?> total)
            </span>
        </h3>
        <?php if ($search): ?>
            <div style="font-size: 14px; color: var(--primary-color);">
                <i class="fas fa-search"></i> Search results for: "<?php echo htmlspecialchars($search); ?>"
            </div>
        <?php endif; ?>
    </div>

    <div class="table-content">
        <?php if (!empty($customers)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date of Birth</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>#<?php echo $customer['id']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="customer-avatar">
                                        <?php echo strtoupper(substr($customer['first_name'], 0, 1)); ?>
                                    </div>
                                    <div class="customer-info">
                                        <div class="customer-name">
                                            <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" style="color: var(--info-color); text-decoration: none;">
                                    <?php echo htmlspecialchars($customer['email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($customer['phone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($customer['phone']); ?>" style="color: var(--text-color); text-decoration: none;">
                                        <?php echo htmlspecialchars($customer['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--border-color); font-style: italic;">Not provided</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($customer['date_of_birth']): ?>
                                    <?php echo date('M d, Y', strtotime($customer['date_of_birth'])); ?>
                                <?php else: ?>
                                    <span style="color: var(--border-color); font-style: italic;">Not provided</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="admin_customers_edit.php?id=<?php echo $customer['id']; ?>" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (hasPermission('admin')): ?>
                                        <a href="admin_customers.php?action=delete&id=<?php echo $customer['id']; ?>" 
                                           class="action-btn delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
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
                <i class="fas fa-users"></i>
                <h4>No Customers Found</h4>
                <p><?php echo $search ? 'No customers match your search criteria.' : 'No customers in the database yet.'; ?></p>
                <?php if (!$search): ?>
                    <a href="admin_customers_add.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Your First Customer
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="admin_customers.php?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <a href="#" class="active"><?php echo $i; ?></a>
                <?php elseif ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="admin_customers.php?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <span>...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="admin_customers.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'admin_footer.php';
?>