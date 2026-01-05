<?php
require_once 'C:/xampp/htdocs/CHADET/config.php';

// Destroy all session data
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['chadet_remember'])) {
    setcookie('chadet_remember', '', time() - 3600, '/');
}

// Clear cart cookie if exists
if (isset($_COOKIE['chadet_cart'])) {
    setcookie('chadet_cart', '', time() - 3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit();
?>