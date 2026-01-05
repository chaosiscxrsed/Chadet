<?php
session_start();
require_once 'C:/xampp/htdocs/CHADET/config.php';
$page_title = 'Sign Up CHADET';
include 'C:/xampp/htdocs/CHADET/header.php';
?>

<!-- Signup Page Content -->
<div class="signup-page-content">
    <div class="signup-container">
        <!-- Left Side - Brand Section -->
        <div class="signup-brand">
            <div class="brand-header">
                <a href="index.php" class="brand-logo"><img src="logo.png" alt="CHADET Logo" style="width: 350px; height: auto;"></a>
            </div>
        </div>

        <!-- Right Side - Signup Form -->
        <div class="signup-form-container">
            <div class="form-content-wrapper">
                <div class="form-header">
                   <h1>Create Account</h1>
                    <p>Join CHADET for an exclusive beauty experience</p>
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

                <form class="signup-form" method="POST" action="signup_process.php">
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-input" placeholder="Jane" required>
                        </div>
                        <div class="form-group half">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Doe" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input" placeholder="(123) 456-7890">
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-input">
                        <small class="field-note">For personalized recommendations</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">Show</button>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                            <span class="strength-text">Strength: <span id="strengthText">None</span></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">Show</button>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="newsletter" name="newsletter" checked>
                            <span class="checkbox-text">Send me beauty tips, exclusive offers, and updates</span>
                        </label>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="terms" name="terms" required>
                            <span class="checkbox-text">I agree to the <a href="terms.php" class="inline-link">Terms & Conditions</a> and <a href="privacy.php" class="inline-link">Privacy Policy</a> *</span>
                        </label>
                    </div>

                    <button type="submit" class="signup-button">Create Account</button>

                    <!-- Loading Spinner -->
                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p>Creating your account...</p>
                    </div>

                    <div class="divider">
                        <span>Or sign up with</span>
                    </div>

                    <div class="social-signup">
                        <button type="button" class="social-button" onclick="socialSignup('google')">
                            <span class="social-icon">G</span>
                            Google
                        </button>
                        <button type="button" class="social-button" onclick="socialSignup('facebook')">
                            <span class="social-icon">f</span>
                            Facebook
                        </button>
                    </div>

                    <div class="login-link">
                        Already have an account?
                        <a href="login.php">Log In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    *{
        font-family: 'Poppins', sans-serif;
    }
    /* Signup Page Styles */
    .signup-page-content {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        background: #faf8f5;
    }

    .signup-container {
        display: flex;
        width: 100%;
        max-width: 1000px;
        min-height: 600px;
        max-height: 85vh;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(78, 73, 52, 0.15);
    }

    /* Left Side - Brand/Visual */
    .signup-brand {
        flex: 1;
        background: linear-gradient(135deg, #4e4934 0%, #635c55 50%, #e0c6ad 100%);
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-width: 400px;
    }

    .signup-brand::before {
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
    }

    .brand-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        width: 100%;
    }

    .brand-logo img {
        max-width: 320px;
        width: 100%;
        height: auto;
        filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.2));
    }

    /* Right Side - Signup Form */
    .signup-form-container {
        flex: 1;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-width: 450px;
    }

    .form-content-wrapper {
        height: 100%;
        overflow-y: auto;
        padding: 40px;
        display: flex;
        flex-direction: column;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
        flex-shrink: 0;
    }

    .form-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #4e4934;
    }

    .form-header p {
        color: #635c55;
        font-size: 15px;
    }

    .signup-form {
        max-width: 100%;
        margin: 0;
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-group.half {
        flex: 1;
        width: 100%;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #635c55;
        font-size: 13px;
        font-weight: 500;
    }

    .field-note {
        display: block;
        margin-top: 4px;
        color: #a59e93;
        font-size: 11px;
        font-style: italic;
    }

    .form-input {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e8e4df;
        border-radius: 8px;
        font-size: 15px;
        color: #4e4934;
        background: #faf8f5;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #4e4934;
        background: white;
        box-shadow: 0 4px 12px rgba(78, 73, 52, 0.1);
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 36px;
        background: none;
        border: none;
        color: #635c55;
        cursor: pointer;
        font-size: 13px;
    }

    .password-strength {
        margin-top: 8px;
    }

    .strength-bar {
        height: 3px;
        background: #e8e4df;
        border-radius: 2px;
        margin-bottom: 4px;
        width: 0%;
        transition: all 0.3s ease;
    }

    .strength-text {
        font-size: 11px;
        color: #a59e93;
    }

    #strengthText {
        font-weight: 500;
    }

    .checkbox-group {
        margin-bottom: 15px;
    }

    .checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label input {
        margin-top: 2px;
        accent-color: #4e4934;
    }

    .checkbox-text {
        font-size: 13px;
        color: #635c55;
        line-height: 1.4;
    }

    .inline-link {
        color: #4e4934;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
    }

    .inline-link:hover {
        text-decoration: underline;
    }

    .signup-button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(45deg, #4e4934, #635c55);
        color: #dbd1c8;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 25px;
        flex-shrink: 0;
    }

    .signup-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(78, 73, 52, 0.2);
    }

    .divider {
        text-align: center;
        position: relative;
        margin: 25px 0;
        color: #a59e93;
        flex-shrink: 0;
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
        padding: 0 15px;
        position: relative;
        font-size: 13px;
    }

    .social-signup {
        display: flex;
        gap: 12px;
        margin-bottom: 25px;
        flex-shrink: 0;
    }

    .social-button {
        flex: 1;
        padding: 12px;
        border: 2px solid #e8e4df;
        border-radius: 8px;
        background: white;
        color: #4e4934;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .social-button:hover {
        border-color: #4e4934;
        background: #faf8f5;
    }

    .social-icon {
        font-size: 16px;
    }

    .login-link {
        text-align: center;
        color: #635c55;
        font-size: 13px;
        flex-shrink: 0;
        margin-top: auto;
        padding-top: 15px;
    }

    .login-link a {
        color: #4e4934;
        text-decoration: none;
        font-weight: 500;
        margin-left: 5px;
        transition: color 0.3s ease;
    }

    .login-link a:hover {
        color: #635c55;
        text-decoration: underline;
    }

    /* Loading Animation */
    .loading {
        display: none;
        text-align: center;
        margin-top: 15px;
        flex-shrink: 0;
    }

    .spinner {
        width: 35px;
        height: 35px;
        border: 2px solid #e8e4df;
        border-top: 2px solid #4e4934;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Error/Success Messages */
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: none;
        flex-shrink: 0;
        font-size: 13px;
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

    /* Custom Scrollbar */
    .form-content-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .form-content-wrapper::-webkit-scrollbar-track {
        background: #f8f8f8;
        border-radius: 3px;
    }

    .form-content-wrapper::-webkit-scrollbar-thumb {
        background: #d1c9bf;
        border-radius: 3px;
    }

    .form-content-wrapper::-webkit-scrollbar-thumb:hover {
        background: #b8aea2;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .signup-container {
            flex-direction: column;
            max-width: 500px;
            max-height: 90vh;
        }

        .signup-brand {
            padding: 30px;
            min-height: 200px;
            min-width: auto;
        }

        .brand-logo img {
            max-width: 250px;
        }

        .signup-form-container {
            min-width: auto;
        }

        .form-content-wrapper {
            padding: 30px;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }

    @media (max-width: 480px) {
        .signup-container {
            border-radius: 10px;
            max-height: 95vh;
        }

        .signup-page-content {
            padding: 15px;
        }

        .signup-brand {
            padding: 20px;
        }

        .brand-logo img {
            max-width: 200px;
        }

        .form-content-wrapper {
            padding: 20px;
        }

        .social-signup {
            flex-direction: column;
        }

        .form-header h1 {
            font-size: 26px;
        }
    }
</style>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const toggleButtons = document.querySelectorAll('.password-toggle');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButtons.forEach(btn => {
                if (btn.getAttribute('onclick').includes(fieldId)) {
                    btn.textContent = 'Hide';
                }
            });
        } else {
            passwordInput.type = 'password';
            toggleButtons.forEach(btn => {
                if (btn.getAttribute('onclick').includes(fieldId)) {
                    btn.textContent = 'Show';
                }
            });
        }
    }

    // Password strength checker
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        let strength = 0;
        let color = '#c00';
        let text = 'None';
        let width = '0%';
        
        // Check password criteria
        if (password.length > 0) {
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update strength display
            switch(strength) {
                case 1:
                    color = '#c00';
                    text = 'Weak';
                    width = '25%';
                    break;
                case 2:
                    color = '#f90';
                    text = 'Fair';
                    width = '50%';
                    break;
                case 3:
                    color = '#fc0';
                    text = 'Good';
                    width = '75%';
                    break;
                case 4:
                    color = '#090';
                    text = 'Strong';
                    width = '100%';
                    break;
            }
        }
        
        // Update UI
        strengthBar.style.width = width;
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    });

    // Confirm password validation
    document.getElementById('confirm_password').addEventListener('input', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = e.target.value;
        
        if (confirmPassword && password !== confirmPassword) {
            e.target.style.borderColor = '#c00';
        } else {
            e.target.style.borderColor = '';
        }
    });

    // Form submission
    document.querySelector('.signup-form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const terms = document.getElementById('terms').checked;
        
        // Validate password match
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
        
        // Validate terms agreement
        if (!terms) {
            e.preventDefault();
            alert('You must agree to the Terms & Conditions and Privacy Policy');
            return false;
        }
        
        // Show loading
        document.getElementById('loading').style.display = 'block';
    });

    // Social signup simulation
    function socialSignup(provider) {
        document.getElementById('loading').style.display = 'block';
        
        setTimeout(() => {
            document.getElementById('loading').style.display = 'none';
            alert(`Redirecting to ${provider} signup...`);
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
        
        // Set max date for date of birth (must be at least 13 years old)
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
        document.getElementById('dob').max = maxDate.toISOString().split('T')[0];
    };
</script>

<?php 
include 'C:/xampp/htdocs/CHADET/footer.php';
?>