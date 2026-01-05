<?php
require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM " . ADMIN_TABLE . " WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Update last login
            $update = $this->pdo->prepare("UPDATE " . ADMIN_TABLE . " SET last_login = NOW() WHERE id = ?");
            $update->execute([$admin['id']]);
            
            // Log activity
            $this->logActivity($admin['id'], $admin['username'], 'admin_login', 'Admin logged in');
            
            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_name'] = $admin['full_name'];
            
            return true;
        }
        return false;
    }
    
    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            $this->logActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'admin_logout', 'Admin logged out');
        }
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ../index.php');
            exit();
        }
    }
    
    public function requireRole($requiredRole) {
        $this->requireLogin();
        if ($_SESSION['admin_role'] !== $requiredRole && $_SESSION['admin_role'] !== 'super_admin') {
            header('Location: ../dashboard.php');
            exit();
        }
    }
    
    private function logActivity($adminId, $username, $action, $details = '') {
        $stmt = $this->pdo->prepare("INSERT INTO admin_logs (admin_id, admin_username, action, details, ip_address, user_agent) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $adminId,
            $username,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    }
}

// Initialize auth
$auth = new Auth($pdo);
?>