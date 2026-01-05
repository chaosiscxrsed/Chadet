<?php
require_once 'C:xampp/htdocs/CHADET/config.php';
$page_title = 'Contact Us';

// Handle form submission
$message_sent = false;

// FIXED LINE 7: Check if action exists in POST array first
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    // In a real application, you would process the form data here
    // For this demo, we'll just show a success message
    $message_sent = true;
}

include 'C:xampp/htdocs/CHADET/header.php';
?>

<style>
.contact-hero {
    background: linear-gradient(60deg, #4e4934, #e0c6ad);
    padding: 80px 50px;
    text-align: center;
}

.contact-hero h1 {
    font-size: 48px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.contact-hero p {
    font-size: 18px;
    color: #ccc;
    max-width: 600px;
    margin: 0 auto;
}

.contact-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 50px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
}

.contact-info h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 30px;
    font-weight: 600;
}

.contact-methods {
    margin-bottom: 40px;
}

.contact-method {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #4e4934, #e0c6ad);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
}

.contact-details h3 {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
    font-weight: 600;
}

.contact-details p {
    color: #666;
    line-height: 1.6;
}

.contact-details a {
    color: #4e4934;
    text-decoration: none;
}

.contact-details a:hover {
    text-decoration: underline;
}

.hours-section {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 40px;
}

.hours-section h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.hours-list {
    list-style: none;
}

.hours-list li {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.hours-list li:last-child {
    border-bottom: none;
}

.contact-form {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-form h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 30px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #eee;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #d63384;
}

.form-group textarea {
    height: 120px;
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.submit-btn {
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
}

.submit-btn:hover {
    background:  #4e4934;
    transform: translateY(-2px);
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border: 1px solid #c3e6cb;
}

.faq-section {
    background: #f8f9fa;
    padding: 80px 50px;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-title {
    text-align: center;
    margin-bottom: 50px;
}

.faq-title h2 {
    font-size: 32px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.faq-item {
    background: white;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.faq-question {
    background: white;
    border: none;
    width: 100%;
    padding: 25px;
    text-align: left;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    transition: background 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-question:hover {
    background: #f8f9fa;
}

.faq-answer {
    padding: 0 25px 25px;
    color: #666;
    line-height: 1.6;
    display: none;
}

.faq-answer.active {
    display: block;
}

.faq-icon {
    transition: transform 0.3s ease;
}

.faq-question.active .faq-icon {
    transform: rotate(45deg);
}

@media (max-width: 768px) {
    .contact-hero {
        padding: 60px 20px;
    }

    .contact-hero h1 {
        font-size: 36px;
    }

    .contact-content {
        grid-template-columns: 1fr;
        gap: 60px;
        padding: 60px 20px;
    }

    .contact-form {
        padding: 30px 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .faq-section {
        padding: 60px 20px;
    }
}
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <h1>CONTACT US</h1>
    <p>We're here to help! Reach out with any questions about our products, orders, or just to say hello.</p>
</section>

<!-- Contact Content -->
<div class="contact-content">
    <!-- Contact Information -->
    <div class="contact-info">
        <h2>GET IN TOUCH</h2>

        <div class="contact-methods">
            <div class="contact-method">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="contact-details">
                    <h3>Phone</h3>
                    <p>Customer Service: <a href="tel:+977-9812345678-CHADET-1">+977 (9812345678) CHADET-1</a><br>
                    Monday - Friday: 9AM - 6PM PST</p>
                </div>
            </div>

            <div class="contact-method">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-details">
                    <h3>Email</h3>
                    <p>General: <a href="mailto:hello@chadetcosmetics.com">hello@chadetcosmetics.com</a><br>
                    Press: <a href="mailto:press@chadetcosmetics.com">press@chadetcosmetics.com</a></p>
                </div>
            </div>

            <div class="contact-method">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="contact-details">
                    <h3>Address</h3>
                    <p>CHADET Cosmetics<br>
                    Samakhusi, Kathmandu<br>
                    Nepal</p>
                </div>
            </div>
        </div>

        <div class="hours-section">
            <h3>Customer Service Hours</h3>
            <ul class="hours-list">
                <li><span>Monday - Friday</span><span>9:00 AM - 6:00 PM PST</span></li>
                <li><span>Saturday</span><span>10:00 AM - 4:00 PM PST</span></li>
                <li><span>Sunday</span><span>Closed</span></li>
            </ul>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form">
        <?php if ($message_sent): ?>
            <div class="success-message">
                <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you within 24 hours.
            </div>
        <?php endif; ?>

        <h2>SEND US A MESSAGE</h2>

        <form method="POST">
            <input type="hidden" name="action" value="send_message">

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                    <option value="">Please select...</option>
                    <option value="product-question">Product Question</option>
                    <option value="order-status">Order Status</option>
                    <option value="shipping">Shipping & Returns</option>
                    <option value="partnership">Partnership Inquiry</option>
                    <option value="press">Press Inquiry</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell us how we can help you..." required></textarea>
            </div>

            <button type="submit" class="submit-btn">SEND MESSAGE</button>
        </form>
    </div>
</div>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="faq-container">
        <div class="faq-title">
            <h2>FREQUENTLY ASKED QUESTIONS</h2>
            <p>Quick answers to common questions</p>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>How long does shipping take?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer">
                Standard shipping takes 3-5 business days within the US. Express shipping (1-2 days) and international shipping options are also available at checkout.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>What is your return policy?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer">
                We offer a 30-day return policy for all unused products in original packaging. If you're not completely satisfied, contact us for a full refund or exchange.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Are CHADET products cruelty-free?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer">
                Yes! All CHADET products are 100% cruelty-free. We never test on animals and work only with suppliers who share our commitment to ethical practices.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFAQ(this)">
                <span>Do you offer shade matching services?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer">
                Absolutely! Contact our customer service team with photos in natural lighting, and we'll help you find your perfect shade match. We also offer a shade exchange program.
            </div>
        </div>
    </div>
</section>

<script>
function toggleFAQ(button) {
    const answer = button.nextElementSibling;
    const isActive = answer.classList.contains('active');

    // Close all FAQ answers
    document.querySelectorAll('.faq-answer').forEach(item => {
        item.classList.remove('active');
    });

    document.querySelectorAll('.faq-question').forEach(item => {
        item.classList.remove('active');
    });

    // Open clicked FAQ if it wasn't already active
    if (!isActive) {
        answer.classList.add('active');
        button.classList.add('active');
    }
}
</script>

<?php include 'C:xampp/htdocs/CHADET/footer.php'; ?>
