<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title) . ' - CHADET Admin'; ?></title>
    
    <!-- CSS -->
    <style>
        :root {
            --primary-color: #4e4934;
            --secondary-color: #635c55;
            --accent-color: #e0c6ad;
            --light-accent: #dbd1c8;
            --dark-color: #333333;
            --light-color: #faf8f5;
            --border-color: #e8e4df;
            --text-color: #7e7a76;
            --text-dark: #4e4934;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --shadow: 0 5px 15px rgba(78, 73, 52, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--text-color);
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            width: 260px;
            background: #e0c6ad 100%;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .admin-brand {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(245, 237, 237, 1);
            background: rgba(0,0,0,0.1);
        }
        
        .admin-brand h1 {
            font-size: 24px;
            color: var(--light-accent);
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .admin-brand p {
            font-size: 12px;
            color: #4e4934;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .admin-nav {
            padding: 20px 0;
        }
        
        .nav-section {
            margin-bottom: 25px;
            padding: 0 20px;
        }
        
        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #4e4934;
            margin-bottom: 12px;
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color:  #4e4934;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: var(--transition);
            position: relative;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: #4e4934;
            color: var(--light-accent);
            border-left-color: var(--accent-color);
        }
        
        .nav-item.active {
            background: #333;
            color: var(--light-accent);
            border-left-color: var(--accent-color);
        }
        
        .nav-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
        }
        
        .nav-item .badge {
            position: absolute;
            right: 15px;
            background: var(--danger-color);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            font-weight: 500;
        }
        
        .admin-sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0,0,0,0.2);
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, var(--accent-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 18px;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-size: 14px;
            color: white;
            font-weight: 500;
        }
        
        .user-role {
            font-size: 11px;
            color: rgba(219, 209, 200, 0.7);
            text-transform: capitalize;
        }
        
        /* Main Content Area */
        .admin-main {
            flex: 1;
            margin-left: 260px;
            transition: var(--transition);
        }
        
        .admin-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-title h1 {
            font-size: 24px;
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: var(--light-accent);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(78, 73, 52, 0.2);
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #218838);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(45deg, #218838, var(--success-color));
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #c82333);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(45deg, #c82333, var(--danger-color));
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: linear-gradient(45deg, var(--warning-color), #e0a800);
            color: #212529;
        }
        
        .btn-warning:hover {
            background: linear-gradient(45deg, #e0a800, var(--warning-color));
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: linear-gradient(45deg, var(--text-color), var(--secondary-color));
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--text-color));
            transform: translateY(-2px);
        }
        
        .btn-logout {
            background: var(--text-dark);
            color: var(--light-accent);
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-logout:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            padding: 20px 30px;
            background: white;
            border-bottom: 1px solid var(--border-color);
        }
        
        .breadcrumb ul {
            display: flex;
            list-style: none;
            gap: 10px;
            align-items: center;
        }
        
        .breadcrumb li {
            color: var(--text-color);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .breadcrumb li:not(:last-child):after {
            content: '›';
            color: var(--text-color);
        }
        
        .breadcrumb li a {
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .breadcrumb li a:hover {
            color: var(--primary-color);
        }
        
        .breadcrumb li.active {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Content Area */
        .admin-content {
            padding: 30px;
            min-height: calc(100vh - 140px);
        }
        
        /* Status Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .badge-primary {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        /* Alerts */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid transparent;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .header-title h1 {
                font-size: 20px;
            }
            
            .admin-content {
                padding: 20px;
            }
        }
    </style>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar" id="adminSidebar">
            <div class="admin-brand">
                <img src="logo.png" alt="CHADET Logo" style="max-width: 200px; height: auto; margin-bottom: 10px;">
                <p>ADMINISTRATION PANEL</p>
            </div>

            <nav class="admin-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="admin_dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="admin_customers.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_customers.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                        <?php
                        $pdo = getDBConnection();
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM customers");
                            $customer_count = $stmt->fetch()['count'];
                            if ($customer_count > 0) {
                                echo '<span class="badge">' . $customer_count . '</span>';
                            }
                        } catch (PDOException $e) {
                            // Silently fail
                        }
                        ?>
                    </a>
                    <a href="admin_products.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = TRUE");
                            $product_count = $stmt->fetch()['count'];
                            if ($product_count > 0) {
                                echo '<span class="badge">' . $product_count . '</span>';
                            }
                        } catch (PDOException $e) {
                            // Silently fail
                        }
                        ?>
                    </a>
                    <a href="admin_orders.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
                            $pending_orders = $stmt->fetch()['count'];
                            if ($pending_orders > 0) {
                                echo '<span class="badge">' . $pending_orders . '</span>';
                            }
                        } catch (PDOException $e) {
                            // Silently fail
                        }
                        ?>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="admin-main">
            <div class="admin-header">
                <div class="header-title">
                    <h1><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
                <div class="header-actions">
                    <a href="index.php" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                    <a href="admin_logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active"><?php echo htmlspecialchars($page_title); ?></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="admin-content">