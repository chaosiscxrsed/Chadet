<?php
session_start();
require_once 'C:/xampp/htdocs/CHADET/config.php';
$page_title = 'Forgot Password CHADET';
include 'C:/xampp/htdocs/CHADET/header.php';
?>

<!-- Forgot Password Page Content -->
<div class="forgot-password-page-content">
    <div class="forgot-password-container">
        <!-- Left Side - Brand Section -->
        <div class="forgot-password-brand">
            <div class="brand-header">
                <a href="index.php" class="brand-logo"><img src="logo.png" alt="CHADET Logo" style="width: 300px; height: auto;"></a>
            </div>
            <div class="brand-message">
                <h2>Reset Your Password</h2>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>
        </div>

        <!-- Right Side - Forgot Password Form -->
        <div class="forgot-password-form-container">
            <div class="form-header">
                <h1>Forgot Password?</h1>
                <p>We'll help you get back into your account</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form class="forgot-password-form" method="POST" action="forgot_password_process.php">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required>
                </div>

                <button type="submit" class="submit-button">Send Reset Link</button>

                <div class="back-to-login">
                    <a href="login.php">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Forgot Password Page Styles */
    .forgot-password-page-content {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        background: #faf8f5;
    }

    .forgot-password-container {
        display: flex;
        width: 100%;
        max-width: 900px;
        min-height: 500px;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(78, 73, 52, 0.15);
    }

    .forgot-password-brand {
        flex: 1;
        background: linear-gradient(135deg, #4e4934 0%, #635c55 50%, #e0c6ad 100%);
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .brand-message {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #dbd1c8;
        margin-top: 30px;
        max-width: 300px;
    }

    .brand-message h2 {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .brand-message p {
        font-size: 14px;
        opacity: 0.9;
    }

    .forgot-password-form-container {
        flex: 1;
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .form-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #4e4934;
    }

    .form-header p {
        color: #635c55;
        font-size: 15px;
    }

    .forgot-password-form {
        max-width: 350px;
        margin: 0 auto;
        width: 100%;
    }

    .submit-button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(45deg, #4e4934, #635c55);
        color: #dbd1c8;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .submit-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(78, 73, 52, 0.2);
    }

    .back-to-login {
        text-align: center;
        margin-top: 25px;
    }

    .back-to-login a {
        color: #4e4934;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .back-to-login a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .forgot-password-container {
            flex-direction: column;
            max-width: 450px;
        }
        
        .forgot-password-form-container {
            padding: 40px;
        }
    }
</style>

<?php include 'C:/xampp/htdocs/CHADET/footer.php'; ?>