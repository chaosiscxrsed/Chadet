<?php
session_start();
require_once 'admin_functions.php';

// Log logout activity if admin was logged in
if (isset($_SESSION['admin_id'])) {
    logAdminActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'LOGOUT', 'Admin logged out', $_SERVER['REMOTE_ADDR']);
}

// Destroy all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page with success message
header("Location: admin_login.php?success=Logged out successfully");
exit();
?>