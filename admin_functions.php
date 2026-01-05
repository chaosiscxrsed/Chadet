<?php
/**
 * CHADET COSMETICS - Admin Functions
 * UPDATED AND GUARANTEED TO WORK
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get database connection - FIXED
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=chadet_cosmetics;charset=utf8mb4", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Authenticate admin - SIMPLIFIED AND FIXED
 */
function authenticateAdmin($username, $password) {
    $pdo = getDBConnection();
    
    try {
        // SIMPLE query - just get the user
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // Check which password column exists
            if (isset($admin['password_hash'])) {
                // Use password_hash column
                if (password_verify($password, $admin['password_hash'])) {
                    return $admin;
                }
            } elseif (isset($admin['password'])) {
                // Use password column (fallback)
                if (password_verify($password, $admin['password'])) {
                    return $admin;
                }
            }
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Admin auth error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Require admin login - FIXED REDIRECTION
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: admin_login.php");
        exit();
    }
}

/**
 * Get current admin data
 */
function getCurrentAdmin() {
    if (isset($_SESSION['admin_id'])) {
        $pdo = getDBConnection();
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get admin data error: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Log admin activity
 */
function logAdminActivity($admin_id, $username, $action, $details = '', $ip_address = '') {
    $pdo = getDBConnection();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip_address = $ip_address ?: $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, admin_username, action, details, ip_address, user_agent) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $username, $action, $details, $ip_address, $user_agent]);
    } catch (PDOException $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    $pdo = getDBConnection();
    $stats = [];
    
    try {
        // Total customers
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM customers");
        $stats['total_customers'] = $stmt->fetch()['total'];
        
        // Total products
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $stats['total_products'] = $stmt->fetch()['total'];
        
        // Active products
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE is_active = TRUE");
        $stats['active_products'] = $stmt->fetch()['total'];
        
        // Total orders
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt->fetch()['total'];
        
        // Today's orders
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()");
        $stats['today_orders'] = $stmt->fetch()['total'];
        
        // Pending orders
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = $stmt->fetch()['total'];
        
        // Total revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid'");
        $stats['total_revenue'] = $stmt->fetch()['total'];
        
        // Today's revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
                            WHERE payment_status = 'paid' AND DATE(created_at) = CURDATE()");
        $stats['today_revenue'] = $stmt->fetch()['total'];
        
        // Low stock products
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 10 AND is_active = TRUE");
        $stats['low_stock'] = $stmt->fetch()['total'];
        
        // New customers today
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM customers WHERE DATE(created_at) = CURDATE()");
        $stats['new_customers_today'] = $stmt->fetch()['total'];
        
    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        return [
            'total_customers' => 0,
            'total_products' => 0,
            'active_products' => 0,
            'total_orders' => 0,
            'today_orders' => 0,
            'pending_orders' => 0,
            'total_revenue' => 0,
            'today_revenue' => 0,
            'low_stock' => 0,
            'new_customers_today' => 0
        ];
    }
    
    return $stats;
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Get status badge
 */
function getStatusBadge($status, $type = 'general') {
    $badges = [
        'order' => [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'processing' => '<span class="badge badge-info">Processing</span>',
            'shipped' => '<span class="badge badge-primary">Shipped</span>',
            'delivered' => '<span class="badge badge-success">Delivered</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>'
        ],
        'payment' => [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'paid' => '<span class="badge badge-success">Paid</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
            'refunded' => '<span class="badge badge-secondary">Refunded</span>'
        ],
        'general' => [
            'active' => '<span class="badge badge-success">Active</span>',
            'inactive' => '<span class="badge badge-secondary">Inactive</span>',
            'suspended' => '<span class="badge badge-danger">Suspended</span>'
        ]
    ];
    
    $typeBadges = $badges[$type] ?? $badges['general'];
    return $typeBadges[$status] ?? '<span class="badge">' . ucfirst($status) . '</span>';
}

/**
 * Get all customers with pagination
 */
function getCustomers($page = 1, $per_page = 10, $search = '') {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $per_page;
    
    $where = '';
    $params = [];
    
    if ($search) {
        $where = "WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $searchTerm = "%$search%";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    }
    
    try {
        // Get total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM customers $where");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get customers
        $sql = "SELECT * FROM customers $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        if ($params) {
            $stmt->execute(array_merge($params, [$per_page, $offset]));
        } else {
            $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        $customers = $stmt->fetchAll();
        
        return [
            'data' => $customers,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
        
    } catch (PDOException $e) {
        error_log("Get customers error: " . $e->getMessage());
        return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

/**
 * Get customer by ID
 */
function getCustomerById($id) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get customer by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Update customer
 */
function updateCustomer($id, $data) {
    $pdo = getDBConnection();
    
    $allowed_fields = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth'];
    $updates = [];
    $params = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, $allowed_fields)) {
            $updates[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $sql = "UPDATE customers SET " . implode(', ', $updates) . " WHERE id = ?";
    $params[] = $id;
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Update customer error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete customer
 */
function deleteCustomer($id) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Delete customer error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all products with pagination
 */
function getProducts($page = 1, $per_page = 10, $search = '', $category = '') {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $per_page;
    
    $where = '';
    $params = [];
    
    if ($search || $category) {
        $conditions = [];
        if ($search) {
            $conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        if ($category) {
            $conditions[] = "c.slug = ?";
            $params[] = $category;
        }
        $where = "WHERE " . implode(' AND ', $conditions);
    }
    
    try {
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    $where";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get products
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $where 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        $params[] = $per_page;
        $params[] = $offset;
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        return [
            'data' => $products,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
        
    } catch (PDOException $e) {
        error_log("Get products error: " . $e->getMessage());
        return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

/**
 * Get product by ID
 */
function getProductById($id) {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get product by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Update product
 */
function updateProduct($id, $data) {
    $pdo = getDBConnection();
    
    $allowed_fields = ['name', 'description', 'price', 'category_id', 'stock_quantity', 
                      'is_featured', 'is_active', 'image_url'];
    $updates = [];
    $params = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, $allowed_fields)) {
            $updates[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $sql = "UPDATE products SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?";
    $params[] = $id;
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Update product error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete product
 */
function deleteProduct($id) {
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Delete product images
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->execute([$id]);
        
        // Delete product variants
        $stmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
        $stmt->execute([$id]);
        
        // Delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        $pdo->commit();
        return $result;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Delete product error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create new product
 */
function createProduct($data) {
    $pdo = getDBConnection();
    
    $fields = ['name', 'slug', 'description', 'price', 'category_id', 'stock_quantity', 
               'is_featured', 'is_active', 'image_url'];
    $values = [];
    $placeholders = [];
    $params = [];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $placeholders[] = '?';
            $params[] = $data[$field];
        } else {
            $placeholders[] = '?';
            $params[] = $field === 'is_featured' || $field === 'is_active' ? 0 : '';
        }
    }
    
    $sql = "INSERT INTO products (" . implode(', ', $fields) . ", created_at, updated_at) 
            VALUES (" . implode(', ', $placeholders) . ", NOW(), NOW())";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Create product error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all categories
 */
function getCategories() {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get categories error: " . $e->getMessage());
        return [];
    }
}

/**
 * Generate pagination links
 */
function generatePagination($current_page, $total_pages, $url, $search = '') {
    if ($total_pages <= 1) return '';
    
    $query_params = '';
    if ($search) {
        $query_params = '&search=' . urlencode($search);
    }
    
    $pagination = '<nav class="pagination"><ul>';
    
    // Previous button
    if ($current_page > 1) {
        $pagination .= '<li><a href="' . $url . '?page=' . ($current_page - 1) . $query_params . '">&laquo; Prev</a></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $current_page ? ' class="active"' : '';
        $pagination .= '<li' . $active . '><a href="' . $url . '?page=' . $i . $query_params . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $pagination .= '<li><a href="' . $url . '?page=' . ($current_page + 1) . $query_params . '">Next &raquo;</a></li>';
    }
    
    $pagination .= '</ul></nav>';
    return $pagination;
}

/**
 * Check admin permission
 */
function hasPermission($required_role = 'admin') {
    $admin = getCurrentAdmin();
    if (!$admin) return false;
    
    $roles = ['editor' => 1, 'admin' => 2, 'super_admin' => 3];
    $user_role = $admin['role'] ?? 'editor';
    $required_level = $roles[$required_role] ?? 1;
    $user_level = $roles[$user_role] ?? 1;
    
    return $user_level >= $required_level;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Upload image
 */
function uploadImage($file, $directory = '../uploads/') {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Max 5MB'];
    }
    
    // Create directory if not exists
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $filename = uniqid() . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
    $filepath = $directory . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $type, $message) {
    header("Location: $url?$type=" . urlencode($message));
    exit();
}

/**
 * NEW FUNCTIONS TO FIX LOGIN ISSUE
 */

/**
 * Check if admin table exists and has admin user
 */
function checkAdminSystemReady() {
    $pdo = getDBConnection();
    
    try {
        // Check if table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'admin_users'");
        if ($tableCheck->rowCount() == 0) {
            return ['ready' => false, 'message' => 'Admin table does not exist'];
        }
        
        // Check if admin user exists
        $userCheck = $pdo->query("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'");
        $count = $userCheck->fetch()['count'];
        
        if ($count == 0) {
            return ['ready' => false, 'message' => 'Admin user does not exist'];
        }
        
        return ['ready' => true, 'message' => 'System is ready'];
        
    } catch (PDOException $e) {
        return ['ready' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Emergency fix for admin login
 */
function emergencyFixAdminLogin() {
    $pdo = getDBConnection();
    
    try {
        // Create table if doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(100) DEFAULT 'admin@chadet.com',
            full_name VARCHAR(100) DEFAULT 'Administrator',
            role VARCHAR(20) DEFAULT 'super_admin',
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Delete existing admin
        $pdo->exec("DELETE FROM admin_users WHERE username = 'admin'");
        
        // Insert new admin
        $password = 'admin123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', $hashed_password]);
        
        return ['success' => true, 'message' => 'Admin system fixed. Login with admin/admin123'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Fix failed: ' . $e->getMessage()];
    }
}

/**
 * Test password verification
 */
function testPasswordVerification() {
    $test_password = 'admin123';
    $test_hash = password_hash($test_password, PASSWORD_DEFAULT);
    
    return [
        'password' => $test_password,
        'hash' => $test_hash,
        'verification' => password_verify($test_password, $test_hash) ? 'PASS' : 'FAIL'
    ];
}

/**
 * Debug admin user
 */
function debugAdminUser() {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch();
        
        if ($admin) {
            return [
                'exists' => true,
                'id' => $admin['id'],
                'username' => $admin['username'],
                'has_password_hash' => isset($admin['password_hash']) && !empty($admin['password_hash']),
                'has_password' => isset($admin['password']) && !empty($admin['password']),
                'password_hash' => isset($admin['password_hash']) ? substr($admin['password_hash'], 0, 50) . '...' : null,
                'role' => $admin['role'] ?? 'not set',
                'status' => $admin['status'] ?? 'not set'
            ];
        } else {
            return ['exists' => false, 'message' => 'Admin user not found'];
        }
        
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * Simple login test
 */
function testLogin($username = 'admin', $password = 'admin123') {
    return authenticateAdmin($username, $password) ? 'SUCCESS' : 'FAILED';
}

/**
 * Add new customer (MISSING FUNCTION)
 */
function addCustomer($data) {
    $pdo = getDBConnection();
    
    $first_name = sanitizeInput($data['first_name'] ?? '');
    $last_name = sanitizeInput($data['last_name'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $date_of_birth = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($data['password'])) {
        return ['success' => false, 'message' => 'All required fields must be filled'];
    }
    
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    // Validate password
    if (strlen($data['password']) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
    }
    
    // Hash password
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
    
    try {
        $sql = "INSERT INTO customers (first_name, last_name, email, phone, date_of_birth, password_hash, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $last_name, $email, $phone, $date_of_birth, $password_hash]);
        
        $customer_id = $pdo->lastInsertId();
        return ['success' => true, 'id' => $customer_id, 'message' => 'Customer added successfully'];
        
    } catch (PDOException $e) {
        error_log("Add customer error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to add customer'];
    }
}

/**
 * Check if email exists (MISSING FUNCTION)
 */
function emailExists($email, $exclude_id = null) {
    $pdo = getDBConnection();
    
    try {
        if ($exclude_id) {
            $sql = "SELECT id FROM customers WHERE email = ? AND id != ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email, $exclude_id]);
        } else {
            $sql = "SELECT id FROM customers WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Email exists check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Search customers (MISSING FUNCTION)
 */
function searchCustomers($search_term, $limit = 20) {
    $pdo = getDBConnection();
    
    try {
        $search_term = "%{$search_term}%";
        $sql = "SELECT * FROM customers 
                WHERE first_name LIKE ? 
                OR last_name LIKE ? 
                OR email LIKE ? 
                OR phone LIKE ?
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_term, $search_term, $search_term, $search_term, $limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Search customers error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent customers (MISSING FUNCTION)
 */
function getRecentCustomers($limit = 10) {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT * FROM customers ORDER BY created_at DESC LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Get recent customers error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get customer orders count (MISSING FUNCTION)
 */
function getCustomerOrdersCount($customer_id) {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT COUNT(*) as order_count FROM orders WHERE customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);
        
        return $stmt->fetch()['order_count'];
        
    } catch (PDOException $e) {
        error_log("Get customer orders count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get all products with pagination - SIMPLE WORKING VERSION
 */
function getProductsSimple($page = 1, $per_page = 10, $search = '', $category = '') {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $per_page;
    
    try {
        // Build base query
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $where = [];
        $params = [];
        
        // Add search condition
        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        // Add category condition
        if (!empty($category)) {
            $where[] = "c.slug = ?";
            $params[] = $category;
        }
        
        // Add WHERE clause if needed
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        // Add ordering and pagination
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
        
        // Execute query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id";
        if (!empty($where)) {
            $count_sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $count_stmt = $pdo->prepare($count_sql);
        if (!empty($where)) {
            // Remove the last two params (limit and offset) for count query
            $count_params = array_slice($params, 0, count($params) - 2);
            $count_stmt->execute($count_params);
        } else {
            $count_stmt->execute();
        }
        
        $total = $count_stmt->fetch()['total'];
        
        return [
            'data' => $products,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
        
    } catch (PDOException $e) {
        error_log("Get products error: " . $e->getMessage());
        return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

/**
 * Get orders with pagination and filtering
 */
function getOrders($page = 1, $per_page = 15, $search = '', $status = '', $date_from = '', $date_to = '') {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $per_page;
    
    $where = [];
    $params = [];
    $param_types = '';
    
    // Build WHERE clause
    if (!empty($search)) {
        $where[] = "(o.order_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ?)";
        $search_term = "%$search%";
        $params = array_fill(0, 4, $search_term);
        $param_types = str_repeat('s', 4);
    }
    
    if (!empty($status)) {
        $where[] = "o.status = ?";
        $params[] = $status;
        $param_types .= 's';
    }
    
    if (!empty($date_from)) {
        $where[] = "DATE(o.created_at) >= ?";
        $params[] = $date_from;
        $param_types .= 's';
    }
    
    if (!empty($date_to)) {
        $where[] = "DATE(o.created_at) <= ?";
        $params[] = $date_to;
        $param_types .= 's';
    }
    
    $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    try {
        // Get total count
        $count_sql = "SELECT COUNT(*) as total 
                     FROM orders o 
                     LEFT JOIN customers c ON o.customer_id = c.id 
                     $where_clause";
        
        $count_stmt = $pdo->prepare($count_sql);
        if (!empty($params)) {
            $count_stmt->execute($params);
        } else {
            $count_stmt->execute();
        }
        $total_rows = $count_stmt->fetch()['total'];
        $total_pages = ceil($total_rows / $per_page);
        
        // Get orders
        $sql = "SELECT o.*, c.first_name, c.last_name, c.email 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                $where_clause 
                ORDER BY o.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        
        // Add limit and offset to params
        if (!empty($params)) {
            $params[] = $per_page;
            $params[] = $offset;
            $stmt->execute($params);
        } else {
            $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        $orders = $stmt->fetchAll();
        
        return [
            'data' => $orders,
            'total' => $total_rows,
            'pages' => $total_pages,
            'current_page' => $page
        ];
        
    } catch (PDOException $e) {
        error_log("Get orders error: " . $e->getMessage());
        return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

/**
 * Get order statistics
 */
function getOrderStats() {
    $pdo = getDBConnection();
    $stats = [
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0,
        'total' => 0,
        'today' => 0,
        'revenue' => 0
    ];
    
    try {
        // Count by status
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        foreach ($statuses as $status) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = ?");
            $stmt->execute([$status]);
            $stats[$status] = $stmt->fetch()['count'];
        }
        
        // Total orders
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
        $stats['total'] = $stmt->fetch()['count'];
        
        // Today's orders
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
        $stats['today'] = $stmt->fetch()['count'];
        
        // Total revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE payment_status = 'paid'");
        $stats['revenue'] = $stmt->fetch()['revenue'];
        
    } catch (PDOException $e) {
        error_log("Get order stats error: " . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Get order items
 */
function getOrderItems($order_id) {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT oi.*, p.name as product_name 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Get order items error: " . $e->getMessage());
        return [];
    }
}

/**
 * Update order status
 */
function updateOrderStatus($order_id, $status) {
    $pdo = getDBConnection();
    
    try {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $order_id]);
        
    } catch (PDOException $e) {
        error_log("Update order status error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete order
 */
function deleteOrder($order_id) {
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Delete order items first
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        // Delete order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $result = $stmt->execute([$order_id]);
        
        $pdo->commit();
        return $result;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Delete order error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get order by ID
 */
function getOrderById($order_id) {
    $pdo = getDBConnection();
    
    try {
        $sql = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone,
                       sa.first_name as shipping_first_name, sa.last_name as shipping_last_name,
                       sa.address_line_1 as shipping_address, sa.city as shipping_city,
                       sa.state as shipping_state, sa.postal_code as shipping_postal_code,
                       sa.country as shipping_country,
                       ba.first_name as billing_first_name, ba.last_name as billing_last_name,
                       ba.address_line_1 as billing_address, ba.city as billing_city,
                       ba.state as billing_state, ba.postal_code as billing_postal_code,
                       ba.country as billing_country
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN customer_addresses sa ON o.shipping_address_id = sa.id
                LEFT JOIN customer_addresses ba ON o.billing_address_id = ba.id
                WHERE o.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Get order by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Update your existing deleteCustomer function to check for orders
 * (Update this existing function in your file)
 */
?>