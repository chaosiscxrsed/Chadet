<?php
require_once 'C:xampp/htdocs/CHADET/config.php';
$page_title = 'Home';
include 'C:xampp/htdocs/CHADET/header.php';
?>
<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">JUST DROPPED</div>
            <h1>MOCHA MUSE<br>COLLECTION</h1>
            <p>Luxurious, long-lasting color that feels weightless. Our new Mocha Muse formula delivers bold, rich pigment with an ultra-smooth finish.</p>
            <a href="products.php?category=all" class="btn-primary">SHOP NOW</a>
        </div>
        <div class="hero-image">
            <img src="chadet1.jpg" alt="CHADET Model" style="border-radius: 15px; max-height: 500px;">
        </div>
    </div>
</section>

<!-- Product Categories -->
<section class="categories">
    <div class="section-title">
        <h2>SHOP BY CATEGORY</h2>
        <p>Discover our complete range of luxury cosmetics</p>
    </div>

    <div class="category-grid">
        <div class="category-card">
            <img src="chadet6.jpg" alt="Lips">
            <a href="products.php?category=lips"><div class="category-overlay">
                <h3>lips</h3>
                <p>Lipsticks, glosses & liners</p>
            </div></a>
        </div>

        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop" alt="Face">
            <a href="products.php?category=face"><div class="category-overlay">
                <h3>face</h3>
                <p>Foundation, concealer & bronzer</p>
            </div></a>
        </div>

        <div class="category-card">
            <img src="chadet2.jpg" alt="Eyes">
            <a href="products.php?category=eyes"><div class="category-overlay">
                <h3>eyes</h3>
                <p>Eyeshadows, mascara & liner</p>
            </div></a>
        </div>

        <div class="category-card">
            <img src="chadet5.jpg" alt="Sets">
            <a href="products.php?category=sets"><div class="category-overlay">
                <h3>sets</h3>
                <p>Curated beauty collections</p>
            </div></a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products">
    <div class="section-title">
        <h2>BESTSELLERS</h2>
        <p>Our most loved products</p>
    </div>

    <div class="product-grid">
        <div class="product-card">
            <div class="product-image">
                <img src="chadet7.jpg" alt="Velvet Matte Lipstick">
                <div class="product-overlay">
                    <a href="product-detail.php?id=1" class="quick-view"> VIEW</a>
                </div>
            </div>
            <div class="product-info">
                    <h3>NUDE BROWN "WOODET" LIPSTICK</h3>
                    <div class="product-price">Rs.1450</div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <img src="images/Beloved.jpg" alt="Glow Foundation">
                <div class="product-overlay">
                    <a href="product-detail.php?id=2" class="quick-view">VIEW</a>
                </div>
            </div>
            <div class="product-info">
                <h3>BELOVED SET</h3>
                    <div class="product-price">Rs.2000</div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <img src="chadet9.jpg" alt="Eyeshadow Palette">
                <div class="product-overlay">
                    <a href="product-detail.php?id=3" class="quick-view"> VIEW</a>
                </div>
            </div>
            <div class="product-info">
                    <h3>SPIRAL GLIDE EYELINER</h3>
                    <div class="product-price">Rs.1100</div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <img src="chadet8.jpg" alt="Lip Gloss">
                <div class="product-overlay">
                    <a href="product-detail.php?id=4" class="quick-view"> VIEW</a>
                </div>
            </div>
            <div class="product-info">
                    <h3>COFFEE "BROOKIE 04" LIPSTICK</h3>
                    <div class="product-price">Rs.1450</div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup -->
<section style="background: linear-gradient(135deg, #e0c6ad 10%, #4e4934 90%); padding: 80px 50px; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <h2 style="font-size: 36px; color: #524c47; margin-bottom: 20px;">STAY IN THE LOOP</h2>
        <p style="color: #4c4945;; margin-bottom: 30px; font-size: 16px;">Be the first to know about new launches, exclusive offers, and beauty tips from CHADET.</p>
        <form style="display: flex; gap: 15px; max-width: 400px; margin: 0 auto;">
            <input type="email" placeholder="Enter your email address" style="flex: 1; padding: 15px; border: none; border-radius: 8px; font-size: 14px;" required>
            <button type="submit" class="btn-primary" style="white-space: nowrap;">SUBSCRIBE</button>
        </form>
    </div>
</section>

<?php include 'C:xampp/htdocs/CHADET/footer.php'; ?>
