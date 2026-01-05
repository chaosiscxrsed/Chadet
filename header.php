<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - CHADET' : 'CHADET - Luxury Cosmetics'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <p>FREE SHIPPING ON ORDERS OVER Rs.5000 | NEW ARRIVALS NOW AVAILABLE</p>
    </div>

    <!-- Navigation -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-left">
                <a href="products.php?category=lips">lips</a>
                <a href="products.php?category=face">face</a>
                <a href="products.php?category=eyes">eyes</a>
                <a href="products.php?category=sets">sets</a>
            </div>

            <div class="nav-center">
                <a href="index.php" class="logo">
                    <h1 style="font-size: 2.5em;"><img src="logo.png" alt="CHADET Logo" style="width: 450px; height: auto;"></h1>
                </a>
            </div>

            <div class="nav-right">
                <a href="#" class="nav-icon">
                    <i class="fas fa-search"></i>
                </a>
                
                <!-- Wishlist/User Profile Links -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <a href="wishlist.php" class="nav-icon">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count">
                            <?php 
                            // Get wishlist count
                            require_once 'config.php';
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE customer_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $result['count'];
                            } catch (PDOException $e) {
                                echo '0';
                            }
                            ?>
                        </span>
                    </a>
                    
                    <div class="user-dropdown">
                        <a href="#" class="nav-icon user-icon">
                            <i class="fas fa-user"></i>
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
                        </a>
                        <div class="dropdown-content">
                            <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                            <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                            <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <a href="login.php" class="nav-icon" title="Login to view wishlist">
                        <i class="fas fa-heart"></i>
                    </a>
                    
                    <a href="login.php" class="nav-icon">
                        <i class="fas fa-user"></i>
                        <span class="login-text">Login</span>
                    </a>
                <?php endif; ?>
                
                <!-- Cart Icon -->
                <a href="cart.php" class="nav-icon cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count">
                        <?php 
                        // Get cart count from session
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            $cart_count = 0;
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_count += $item['quantity'] ?? 1;
                            }
                            echo $cart_count;
                        } else {
                            echo '0';
                        }
                        ?>
                    </span>
                </a>
            </div>
        </nav>
    </header>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay">
        <div class="search-container">
            <button class="close-search" id="closeSearch">
                <i class="fas fa-times"></i>
            </button>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search for products...">
                <button id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>

<style>
    /* Search Overlay Styles */
    .search-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        z-index: 9999;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .search-overlay.active {
        display: flex;
        opacity: 1;
        align-items: flex-start;
        justify-content: center;
        padding-top: 100px;
    }

    .search-container {
        width: 90%;
        max-width: 800px;
        position: relative;
    }

    .close-search {
        position: absolute;
        right: 0;
        top: -60px;
        background: none;
        border: none;
        font-size: 24px;
        color: #4e4934;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .close-search:hover {
        color: #635c55;
    }

    .search-box {
        display: flex;
        margin-bottom: 30px;
        border-bottom: 2px solid #4e4934;
        padding-bottom: 10px;
    }

    .search-box input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 24px;
        color: #4e4934;
        background: transparent;
        padding: 10px 0;
    }

    .search-box button {
        background: none;
        border: none;
        font-size: 24px;
        color: #4e4934;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .search-box button:hover {
        color: #635c55;
    }

    .search-results {
        max-height: 400px;
        overflow-y: auto;
    }

    /* User Dropdown Styles */
    .user-dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 1000;
        border-radius: 8px;
        padding: 10px 0;
    }

    .user-dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        display: block;
        padding: 10px 20px;
        color: #4e4934;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.3s ease;
    }

    .dropdown-content a:hover {
        background: #faf8f5;
    }

    .dropdown-content i {
        width: 20px;
        margin-right: 10px;
    }

    .dropdown-divider {
        height: 1px;
        background: #e8e4df;
        margin: 5px 0;
    }

    .user-name {
        font-size: 12px;
        margin-left: 5px;
    }

    .login-text {
        font-size: 12px;
        margin-left: 5px;
    }

    /* Cart Count Badge */
    .cart-count, .wishlist-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #4e4934;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .nav-right .nav-icon .user-name,
        .nav-right .nav-icon .login-text {
            display: none;
        }
        
        .dropdown-content {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            border-radius: 20px 20px 0 0;
        }
    }
</style>

<script>
    // Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Search icon click
        const searchIcon = document.querySelector('.nav-icon .fa-search').closest('.nav-icon');
        const searchOverlay = document.getElementById('searchOverlay');
        const closeSearch = document.getElementById('closeSearch');
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const searchResults = document.getElementById('searchResults');

        // Open search overlay
        if (searchIcon) {
            searchIcon.addEventListener('click', function(e) {
                e.preventDefault();
                searchOverlay.classList.add('active');
                searchInput.focus();
            });
        }

        // Close search overlay
        if (closeSearch) {
            closeSearch.addEventListener('click', function() {
                searchOverlay.classList.remove('active');
                searchInput.value = '';
                searchResults.innerHTML = '';
            });
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                searchOverlay.classList.remove('active');
                searchInput.value = '';
                searchResults.innerHTML = '';
            }
        });

        // Search functionality
        if (searchButton && searchInput) {
            // Search on button click
            searchButton.addEventListener('click', performSearch);
            
            // Search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Search on input (with debounce)
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                if (this.value.length >= 2) {
                    searchTimeout = setTimeout(performSearch, 300);
                } else {
                    searchResults.innerHTML = '';
                }
            });
        }

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                searchResults.innerHTML = '<p class="search-message">Please enter at least 2 characters</p>';
                return;
            }

            searchResults.innerHTML = '<p class="search-loading">Searching...</p>';

            // AJAX request to search endpoint
            fetch(`search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.products.length > 0) {
                        let html = '<div class="search-results-grid">';
                        data.products.forEach(product => {
                            html += `
                                <a href="product.php?id=${product.id}" class="search-result-item">
                                    <div class="search-result-image">
                                        <img src="${product.image_url}" alt="${product.name}">
                                    </div>
                                    <div class="search-result-info">
                                        <h4>${product.name}</h4>
                                        <p class="search-result-price">$${product.price}</p>
                                        <p class="search-result-category">${product.category}</p>
                                    </div>
                                </a>
                            `;
                        });
                        html += '</div>';
                        searchResults.innerHTML = html;
                    } else {
                        searchResults.innerHTML = '<p class="search-message">No products found</p>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<p class="search-message">Error searching products</p>';
                });
        }
    });
</script>