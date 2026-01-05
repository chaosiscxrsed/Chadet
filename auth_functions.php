<?php
require_once 'C:/xampp/htdocs/CHADET/config.php';


function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

function requireLogin($pdo) {
    if (!isLoggedIn()) {
        // Check for remember me cookie
        if (isset($_COOKIE['chadet_remember'])) {
            $cookie_value = base64_decode($_COOKIE['chadet_remember']);
            list($user_id, $token_hash) = explode(':', $cookie_value);
            
            try {
                $sql = "SELECT id, email, first_name, last_name, password_hash FROM customers WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id]);
                
                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verify token
                    if (hash('sha256', $user['password_hash']) === $token_hash) {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['logged_in'] = true;
                        return; // User is now logged in
                    }
                }
            } catch (PDOException $e) {
                error_log("Remember me login failed: " . $e->getMessage());
            }
        }
        
        // Not logged in and no valid remember me cookie
        header("Location: login.php?error=Please login to access this page");
        exit();
    }
}

function redirectIfLoggedIn($pdo, $redirect_to = 'index.php') {
    if (isLoggedIn()) {
        header("Location: $redirect_to");
        exit();
    }
}

function validatePasswordStrength($password) {
    $strength = 0;
    
    // Length check
    if (strlen($password) >= 8) $strength++;
    
    // Contains uppercase
    if (preg_match('/[A-Z]/', $password)) $strength++;
    
    // Contains numbers
    if (preg_match('/[0-9]/', $password)) $strength++;
    
    // Contains special characters
    if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;
    
    return $strength; // 0-4
}

function getPasswordStrengthText($strength) {
    switch ($strength) {
        case 0: return 'None';
        case 1: return 'Weak';
        case 2: return 'Fair';
        case 3: return 'Good';
        case 4: return 'Strong';
        default: return 'Unknown';
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function emailExists($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Email check failed: " . $e->getMessage());
        return false;
    }
}

function getUserById($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, date_of_birth, created_at FROM customers WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get user by ID failed: " . $e->getMessage());
        return null;
    }
}

function updateUserProfile($pdo, $user_id, $data) {
    try {
        // Build SQL dynamically based on provided fields
        $sql = "UPDATE customers SET ";
        $params = [];
        
        if (isset($data['first_name'])) {
            $sql .= "first_name = ?, ";
            $params[] = $data['first_name'];
        }
        
        if (isset($data['last_name'])) {
            $sql .= "last_name = ?, ";
            $params[] = $data['last_name'];
        }
        
        if (isset($data['phone'])) {
            $sql .= "phone = ?, ";
            $params[] = $data['phone'];
        }
        
        if (isset($data['date_of_birth'])) {
            $sql .= "date_of_birth = ?, ";
            $params[] = $data['date_of_birth'];
        }
        
        $sql .= "updated_at = NOW() WHERE id = ?";
        $params[] = $user_id;
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Update profile failed: " . $e->getMessage());
        return false;
    }
}

function addToCart($product_id, $quantity = 1, $variant_id = null) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $cart_item = [
        'product_id' => $product_id,
        'quantity' => $quantity,
        'variant_id' => $variant_id
    ];
    
    // Check if item already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id && $item['variant_id'] == $variant_id) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }
    
    return true;
}

function getCartCount() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}

function getCartTotal($pdo) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $total = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        try {
            $sql = "SELECT price FROM products WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item['product_id']]);
            
            if ($stmt->rowCount() > 0) {
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                $total += $product['price'] * $item['quantity'];
                
                // Add variant price adjustment if exists
                if ($item['variant_id']) {
                    $variant_sql = "SELECT price_adjustment FROM product_variants WHERE id = ?";
                    $variant_stmt = $pdo->prepare($variant_sql);
                    $variant_stmt->execute([$item['variant_id']]);
                    
                    if ($variant_stmt->rowCount() > 0) {
                        $variant = $variant_stmt->fetch(PDO::FETCH_ASSOC);
                        $total += $variant['price_adjustment'] * $item['quantity'];
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Cart total calculation failed: " . $e->getMessage());
        }
    }
    
    return $total;
}

// Function to get user orders
function getUserOrders($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT o.*, 
                   COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = :user_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Function to get order items
function getOrderItems($pdo, $order_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT oi.* 
            FROM order_items oi
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute(['order_id' => $order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Function to generate order number
function generateOrderNumber($pdo) {
    $year = date('Y');
    $month = date('m');
    
    // Get last order number for this month
    $stmt = $pdo->prepare("
        SELECT order_number FROM orders 
        WHERE order_number LIKE ? 
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute(["CHD-$year-$month-%"]);
    $last = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($last) {
        $parts = explode('-', $last['order_number']);
        $last_num = (int)end($parts);
        $next_num = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $next_num = '001';
    }
    
    return "CHD-$year-$month-$next_num";
}
?>