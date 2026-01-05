<?php
require_once 'C:xampp/htdocs/CHADET/config.php';

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$page_title = ucfirst($category) . ' Products';

include 'C:xampp/htdocs/CHADET/header.php';

// Sample products data (in a real application, this would come from the database)
$products = [
    ['id' => 1, 'name' => 'NUDE BROWN "WOODET" LIPSTICK', 'price' => 1200, 'category' => 'lips', 'image' => 'chadet7.jpg'],
    ['id' => 2, 'name' => 'RADIANT GLOW FOUNDATION', 'price' => 1250, 'category' => 'face', 'image' => 'images/6-stand.jpeg'],
    ['id' => 3, 'name' => 'SPIRAL GLIDE EYELINER', 'price' => 1000, 'category' => 'eyes', 'image' => 'chadet9.jpg'],
    ['id' => 4, 'name' => 'COFFEE LIP GLOSS', 'price' => 1250, 'category' => 'lips', 'image' => 'chadet8.jpg'],
    ['id' => 5, 'name' => 'COFFEE "BROOKIE04" LIPSTICK', 'price' => 1250, 'category' => 'lips', 'image' => 'images/Brookie.jpeg'],
    ['id' => 6, 'name' => 'SQUISHY LIPSTICK', 'price' => 1250, 'category' => 'lips', 'image' => 'images/sobert.jpg'],
    ['id' => 7, 'name' => 'BELOVED SET', 'price' => 2650, 'category' => 'sets', 'image' => 'images/beloved.jpg'],
    ['id' => 8, 'name' => 'CONTOURING SET', 'price' => 6500, 'category' => 'sets', 'image' => 'images/samna.jpeg'],
    ['id' => 9, 'name' => 'LIQUID LIPSTICK', 'price' => 1250, 'category' => 'lips', 'image' => 'chadet4.jpg'],
    ['id' => 10, 'name' => 'CONCEALER PALLETE', 'price' => 2500, 'category' => 'face', 'image' => 'images/concealer.jpeg'],
    ['id' => 11, 'name' => 'SMOKEY EYE KIT', 'price' => 1500, 'category' => 'eyes', 'image' => 'images/5-stand.jpeg'],
    ['id' => 12, 'name' => 'COMPLETEE MAKEUP SET', 'price' => 12500, 'category' => 'sets', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=300&h=350&fit=crop'],
    ['id' => 13, 'name' => 'BELOVED FOUNDATION', 'price' => 1250, 'category' => 'face', 'image' => 'images/beloved.jpg'],
    ['id' => 14, 'name' => 'BELOVED LIPLINER', 'price' => 1000, 'category' => 'lips', 'image' => 'images/beloved1.jpeg'],
    ['id' => 15, 'name' => 'MOCHA MUSE LIPLINER', 'price' => 1000, 'category' => 'lips', 'image' => 'images/mocha.jpeg']
];

// Filter products by category
if ($category !== 'all') {
    $products = array_filter($products, function($product) use ($category) {
        return $product['category'] === $category;
    });
}
?>

<style>
.category-header {
    background: linear-gradient(135deg,#4e4934 20%, #e0c6ad 80%);
    padding: 60px 50px;
    text-align: center;
}

.category-header h1 {
    font-size: 48px;
    color: #333;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.category-header p {
    color: #ccc;
    font-size: 18px;
}

.filter-bar {
    background: white;
    padding: 30px 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 60px;
}

.filter-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.filter-categories {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.filter-categories a {
    text-decoration: none;
    color: #666;
    font-weight: 500;
    text-transform: lowercase;
    padding: 8px 0;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}

.filter-categories a.active,
.filter-categories a:hover {
    color: #d63384;
    border-bottom-color: #d63384;
}

.product-count {
    color: #666;
    font-size: 14px;
}

.products-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 50px 80px;
}
</style>

<!-- Category Header -->
<section class="category-header">
    <h1><?php echo $category === 'all' ? 'All Products' : $category; ?></h1>
    <p><?php echo $category === 'all' ? 'Discover our complete collection' : 'Luxury ' . $category . ' products for every look'; ?></p>
</section>

<!-- Filter Bar -->
<section class="filter-bar">
    <div class="filter-container">
        <div class="filter-categories">
            <a href="products.php" class="<?php echo $category === 'all' ? 'active' : ''; ?>">all products</a>
            <a href="products.php?category=lips" class="<?php echo $category === 'lips' ? 'active' : ''; ?>">lips</a>
            <a href="products.php?category=face" class="<?php echo $category === 'face' ? 'active' : ''; ?>">face</a>
            <a href="products.php?category=eyes" class="<?php echo $category === 'eyes' ? 'active' : ''; ?>">eyes</a>
            <a href="products.php?category=sets" class="<?php echo $category === 'sets' ? 'active' : ''; ?>">sets</a>
        </div>
        <div class="product-count">
            <?php echo count($products); ?> products
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="products-container">
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <div class="product-overlay">
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="quick-view">VIEW</a>
                </div>
            </div>
            <div class="product-info">
                <h3><?php echo strtoupper($product['name']); ?></h3>
                <div class="product-price">Rs.<?php echo $product['price']; ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'C:xampp/htdocs/CHADET/footer.php'; ?>
