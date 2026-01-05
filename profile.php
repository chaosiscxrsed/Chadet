<?php
require_once 'config.php';
require_once 'auth_functions.php';

$page_title = 'My Profile';
requireLogin($pdo);

// Get user data
$user = getUserById($pdo, $_SESSION['user_id']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    
    $errors = [];
    
    // Validation
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    
    // Check if email changed and if new email exists
    if ($email !== $user['email']) {
        if (emailExists($pdo, $email)) {
            $errors[] = "Email already registered.";
        }
    }
    
    if (empty($errors)) {
        $update_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'date_of_birth' => $dob
        ];
        
        if (updateUserProfile($pdo, $_SESSION['user_id'], $update_data)) {
            $_SESSION['success'] = 'Profile updated successfully';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
    } else {
        $_SESSION['error'] = implode(' ', $errors);
    }
}

include 'header.php';
?>

<div class="profile-page">
    <div class="container">
        <h1>My Profile</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-nav">
                    <a href="profile.php" class="active"><i class="fas fa-user"></i> Personal Info</a>
                    <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                    <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                </div>
            </div>
            
            <div class="profile-main">
                <form method="POST" class="profile-form">
                    <div class="form-row">
                        <div class="form-group half">
                            <label>First Name *</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group half">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo $user['date_of_birth'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Member Since</label>
                        <p class="read-only"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    
                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-page {
        padding: 60px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .profile-page h1 {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        color: #4e4934;
        margin-bottom: 40px;
    }

    .profile-content {
        display: flex;
        gap: 40px;
    }

    .profile-sidebar {
        flex: 0 0 250px;
    }

    .profile-nav {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .profile-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        color: #635c55;
        text-decoration: none;
        border-radius: 5px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }

    .profile-nav a:hover,
    .profile-nav a.active {
        background: #faf8f5;
        color: #4e4934;
    }

    .profile-main {
        flex: 1;
    }

    .profile-form {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.half {
        flex: 1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #635c55;
        font-size: 14px;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e8e4df;
        border-radius: 8px;
        font-size: 15px;
        color: #4e4934;
        background: #faf8f5;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #4e4934;
        background: white;
    }

    .read-only {
        padding: 12px 0;
        color: #4e4934;
        font-weight: 500;
    }

    .btn-save {
        background: linear-gradient(45deg, #4e4934, #635c55);
        color: white;
        border: none;
        padding: 14px 30px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(78, 73, 52, 0.2);
    }

    @media (max-width: 768px) {
        .profile-content {
            flex-direction: column;
        }
        
        .profile-sidebar {
            flex: none;
            width: 100%;
        }
        
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>

<?php include 'footer.php'; ?>