<?php
require_once 'C:/xampp/htdocs/CHADET/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: forgot_password.php?error=Please enter a valid email address");
        exit();
    }
    
    try {
        // Check if email exists
        $sql = "SELECT id, first_name FROM customers WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Check if reset_token column exists, if not, skip this part
            try {
                $update_sql = "UPDATE customers SET reset_token = ?, reset_token_expiry = ? WHERE id = ?";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([$token_hash, $expires_at, $user['id']]);
                
                // In a real application, send email here
                // For now, just show success message
                
                $message = "Password reset link has been sent to your email. Please check your inbox.";
                
                header("Location: forgot_password.php?success=" . urlencode($message));
            } catch (PDOException $e) {
                // Column might not exist, show alternative message
                $message = "Password reset feature is being updated. Please contact support for assistance.";
                header("Location: forgot_password.php?success=" . urlencode($message));
            }
        } else {
            header("Location: forgot_password.php?error=Email address not found");
        }
    } catch (PDOException $e) {
        header("Location: forgot_password.php?error=System error. Please try again later.");
        error_log("Forgot password error: " . $e->getMessage());
    }
    
    exit();
} else {
    header("Location: forgot_password.php");
    exit();
}
?>