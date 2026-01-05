<?php
require_once 'C:xampp/htdocs/CHADET/config.php';
$page_title = 'About CHADET';
include 'C:xampp/htdocs/CHADET/header.php';
?>

<style>
.about-hero {
    background: linear-gradient(135deg,#4e4934 20%, #e0c6ad 80%);
    padding: 100px 50px;
    text-align: center;
}

.about-hero h1 {
    font-size: 48px;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.about-hero p {
    font-size: 20px;
    color: #ccc;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.about-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 50px;
}

.story-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
    margin-bottom: 100px;
}

.story-text h2 {
    font-size: 36px;
    color: #333;
    margin-bottom: 25px;
    font-weight: 600;
}

.story-text p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
    font-size: 16px;
}

.story-image {
    border-radius: 15px;
    overflow: hidden;
}

.story-image img {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.values-section {
    margin-bottom: 100px;
}

.values-title {
    text-align: center;
    margin-bottom: 60px;
}

.values-title h2 {
    font-size: 36px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.values-title p {
    color: #666;
    font-size: 16px;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.value-card {
    text-align: center;
    padding: 40px 30px;
    background: #f8f9fa;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg,#4e4934 20%, #e0c6ad 80%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 30px;
    color: white;
}

.value-card h3 {
    font-size: 20px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.value-card p {
    color: #666;
    line-height: 1.6;
}

.founder-section {
    background: #f8f9fa;
    padding: 80px 50px;
    border-radius: 20px;
    margin-bottom: 80px;
}

.founder-content {
    max-width: 1000px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 60px;
    align-items: center;
}

.founder-image {
    border-radius: 15px;
    overflow: hidden;
}

.founder-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.founder-text h2 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.founder-text h3 {
    color: #d63384;
    font-size: 16px;
    margin-bottom: 25px;
    font-weight: 500;
}

.founder-text p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
}

.quote {
    border-left: 4px solid #d63384;
    padding-left: 20px;
    margin: 30px 0;
    font-style: italic;
    color: #333;
    font-size: 18px;
}

.cta-section {
    text-align: center;
    padding: 60px 0;
}

.cta-section h2 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.cta-section p {
    color: #666;
    margin-bottom: 30px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .about-hero {
        padding: 60px 20px;
    }

    .about-hero h1 {
        font-size: 36px;
    }

    .about-content {
        padding: 60px 20px;
    }

    .story-section {
        grid-template-columns: 1fr;
        gap: 40px;
        margin-bottom: 60px;
    }

    .founder-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .founder-section {
        padding: 60px 20px;
    }

    .values-section {
        margin-bottom: 60px;
    }
}
</style>

<!-- Hero Section -->
<section class="about-hero">
    <h1>ABOUT CHADET</h1>
    <p>Luxury cosmetics that celebrate individuality, empower confidence, and deliver exceptional quality with every application.</p>
</section>

<!-- About Content -->
<div class="about-content">
    <!-- Brand Story -->
    <section class="story-section">
        <div class="story-text">
            <h2>OUR STORY</h2>
            <p>CHADET was born from a simple belief: that beauty should be accessible, inclusive, and empowering for everyone. What started as a passion project in a small studio has grown into a luxury cosmetics brand that celebrates diversity and self-expression.</p>
            <p>We understand that makeup is more than just products – it's a form of art, a way to express your personality, and a tool for confidence. Every formula we create is designed with this philosophy in mind, combining cutting-edge technology with luxurious ingredients.</p>
            <p>From our signature velvet matte lipsticks to our radiant foundation collection, each product is crafted to deliver professional-quality results while being gentle on your skin.</p>
        </div>
        <div class="story-image">
            <img src="images/2-stand.jpeg" alt="CHADET Brand Story">
        </div>
    </section>

    <!-- Values -->
    <section class="values-section">
        <div class="values-title">
            <h2>OUR VALUES</h2>
            <p>The principles that guide everything we do</p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🌿</div>
                <h3>CLEAN BEAUTY</h3>
                <p>We're committed to clean, cruelty-free formulations that are safe for you and the environment. All our products are paraben-free and made with ethically sourced ingredients.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">✨</div>
                <h3>QUALITY FIRST</h3>
                <p>Every product undergoes rigorous testing to ensure superior performance, longevity, and comfort. We never compromise on quality to meet a price point.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💗</div>
                <h3>INCLUSIVITY</h3>
                <p>Beauty comes in all shades and forms. Our extensive shade ranges and diverse campaigns celebrate the uniqueness of every individual.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🔬</div>
                <h3>INNOVATION</h3>
                <p>We constantly push the boundaries of cosmetic technology, developing new formulas and finishes that set industry standards.</p>
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="founder-section">
        <div class="founder-content">
            <div class="founder-image">
                <img src="prakriti.jpg" alt="PS, Founder">
            </div>
            <div class="founder-text">
                <h2>Meet Prakriti Shrestha</h2>
                <h3>Founder</h3>
                <p>Growing up, I struggled to find makeup that matched my skin tone and expressed my personality. This frustration sparked my passion for creating inclusive, high-quality cosmetics that work for everyone.</p>
                <p>After studying chemistry and working with top beauty brands for over a decade, I launched CHADET with a mission to democratize luxury beauty. Every product we create is tested on my own skin first – if I wouldn't wear it, we won't sell it.</p>
                <div class="quote">
                    "Beauty is not about conforming to standards - it's about expressing your authentic self with confidence."
                </div>
                <p>Today, CHADET is more than a brand; it's a community of beauty lovers who believe that everyone deserves to feel beautiful, confident, and empowered.</p>
            </div>
        </div>
    </section>
</div>

<!-- Call to Action -->
<section class="cta-section">
    <h2>JOIN THE CHADET FAMILY</h2>
    <p>Discover products that celebrate your unique beauty</p>
    <a href="products.php" class="btn-primary">SHOP NOW</a>
</section>

<?php include 'C:xampp/htdocs/CHADET/footer.php'; ?>
