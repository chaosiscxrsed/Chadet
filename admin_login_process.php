<?php
// admin_login_process.php - SIMPLE FIXED VERSION
session_start();

// Include the fixed functions
require_once 'admin_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header("Location: admin_login.php?error=Please enter both username and password");
        exit();
    }
    
    // Authenticate using the fixed function
    $admin = authenticateAdmin($username, $password);
    
    if ($admin) {
        // SUCCESS - Set session variables
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
        $_SESSION['admin_name'] = $admin['full_name'] ?? 'Administrator';
        $_SESSION['admin_login_time'] = time();
        
        // Update last login if column exists
        $pdo = getDBConnection();
        try {
            $pdo->exec("UPDATE admin_users SET last_login = NOW() WHERE id = " . $admin['id']);
        } catch(Exception $e) {
            // Ignore error - not critical
        }
        
        // Redirect to dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // FAILED login
        header("Location: admin_login.php?error=Invalid username or password");
        exit();
    }
} else {
    // Not a POST request
    header("Location: admin_login.php");
    exit();
}
?>