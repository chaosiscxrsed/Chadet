<?php
require_once 'config.php';
require_once 'auth_functions.php';

$page_title = 'My Wishlist';
requireLogin($pdo);

// Handle add/remove from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_wishlist'])) {
        $product_id = $_POST['product_id'];
        
        try {
            $sql = "INSERT INTO wishlist (customer_id, product_id) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE created_at = NOW()";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $_SESSION['success'] = 'Product added to wishlist';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to add to wishlist';
        }
    } elseif (isset($_POST['remove_from_wishlist'])) {
        $wishlist_id = $_POST['wishlist_id'];
        
        try {
            $sql = "DELETE FROM wishlist WHERE id = ? AND customer_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$wishlist_id, $_SESSION['user_id']]);
            $_SESSION['success'] = 'Product removed from wishlist';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to remove from wishlist';
        }
    }
    
    header("Location: wishlist.php");
    exit();
}

// Get wishlist items
try {
    $sql = "SELECT w.id as wishlist_id, p.*, c.name as category_name 
            FROM wishlist w 
            JOIN products p ON w.product_id = p.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE w.customer_id = ? 
            ORDER BY w.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $wishlist_items = [];
    error_log("Wishlist query error: " . $e->getMessage());
}

include 'header.php';
?>

<div class="wishlist-page">
    <div class="container">
        <h1>My Wishlist</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (empty($wishlist_items)): ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart"></i>
                <h2>Your wishlist is empty</h2>
                <p>Add products you love to your wishlist</p>
                <a href="products.php" class="btn-primary">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="wishlist-item">
                        <a href="product.php?id=<?php echo $item['id']; ?>">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </a>
                        <div class="wishlist-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($item['category_name']); ?></p>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                            
                            <form method="POST" class="wishlist-actions">
                                <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                <button type="submit" name="remove_from_wishlist" class="btn-remove">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                                <a href="cart.php?add=<?php echo $item['id']; ?>" class="btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </a>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .wishlist-page {
        padding: 60px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .wishlist-page h1 {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        color: #4e4934;
        margin-bottom: 40px;
        text-align: center;
    }

    .empty-wishlist {
        text-align: center;
        padding: 60px 20px;
        background: #faf8f5;
        border-radius: 15px;
    }

    .empty-wishlist i {
        font-size: 60px;
        color: #e0c6ad;
        margin-bottom: 20px;
    }

    .empty-wishlist h2 {
        font-size: 24px;
        color: #4e4934;
        margin-bottom: 10px;
    }

    .empty-wishlist p {
        color: #635c55;
        margin-bottom: 30px;
    }

    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .wishlist-item {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .wishlist-item:hover {
        transform: translateY(-5px);
    }

    .wishlist-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .wishlist-info {
        padding: 20px;
    }

    .wishlist-info h3 {
        font-size: 16px;
        color: #4e4934;
        margin-bottom: 5px;
    }

    .wishlist-info .category {
        font-size: 12px;
        color: #a59e93;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .wishlist-info .price {
        font-size: 18px;
        font-weight: 600;
        color: #4e4934;
        margin-bottom: 15px;
    }

    .wishlist-actions {
        display: flex;
        gap: 10px;
    }

    .btn-remove, .btn-add-to-cart {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-remove {
        background: #fee;
        color: #c00;
        border: 1px solid #fcc;
    }

    .btn-remove:hover {
        background: #fdd;
    }

    .btn-add-to-cart {
        background: #4e4934;
        color: white;
        border: 1px solid #4e4934;
    }

    .btn-add-to-cart:hover {
        background: #635c55;
    }

    @media (max-width: 768px) {
        .wishlist-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }

    @media (max-width: 480px) {
        .wishlist-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include 'footer.php'; ?>