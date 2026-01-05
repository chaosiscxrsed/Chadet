<?php
session_start();
require_once 'C:xampp/htdocs/CHADET/config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$products = [
    1 => [
        'id' => 1,
        'name' => 'NUDE BROWN "WOODET" LIPSTICK',
        'price' => 1200,
        'category' => 'lips',
        'description' => 'Our signature velvet matte lipstick delivers rich, long-lasting color with a weightless feel. The innovative formula glides on smoothly and sets to a comfortable matte finish that won\'t crack or fade.',
        'features' => ['8-hour wear', 'Transfer-resistant', 'Paraben-free', 'Cruelty-free'],
        'shades' => ['Woodet', 'Brookie', 'Squishy', 'Sobert'],
        'image' => 'chadet7.jpg',
        'images' => [
            'chadet8.jpg',
            'images/Brookie.jpeg',
            'images/sobert.jpg'
        ]
    ],
    2 => [
        'id' => 2,
        'name' => 'Beloved Set',
        'price' => 4500,
        'category' => 'face',
        'description' => 'Achieve a flawless, natural-looking complexion with our radiant glow foundation. This lightweight formula provides buildable coverage while hydrating your skin for a healthy, luminous finish.',
        'features' => ['Medium to full coverage', 'SPF 15', 'Hydrating formula', '24-hour wear'],
        'shades' => ['Fair', 'Light', 'Medium', 'Deep'],
        'image' => 'images/beloved.jpg',
        'images' => [
            'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600&h=600&fit=crop',
            'images/concealer.jpeg',
        ]
    ]
];

$product = isset($products[$product_id]) ? $products[$product_id] : $products[1];
$page_title = $product['name'];

// Handle add to cart
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $shade = $_POST['shade'] ?? '';

    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 0;
    }

    $_SESSION['cart'][$product_id] += $quantity;

    header('Location: cart.php');
    exit;
}
include 'C:xampp/htdocs/CHADET/header.php';
?>

<style>
.product-detail {
    max-width: 1400px;
    margin: 0 auto;
    padding: 80px 50px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
}

.product-images {
    position: sticky;
    top: 120px;
}

.main-image {
    width: 100%;
    border-radius: 15px;
    margin-bottom: 20px;
    aspect-ratio: 1;
    object-fit: cover;
}

.image-thumbnails {
    display: flex;
    gap: 15px;
}

.thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.thumbnail.active,
.thumbnail:hover {
    opacity: 1;
}

.product-info {
    padding-top: 20px;
}

.product-category {
    color: #d63384;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.product-title {
    font-size: 36px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.2;
}

.product-price {
    font-size: 24px;
    color: #d63384;
    font-weight: 600;
    margin-bottom: 30px;
}

.product-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 30px;
}

.product-features {
    margin-bottom: 30px;
}

.product-features h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.features-list {
    list-style: none;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.features-list li {
    color: #666;
    font-size: 14px;
    position: relative;
    padding-left: 15px;
}

.features-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #d63384;
    font-weight: bold;
}

.product-options {
    margin-bottom: 40px;
}

.option-group {
    margin-bottom: 25px;
}

.option-label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.shade-options {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.shade-option {
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.shade-option:hover,
.shade-option.selected {
    border-color: #d63384;
    background: #f8d7da;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 15px;
}

.quantity-input {
    width: 60px;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    text-align: center;
    font-size: 16px;
}

.add-to-cart-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.btn-add-cart {
    background: #333;
    color: white;
    padding: 18px 40px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add-cart:hover {
    background: #666;
    transform: translateY(-2px);
}

.product-actions {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.btn-secondary {
    background: transparent;
    color: #333;
    border: 2px solid #333;
    padding: 15px 30px;
    text-decoration: none;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
}

.btn-secondary:hover {
    background: #333;
    color: white;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
        gap: 40px;
        padding: 40px 20px;
    }

    .product-images {
        position: static;
    }

    .product-title {
        font-size: 28px;
    }

    .features-list {
        grid-template-columns: 1fr;
    }

    .product-actions {
        flex-direction: column;
    }
}
</style>

<section class="product-detail">
    <div class="product-images">
        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="main-image" id="mainImage">
        <div class="image-thumbnails">
            <?php foreach ($product['images'] as $index => $image): ?>
            <img src="<?php echo $image; ?>" alt="<?php echo $product['name']; ?>"
                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                 onclick="changeMainImage(this)">
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-info">
        <div class="product-category"><?php echo strtoupper($product['category']); ?></div>
        <h1 class="product-title"><?php echo strtoupper($product['name']); ?></h1>
        <div class="product-price">Rs.<?php echo $product['price']; ?></div>

        <p class="product-description"><?php echo $product['description']; ?></p>

        <div class="product-features">
            <h4>Key Features</h4>
            <ul class="features-list">
                <?php foreach ($product['features'] as $feature): ?>
                <li><?php echo $feature; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <form method="POST" class="add-to-cart-form">
            <input type="hidden" name="action" value="add_to_cart">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <div class="product-options">
                <div class="option-group">
                    <div class="option-label">Select Shade</div>
                    <div class="shade-options">
                        <?php foreach ($product['shades'] as $shade): ?>
                        <div class="shade-option" onclick="selectShade(this)" data-shade="<?php echo $shade; ?>">
                            <?php echo $shade; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="shade" id="selectedShade" required>
                </div>

                <div class="option-group">
                    <div class="option-label">Quantity</div>
                    <div class="quantity-selector">
                        <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-input">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-add-cart">ADD TO CART</button>
        </form>

        <div class="product-actions">
            <a href="#" class="btn-secondary">♡ ADD TO WISHLIST</a>
            <a href="products.php?category=<?php echo $product['category']; ?>" class="btn-secondary">VIEW ALL <?php echo strtoupper($product['category']); ?></a>
        </div>
    </div>
</section>

<script>
function changeMainImage(thumbnail) {
    document.getElementById('mainImage').src = thumbnail.src;

    // Update active thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    thumbnail.classList.add('active');
}

function selectShade(element) {
    // Remove previous selection
    document.querySelectorAll('.shade-option').forEach(option => option.classList.remove('selected'));

    // Add selection to clicked element
    element.classList.add('selected');

    // Update hidden input
    document.getElementById('selectedShade').value = element.dataset.shade;
}

// Auto-select first shade
document.addEventListener('DOMContentLoaded', function() {
    const firstShade = document.querySelector('.shade-option');
    if (firstShade) {
        selectShade(firstShade);
    }
});
</script>

<?php include 'C:xampp/htdocs/CHADET/footer.php'; ?>
