<?php
require_once 'admin_functions.php';
requireAdminLogin();

$current_admin = getCurrentAdmin();
$page_title = 'Edit Customer';

$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$customer_id) {
    header("Location: admin_customers.php?error=Customer ID required");
    exit();
}

$customer = getCustomerById($customer_id);

if (!$customer) {
    header("Location: admin_customers.php?error=Customer not found");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateCustomer($customer_id, $_POST);
    
    if ($result['success']) {
        logAdminActivity($current_admin['id'], $current_admin['username'], 'UPDATE_CUSTOMER', "Updated customer: " . $_POST['email'], $_SERVER['REMOTE_ADDR']);
        header("Location: admin_customers.php?success=Customer updated successfully");
        exit();
    } else {
        $error = $result['message'];
    }
}

require_once 'admin_header.php';
?>

<style>
    /* Same styles as add.php */
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
        background: #e0c6ad;
        color: #4e4934;
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

    .password-note {
        background: var(--light-color);
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        border-left: 4px solid var(--info-color);
    }

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
            <i class="fas fa-user-edit"></i>
            Edit Customer: <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
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
                    <input type="text" id="first_name" name="first_name" class="form-control" 
                           value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" 
                           value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                <small style="color: var(--text-color); margin-top: 5px; display: block;">
                    Customer will use this to log in
                </small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                           value="<?php echo htmlspecialchars($customer['date_of_birth'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">New Password (Leave blank to keep current)</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" class="form-control">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                    <button type="button" class="toggle-password" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small id="passwordMatch" style="margin-top: 5px; display: block;"></small>
            </div>

            <div class="password-note">
                <i class="fas fa-info-circle" style="color: var(--info-color);"></i>
                <strong>Note:</strong> Only fill in the password fields if you want to change the customer's password.
                If left blank, the current password will remain unchanged.
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Customer
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

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchText = document.getElementById('passwordMatch');
    
    if (!password && !confirmPassword) {
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
document.getElementById('password').addEventListener('input', checkPasswordMatch);
document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

// Form validation
document.getElementById('customerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password || confirmPassword) {
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
    }
    
    return true;
});
</script>

<?php
require_once 'admin_footer.php';
?>