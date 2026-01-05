// CHADET Cosmetics - Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    initMobileMenu();

    // Newsletter form handling
    initNewsletterForm();

    // Smooth scrolling for anchor links
    initSmoothScrolling();

    // Cart counter animation
    animateCartCounter();

    // Image lazy loading
    initLazyLoading();
});

// Mobile Menu Functionality
function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navbar = document.querySelector('.navbar');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navbar.classList.toggle('mobile-open');
        });
    }
}

// Newsletter Form
function initNewsletterForm() {
    const newsletterForms = document.querySelectorAll('.newsletter-form');

    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = this.querySelector('input[type="email"]').value;
            const button = this.querySelector('button');
            const originalText = button.textContent;

            // Simulate API call
            button.textContent = 'SUBSCRIBING...';
            button.disabled = true;

            setTimeout(() => {
                button.textContent = 'SUBSCRIBED!';
                button.style.background = '#28a745';

                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                    button.disabled = false;
                    this.reset();
                }, 2000);
            }, 1000);

            // Show success message
            showNotification('Thank you for subscribing to CHADET updates!', 'success');
        });
    });
}

// Smooth Scrolling
function initSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Cart Counter Animation
function animateCartCounter() {
    const cartCount = document.querySelector('.cart-count');

    if (cartCount) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    cartCount.style.transform = 'scale(1.3)';
                    setTimeout(() => {
                        cartCount.style.transform = 'scale(1)';
                    }, 200);
                }
            });
        });

        observer.observe(cartCount, {
            childList: true,
            characterData: true,
            subtree: true
        });
    }
}

// Lazy Loading for Images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Product Quick View
function openQuickView(productId) {
    // This would typically load product data via AJAX
    console.log('Opening quick view for product:', productId);

    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'quick-view-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="closeQuickView()"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closeQuickView()">&times;</button>
            <div class="quick-view-content">
                <h3>Quick View - Product ${productId}</h3>
                <p>Quick view functionality would be implemented here.</p>
                <button class="btn-primary" onclick="addToCart(${productId})">ADD TO CART</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    const modal = document.querySelector('.quick-view-modal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

// Add to Cart Function
function addToCart(productId, quantity = 1) {
    // This would typically send data to the server
    console.log(`Adding product ${productId} to cart (quantity: ${quantity})`);

    // Simulate adding to cart
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const currentCount = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = currentCount + quantity;
    }

    showNotification('Product added to cart!', 'success');
}

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;

    // Add notification styles if not already present
    if (!document.querySelector('#notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                z-index: 10000;
                display: flex;
                align-items: center;
                gap: 15px;
                max-width: 300px;
                animation: slideIn 0.3s ease;
            }

            .notification-success {
                border-left: 4px solid #28a745;
            }

            .notification-error {
                border-left: 4px solid #dc3545;
            }

            .notification-info {
                border-left: 4px solid #d63384;
            }

            .notification button {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                color: #999;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Search Functionality
function initSearch() {
    const searchBtn = document.querySelector('.fa-search');

    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = prompt('Search CHADET products:');
            if (searchTerm) {
                // In a real application, this would redirect to search results
                window.location.href = `products.php?search=${encodeURIComponent(searchTerm)}`;
            }
        });
    }
}

// Product Filters
function filterProducts(category) {
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        const productCategory = product.dataset.category;

        if (category === 'all' || productCategory === category) {
            product.style.display = 'block';
            product.style.animation = 'fadeIn 0.3s ease';
        } else {
            product.style.display = 'none';
        }
    });

    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('category', category);
    window.history.pushState({}, '', url);
}

// Wishlist Functionality
function toggleWishlist(productId) {
    const wishlistBtn = document.querySelector(`[data-product-id="${productId}"] .wishlist-btn`);

    if (wishlistBtn) {
        wishlistBtn.classList.toggle('active');

        if (wishlistBtn.classList.contains('active')) {
            wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
            showNotification('Added to wishlist!', 'success');
        } else {
            wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
            showNotification('Removed from wishlist', 'info');
        }
    }
}

// Initialize search when DOM loads
document.addEventListener('DOMContentLoaded', initSearch);
