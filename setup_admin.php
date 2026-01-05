<?php
// setup_admin.php - RUN THIS ONCE ONLY
echo "<h2>Setting Up Admin Login System</h2>";
echo "<hr>";

// Database connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Change if you have password
$dbname = 'chadet_cosmetics';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database<br>";
} catch(PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Step 1: Create table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT 'admin@chadet.com',
    full_name VARCHAR(100) DEFAULT 'Administrator',
    role VARCHAR(20) DEFAULT 'super_admin',
    status VARCHAR(20) DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "✅ Table created/verified<br>";
} catch(PDOException $e) {
    echo "⚠️ Table creation: " . $e->getMessage() . "<br>";
}

// Step 2: Delete existing admin (to avoid duplicates)
try {
    $pdo->exec("DELETE FROM admin_users WHERE username = 'admin'");
    echo "✅ Cleared existing admin<br>";
} catch(PDOException $e) {
    echo "⚠️ Clear admin: " . $e->getMessage() . "<br>";
}

// Step 3: Insert admin user
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin_users (username, password_hash) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

if ($stmt->execute(['admin', $hashed_password])) {
    echo "✅ Admin user created successfully!<br>";
} else {
    echo "❌ Failed to create admin: " . implode(", ", $stmt->errorInfo()) . "<br>";
}

// Step 4: Verify
$stmt = $pdo->query("SELECT * FROM admin_users");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<hr>";
echo "<h3>Admin User Created:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Username</th><th>Role</th><th>Status</th></tr>";
echo "<tr>";
echo "<td>{$admin['username']}</td>";
echo "<td>{$admin['role']}</td>";
echo "<td>{$admin['status']}</td>";
echo "</tr>";
echo "</table>";

echo "<h3>Login Credentials:</h3>";
echo "<div style='background:#f0f0f0; padding:20px; border-radius:10px;'>";
echo "<p><strong>Username:</strong> admin</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><strong>URL:</strong> http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/admin_login.php</p>";
echo "</div>";

echo "<hr>";
echo "<h2 style='color:green;'>✅ SETUP COMPLETE!</h2>";
echo "<p><a href='admin_login.php' style='background:#4e4934; color:white; padding:15px 30px; text-decoration:none; border-radius:8px; font-size:18px; display:inline-block; margin:20px 0;'>GO TO LOGIN PAGE</a></p>";

echo "<script>";
echo "setTimeout(function() {";
echo "  window.location.href = 'admin_login.php';";
echo "}, 5000);";
echo "</script>";
?>