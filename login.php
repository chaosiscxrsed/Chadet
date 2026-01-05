<?php
session_start();
require_once 'C:/xampp/htdocs/CHADET/config.php';
$page_title = 'Login CHADET';
include 'C:/xampp/htdocs/CHADET/header.php';
?>

<!-- Login Page Content -->
<div class="login-page-content">
    <div class="login-container">
        <!-- Left Side - Brand Section -->
        <div class="login-brand">
            <div class="brand-header">
                <a href="index.php" class="brand-logo"><img src="logo.png" alt="CHADET Logo" style="width: 500px; height: auto;"></a>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="form-header">
                <h1>Welcome to CHADET</h1>
                <p>Log in to your CHADET account</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert error" id="errorAlert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success" id="successAlert">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="login_process.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">Show</button>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        Remember me
                    </label>
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="login-button">LOG IN</button>

                <!-- Loading Spinner -->
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Logging in...</p>
                </div>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                <div class="social-login">
                    <button type="button" class="social-button" onclick="socialLogin('google')">
                        <span class="social-icon">G</span>
                        Google
                    </button>
                    <button type="button" class="social-button" onclick="socialLogin('facebook')">
                        <span class="social-icon">f</span>
                        Facebook
                    </button>
                </div>

                <div class="signup-link">
                    Don't have an account?
                    <a href="signup.php">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
*{
    font-family: 'Poppins', sans-serif;
}

.login-page-content {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: #faf8f5;
}

.login-container {
    display: flex;
    width: 100%;
    max-width: 1200px;
    min-height: 700px;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(78, 73, 52, 0.1);
}

/* Left Side - Brand/Visual */
.login-brand {
    flex: 1;
    background: linear-gradient(135deg, #4e4934 0%, #635c55 50%, #e0c6ad 100%);
    padding: 60px;
    display: flex;
    align-items: center; /* Center vertically */
    justify-content: center; /* Center horizontally */
    position: relative;
    overflow: hidden;
    text-align: center; /* Center text content */
}

.login-brand::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="white" opacity="0.05"/></svg>');
}

.brand-header {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.brand-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    width: 100%;
}

.brand-logo img {
    max-width: 400px; /* Reduced from 500px for better fit */
    width: 100%;
    height: auto;
    filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.2));
    transition: transform 0.3s ease;
}

.brand-logo:hover img {
    transform: scale(1.02);
}

/* Right Side - Login Form */
.login-form-container {
    flex: 1;
    padding: 80px 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-header {
    text-align: center;
    margin-bottom: 50px;
}

.form-header h1 {
    font-size: 36px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #4e4934;
}

.form-header p {
    color: #635c55;
    font-size: 16px;
}

.login-form {
    max-width: 400px;
    margin: 0 auto;
    width: 100%;
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #635c55;
    font-size: 14px;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e8e4df;
    border-radius: 10px;
    font-size: 16px;
    color: #4e4934;
    background: #faf8f5;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #4e4934;
    background: white;
    box-shadow: 0 5px 15px rgba(78, 73, 52, 0.1);
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 40px;
    margin-top: 8px;
    background: none;
    border: none;
    color: #635c55;
    cursor: pointer;
    font-size: 14px;
}

.remember-forgot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #635c55;
}

.remember-me input {
    accent-color: #4e4934;
}

.forgot-password {
    color: #4e4934;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.forgot-password:hover {
    color: #635c55;
    text-decoration: underline;
}

.login-button {
    width: 100%;
    padding: 16px;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(45deg, #4e4934, #635c55);
    color: #dbd1c8;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.login-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(78, 73, 52, 0.2);
}

.login-button:active {
    transform: translateY(0);
}

.divider {
    text-align: center;
    position: relative;
    margin: 30px 0;
    color: #a59e93;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e8e4df;
}

.divider span {
    background: white;
    padding: 0 20px;
    position: relative;
    font-size: 14px;
}

.social-login {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.social-button {
    flex: 1;
    padding: 14px;
    border: 2px solid #e8e4df;
    border-radius: 10px;
    background: white;
    color: #4e4934;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.social-button:hover {
    border-color: #4e4934;
    background: #faf8f5;
}

.social-icon {
    font-size: 18px;
}

.signup-link {
    text-align: center;
    color: #635c55;
    font-size: 14px;
}

.signup-link a {
    color: #4e4934;
    text-decoration: none;
    font-weight: 500;
    margin-left: 5px;
    transition: color 0.3s ease;
}

.signup-link a:hover {
    color: #635c55;
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 992px) {
    .login-container {
        flex-direction: column;
        max-width: 500px;
    }

    .login-brand {
        padding: 40px;
        min-height: 300px;
    }

    .brand-logo img {
        max-width: 300px;
    }

    .login-form-container {
        padding: 50px 40px;
    }
}

@media (max-width: 480px) {
    .login-container {
        border-radius: 10px;
    }

    .login-brand {
        padding: 30px 20px;
    }

    .brand-logo img {
        max-width: 250px;
    }

    .login-form-container {
        padding: 40px 20px;
    }

    .social-login {
        flex-direction: column;
    }

    .form-header h1 {
        font-size: 28px;
    }
}

/* Loading Animation */
.loading {
    display: none;
    text-align: center;
    margin-top: 20px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e8e4df;
    border-top: 3px solid #4e4934;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Error/Success Messages */
.alert {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: none;
}

.alert.error {
    background: #fee;
    border: 1px solid #fcc;
    color: #c00;
}

.alert.success {
    background: #efd;
    border: 1px solid #cec;
    color: #080;
}
    /* Error/Success Messages */
    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }

    .alert.error {
        background: #fee;
        border: 1px solid #fcc;
        color: #c00;
    }

    .alert.success {
        background: #efd;
        border: 1px solid #cec;
        color: #080;
    }
</style>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = 'Show';
        }
    }

    // Form submission
    document.querySelector('.login-form').addEventListener('submit', function(e) {
        document.getElementById('loading').style.display = 'block';
    });

    // Social login simulation
    function socialLogin(provider) {
        document.getElementById('loading').style.display = 'block';
        
        setTimeout(() => {
            document.getElementById('loading').style.display = 'none';
            alert(`Redirecting to ${provider} login...`);
        }, 1000);
    }

    // Show error/success messages from PHP
    window.onload = function() {
        <?php if (isset($_GET['error'])): ?>
            document.getElementById('errorAlert').style.display = 'block';
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            document.getElementById('successAlert').style.display = 'block';
        <?php endif; ?>
    };
</script>

<?php 
include 'C:/xampp/htdocs/CHADET/footer.php';
?>