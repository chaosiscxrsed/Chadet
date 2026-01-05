<?php
$page_title = 'Shopping Cart';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sample products data for cart display
$products = [
    1 => ['name' => 'NUDE BROWN "WOODET" LIPSTICK', 'price' => 1200, 'image' => 'chadet7.jpg'],
    2 => ['name' => 'RADIANT GLOW FOUNDATION', 'price' => 1250, 'image' => 'images/6-stand.jpeg'],
    3 => ['name' => 'BELOVED SET', 'price' => 2650, 'image' => 'images/beloved.jpg'],
    4 => ['name' => 'COFFEE LIP GLOSS', 'price' => 1250, 'image' => 'chadet8.jpg']
];

// Handle cart actions - FIXED LINES 14 & 26
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_cart' && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        }
        header('Location: cart.php');
        exit;
    }

    if ($_POST['action'] === 'remove_item' && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        header('Location: cart.php');
        exit;
    }
}

// Calculate totals
$subtotal = 0;
$item_count = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    if (isset($products[$product_id])) {
        $subtotal += $products[$product_id]['price'] * $quantity;
        $item_count += $quantity;
    }
}

$shipping = $subtotal >= 5000 ? 0 : 150;
$total = $subtotal + $shipping;

include 'header.php';
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 50px;
}

.cart-header {
    text-align: center;
    margin-bottom: 60px;
}

.cart-header h1 {
    font-size: 36px;
    color: #333;
    margin-bottom: 10px;
    font-weight: 600;
}

.cart-header p {
    color: #666;
    font-size: 16px;
}

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 60px;
}

.cart-items {
    background: white;
}

.cart-item {
    display: grid;
    grid-template-columns: 120px 1fr auto auto auto;
    gap: 20px;
    align-items: center;
    padding: 30px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    object-fit: cover;
}

.item-details h3 {
    font-size: 16px;
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}

.item-details p {
    color: #666;
    font-size: 14px;
}

.item-price {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    border-color: #d63384;
    color: #d63384;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    font-size: 14px;
}

.remove-btn {
    background: none;
    border: none;
    color: #999;
    font-size: 18px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.remove-btn:hover {
    color: #d63384;
}

.cart-summary {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    height: fit-content;
    position: sticky;
    top: 120px;
}

.summary-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #333;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 14px;
}

.summary-row.total {
    font-size: 18px;
    font-weight: 600;
    padding-top: 15px;
    border-top: 1px solid #ddd;
    margin-top: 20px;
}

.shipping-note {
    background: #e8f5e8;
    color: #28a745;
    padding: 15px;
    border-radius: 8px;
    font-size: 14px;
    margin: 20px 0;
    text-align: center;
}

.checkout-btn {
    width: 100%;
    background: #333;
    color: white;
    padding: 18px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.checkout-btn:hover {
    background: #555;
}

.btn-secondary {
    background: #333;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-secondary:hover {
    background: #555;
}

.continue-shopping {
    display: block;
    text-align: center;
    color: #666;
    text-decoration: none;
    margin-top: 15px;
    font-size: 14px;
    transition: color 0.3s ease;
}

.continue-shopping:hover {
    color: #666;
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.empty-cart h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

.empty-cart p {
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .cart-container {
        padding: 40px 20px;
    }

    .cart-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 15px;
    }

    .item-image {
        width: 80px;
        height: 80px;
    }

    .item-price, .quantity-controls, .remove-btn {
        grid-column: 2;
        justify-self: start;
        margin-top: 10px;
    }
}
</style>

<div class="cart-container">
    <div class="cart-header">
        <h1>SHOPPING CART</h1>
        <p><?php echo $item_count; ?> item(s) in your cart</p>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Add some beautiful products to get started!</p>
            <a href="products.php" class="btn-primary">CONTINUE SHOPPING</a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <form method="POST" id="cartForm">
                    <input type="hidden" name="action" value="update_cart">

                    <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                        <?php if (isset($products[$product_id])): ?>
                            <?php $product = $products[$product_id]; ?>
                            <div class="cart-item">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="item-image">

                                <div class="item-details">
                                    <h3><?php echo strtoupper($product['name']); ?></h3>
                                    <p>Color: Rouge</p>
                                </div>

                                <div class="item-price">Rs.<?php echo $product['price']; ?></div>

                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $product_id; ?>, -1)">-</button>
                                    <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" class="quantity-input" id="qty_<?php echo $product_id; ?>">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $product_id; ?>, 1)">+</button>
                                </div>

                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="remove_item">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" class="remove-btn" title="Remove item">×</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-secondary">UPDATE CART</button>
                    </div>
                </form>
            </div>

            <div class="cart-summary">
                <h3 class="summary-title">ORDER SUMMARY</h3>

                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>Rs.<?php echo number_format($subtotal, 2); ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo $shipping > 0 ? 'Rs.' . number_format($shipping, 2) : 'FREE'; ?></span>
                </div>

                <?php if ($shipping > 0 && $subtotal < 5000): ?>
                    <div class="shipping-note">
                        Add Rs.<?php echo number_format(5000 - $subtotal, 2); ?> more for FREE shipping!
                    </div>
                <?php elseif ($shipping == 0): ?>
                    <div class="shipping-note">
                        🎉 You qualify for FREE shipping!
                    </div>
                <?php endif; ?>

                <div class="summary-row total">
                    <span>Total:</span>
                    <span>Rs.<?php echo number_format($total, 2); ?></span>
                </div>

                <button type="button" class="checkout-btn" onclick="proceedToCheckout()">
                    PROCEED TO CHECKOUT
                </button>

                <a href="products.php" class="continue-shopping">← Continue Shopping</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(productId, change) {
    const input = document.getElementById('qty_' + productId);
    let newQuantity = parseInt(input.value) + change;

    if (newQuantity >= 1) {
        input.value = newQuantity;
        // Auto-submit form when quantity changes
        document.getElementById('cartForm').submit();
    }
}

function proceedToCheckout() {
    alert('Checkout functionality would be implemented here!\n');
}

// Auto-update cart when quantity changes
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('cartForm').submit();
    });
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

<?php include 'footer.php'; ?>