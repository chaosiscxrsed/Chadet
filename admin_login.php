<?php
session_start();

// Include functions
require_once 'admin_functions.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header("Location: admin_dashboard.php");
    exit();
}

// Check if we need to setup
$needs_setup = false;

// Auto-setup if needed
if ($needs_setup && isset($_GET['auto_setup'])) {
    setupAdminTable();
    createAdminUser('admin', 'admin123', 'admin@chadet.com', 'Administrator', 'super_admin');
    header("Location: admin_login.php?success=Admin user created automatically. Use admin / admin123");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin Login - CHADET Cosmetics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: linear-gradient(135deg, #4e4934 0%, #635c55 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.login-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 380px;
    overflow: hidden;
}

.login-header {
    background: white;
    padding: 20px;
    text-align: center;
    border-bottom: 2px solid #f0f0f0;
}

.brand-logo {
    margin-bottom: 10px;
}

.brand-logo img {
    max-width: 250px;
    height: auto;
}

.login-header h1 {
    color: #4e4934;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 5px;
}

.login-header p {
    color: #635c55;
    font-size: 12px;
    letter-spacing: 1px;
}

.login-form {
    padding: 25px;
}

.alert {
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert-danger {
    background: #fee;
    border: 1px solid #fcc;
    color: #c00;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #4e4934;
    font-weight: 500;
    font-size: 13px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e8e4df;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #faf8f5;
    color: #4e4934;
}

.form-control:focus {
    outline: none;
    border-color: #4e4934;
    background: white;
    box-shadow: 0 0 0 2px rgba(78, 73, 52, 0.1);
}

.btn-login {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg, #4e4934, #635c55);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 5px;
}

.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 10px rgba(78, 73, 52, 0.15);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #e8e4df;
    font-size: 11px;
    color: #635c55;
}

.login-footer p {
    margin: 3px 0;
}

.password-toggle {
    position: relative;
}

.password-toggle input {
    padding-right: 40px;
}

.toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #635c55;
    cursor: pointer;
    font-size: 14px;
}

@media (max-width: 480px) {
    .login-container {
        max-width: 320px;
    }
    
    .login-form {
        padding: 20px;
    }
    
    .brand-logo img {
        max-width: 150px;
    }
}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="brand-logo">
                <img src="logo.png" alt="CHADET Logo">
            </div>
            <p>ADMINISTRATOR PANEL</p>
        </div>
        
        <div class="login-form">
            <?php if ($needs_setup): ?>
                <div class="setup-notice">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Setup Required:</strong> Admin system needs initial setup.
                    <br>
                    <a href="admin_login.php?auto_setup=1">Click here to auto-setup</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['warning'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <?php echo htmlspecialchars($_GET['warning']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['info'])): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <?php echo htmlspecialchars($_GET['info']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="admin_login_process.php" id="loginForm">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="Enter username" 
                           value="admin"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Enter password" 
                           value="admin123"
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn-login" id="loginButton">
                    <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
                </button>
            </form>
            
            <div class="login-footer">
                <p>For authorized personnel only</p>
                <p>
                    <a href="admin_login.php?auto_setup=1">
                        <i class="fas fa-cogs"></i> Auto Setup
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            });
        }, 5000);
        
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Focus username field on page load
        document.getElementById('username').focus();
        
        // Handle Enter key to submit
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !document.getElementById('loginButton').disabled) {
                document.getElementById('loginForm').submit();
            }
        });
        
        // Debug function
        function showDebug() {
            window.open('debug_login.php', '_blank');
        }
        
        // Auto login for testing (remove in production)
        function autoLogin() {
            document.getElementById('loginForm').submit();
        }
        
        // Uncomment for auto-login testing
        // setTimeout(autoLogin, 1000);
    </script>
</body>
</html>