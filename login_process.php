<?php
require_once 'C:/xampp/htdocs/CHADET/config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate inputs
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email address is required.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    // If no errors, proceed with login
    if (empty($errors)) {
        try {
            // Prepare SQL statement
            $sql = "SELECT id, email, first_name, last_name, password_hash FROM customers WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['logged_in'] = true;
                    
                    // If "Remember me" is checked, set cookie
                    if ($remember) {
                        $cookie_name = "chadet_remember";
                        $cookie_value = base64_encode($user['id'] . ':' . hash('sha256', $user['password_hash']));
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 30 days
                    }
                    
                    // Update last login
                    try {
                        $update_sql = "UPDATE customers SET updated_at = NOW() WHERE id = ?";
                        $update_stmt = $pdo->prepare($update_sql);
                        $update_stmt->execute([$user['id']]);
                    } catch (PDOException $e) {
                        error_log("Update last login failed: " . $e->getMessage());
                    }
                    
                    // Redirect to dashboard or home page
                    header("Location: index.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Login failed. Please try again later.";
            error_log("Database error: " . $e->getMessage());
        }
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode(" ", $errors);
        header("Location: login.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header("Location: login.php");
    exit();
}
?>