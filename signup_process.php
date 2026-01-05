<?php
require_once 'C:/xampp/htdocs/CHADET/config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validate inputs
    $errors = [];
    
    // Name validation
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (strlen($first_name) > 50) {
        $errors[] = "First name must be less than 50 characters.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (strlen($last_name) > 50) {
        $errors[] = "Last name must be less than 50 characters.";
    }
    
    // Email validation
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email address must be less than 100 characters.";
    }
    
    // Phone validation (optional)
    if (!empty($phone) && strlen($phone) > 20) {
        $errors[] = "Phone number must be less than 20 characters.";
    }
    
    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // Terms agreement validation
    if (!$terms) {
        $errors[] = "You must agree to the Terms & Conditions and Privacy Policy.";
    }
    
    // Date of birth validation (optional)
    if (!empty($dob)) {
        $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$dob_date || $dob_date->format('Y-m-d') !== $dob) {
            $errors[] = "Please enter a valid date of birth.";
        } else {
            // Check if user is at least 13 years old
            $today = new DateTime();
            $age = $today->diff($dob_date)->y;
            if ($age < 16) {
                $errors[] = "You must be at least 13 years old to register.";
            }
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email address is already registered. Please use a different email or login.";
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Prepare SQL statement
                $sql = "INSERT INTO customers (first_name, last_name, email, password_hash, phone, date_of_birth, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$first_name, $last_name, $email, $password_hash, $phone, $dob]);
                
                $user_id = $pdo->lastInsertId();
                
                // Handle newsletter subscription if selected
                if ($newsletter) {
                    try {
                        $newsletter_sql = "INSERT INTO newsletter_subscribers (email, status, subscribed_at) 
                                           VALUES (?, 'active', NOW())
                                           ON DUPLICATE KEY UPDATE status = 'active', unsubscribed_at = NULL";
                        $newsletter_stmt = $pdo->prepare($newsletter_sql);
                        $newsletter_stmt->execute([$email]);
                    } catch (PDOException $e) {
                        // Newsletter subscription failed, but registration was successful
                        error_log("Newsletter subscription failed: " . $e->getMessage());
                    }
                }
                
                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                $_SESSION['logged_in'] = true;
                
                // Redirect to success page
                header("Location: login.php?success=Registration successful! Welcome to CHADET.");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again later.";
            error_log("Database error: " . $e->getMessage());
        }
    }
    
    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $error_message = implode(" ", $errors);
        header("Location: signup.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If not POST request, redirect to registration page
    header("Location: signup.php");
    exit();
}
?>