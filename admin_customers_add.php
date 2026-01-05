<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Add New Customer';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = addCustomer($_POST);
    
    if ($result['success']) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'ADD_CUSTOMER', "Added new customer: " . $_POST['email'], $_SERVER['REMOTE_ADDR']);
        header("Location: admin_customers.php?success=Customer added successfully");
        exit();
    } else {
        $error = $result['message'];
    }
}

require_once 'admin_header.php';
?>

<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .form-header {
        padding: 25px 30px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .form-header h2 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-content {
        padding: 30px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(78, 73, 52, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .password-field {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-color);
        cursor: pointer;
    }

    .password-strength {
        height: 4px;
        margin-top: 5px;
        border-radius: 2px;
        transition: var(--transition);
    }

    .strength-weak { background: var(--danger-color); width: 25%; }
    .strength-fair { background: var(--warning-color); width: 50%; }
    .strength-good { background: var(--info-color); width: 75%; }
    .strength-strong { background: var(--success-color); width: 100%; }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h2>
            <i class="fas fa-user-plus"></i>
            Add New Customer
        </h2>
    </div>

    <div class="form-content">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="customerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <small style="color: var(--text-color); margin-top: 5px; display: block;">
                    Customer will use this to log in
                </small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                <small style="color: var(--text-color); margin-top: 5px; display: block;">
                    Minimum 8 characters with at least one letter and one number
                </small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <button type="button" class="toggle-password" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small id="passwordMatch" style="margin-top: 5px; display: block;"></small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Customer
                </button>
                <a href="admin_customers.php" class="btn" style="background: var(--border-color); color: var(--text-dark);">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleBtn = document.querySelector('.toggle-password i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleBtn.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleBtn.className = 'fas fa-eye';
    }
}

function toggleConfirmPassword() {
    const passwordField = document.getElementById('confirm_password');
    const toggleBtn = document.querySelectorAll('.toggle-password i')[1];
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleBtn.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleBtn.className = 'fas fa-eye';
    }
}

function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('passwordStrength');
    
    // Reset
    strengthBar.className = 'password-strength';
    strengthBar.style.width = '0%';
    
    if (!password) return;
    
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Character variety checks
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    // Update strength bar
    if (strength <= 2) {
        strengthBar.className = 'password-strength strength-weak';
    } else if (strength <= 3) {
        strengthBar.className = 'password-strength strength-fair';
    } else if (strength <= 4) {
        strengthBar.className = 'password-strength strength-good';
    } else {
        strengthBar.className = 'password-strength strength-strong';
    }
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchText = document.getElementById('passwordMatch');
    
    if (!confirmPassword) {
        matchText.textContent = '';
        matchText.style.color = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchText.textContent = '✓ Passwords match';
        matchText.style.color = 'var(--success-color)';
    } else {
        matchText.textContent = '✗ Passwords do not match';
        matchText.style.color = 'var(--danger-color)';
    }
}

// Event listeners
document.getElementById('password').addEventListener('input', function() {
    checkPasswordStrength(this.value);
    checkPasswordMatch();
});

document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

// Form validation
document.getElementById('customerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long');
        return false;
    }
    
    return true;
});
</script>

<?php
require_once 'admin_footer.php';
?>