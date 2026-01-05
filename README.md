# CHADET Cosmetics - Ecommerce Website

A luxury cosmetics ecommerce website built with PHP, HTML, CSS, and JavaScript for XAMPP. Inspired by KylieCosmetics design aesthetic.

## 🚀 Features

- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **Product Catalog** - Browse products by categories (lips, face, eyes, sets)
- **Product Detail Pages** - Detailed product information with image gallery
- **Shopping Cart** - Add, update, and remove items from cart
- **Contact Forms** - Customer inquiries and newsletter subscription
- **Clean UI/UX** - Modern, luxury cosmetics brand aesthetic
- **Database Integration** - MySQL database for products and orders

## 📋 Requirements

- **XAMPP** (Apache + MySQL + PHP)
- **Web Browser** (Chrome, Firefox, Safari, Edge)
- **PHP 7.4+**
- **MySQL 5.7+**

## 🛠️ Installation Instructions

### Step 1: Download and Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup the Project
1. Copy the `chadet-cosmetics` folder to your XAMPP `htdocs` directory
   ```
   C:\xampp\htdocs\chadet-cosmetics\  (Windows)
   /Applications/XAMPP/htdocs/chadet-cosmetics/  (Mac)
   ```

### Step 3: Database Setup
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Create a new database named `chadet_cosmetics`
3. Import the database structure:
   - Go to the SQL tab in phpMyAdmin
   - Copy and paste the contents of `database/setup.sql`
   - Click "Go" to execute the SQL

**Alternative Method:**
- Open the website first and the database will be created automatically
- Then run the SQL file to populate with sample data

### Step 4: Access the Website
1. Open your web browser
2. Navigate to: `http://localhost/chadet-cosmetics/`
3. The website should load with the CHADET homepage

## 📁 Project Structure

```
Chadet/
├── index.php              # Homepage
├── about.php             # About page
├── contact.php           # Contact page
├── products.php          # Product listing
├── product-detail.php    # Product details
├── cart.php              # Shopping cart
├── checkout.php          # Checkout process
├── orders.php            # User orders
├── wishlist.php          # Wishlist
│
├── auth/
│   ├── login.php         # User login
│   ├── login_process.php # Login processing
│   ├── signup.php        # User registration
│   ├── signup_process.php# Registration processing
│   ├── logout.php        # Logout
│   ├── forgot_password.php # Password recovery
│   └── auth_functions.php # Auth helper functions
│
├── user/
│   ├── profile.php       # User profile
│   └── orders.php        # User orders
│
├── admin/
│   ├── admin_dashboard.php    # Admin dashboard
│   ├── admin_login.php        # Admin login
│   ├── admin_products.php     # Product management
│   ├── admin_products_add.php # Add products
│   ├── admin_products_edit.php# Edit products
│   ├── admin_orders.php       # Order management
│   ├── admin_customers.php    # Customer management
│   └── admin_functions.php    # Admin helper functions
│
├── includes/
│   ├── config.php        # Database configuration
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── auth_functions.php # Authentication functions
│
├── assets/
│   ├── images/           # Product images
│   │   ├── chadet1.jpg
│   │   ├── chadet2.jpg
│   │   └── ...
│   ├── style.css         # Main stylesheet
│   └── script.js         # JavaScript file
│
├── database/
│   └── setup.sql         # Database schema
│
├── config.php            # Main configuration
├── README.md             # This file
└── .gitignore           # Git ignore file
```

## 🎨 Design Features

- **Color Scheme**: Pink/rose tones with black and white accents
- **Typography**: Poppins font family for modern look
- **Layout**: Grid-based responsive design
- **Images**: High-quality cosmetic product imagery
- **Animations**: Smooth hover effects and transitions

## 🛒 Functionality

### Homepage
- Hero section with featured products
- Product categories navigation
- Newsletter signup
- Featured bestsellers section

### Product Pages
- Category filtering (lips, face, eyes, sets)
- Product grid with hover effects
- Quick view functionality
- Add to cart functionality

### Shopping Cart
- View cart items
- Update quantities
- Remove items
- Calculate totals with shipping
- Checkout simulation

### Additional Pages
- About page with brand story
- Contact page with form and FAQ
- Responsive navigation

## 🔧 Customization

### Adding New Products
1. Add product images to the `images/` folder (create if needed)
2. Update the `$products` array in relevant PHP files
3. Or use the database to add products via phpMyAdmin

### Changing Colors
1. Open `css/style.css`
2. Modify the CSS custom properties and color values
3. Main brand color: `#d63384` (pink)

### Database Configuration
1. Edit `includes/config.php`
2. Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'chadet_cosmetics');
   ```

## 📱 Browser Compatibility

- ✅ Chrome 70+
- ✅ Firefox 65+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 🚨 Troubleshooting

### Common Issues

**"Database connection failed"**
- Ensure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Verify database name is correct

**"Page not found" or 404 errors**
- Check that files are in the correct `htdocs` directory
- Ensure Apache is running in XAMPP
- Verify the URL: `http://localhost/chadet-cosmetics/`

**Images not loading**
- Check internet connection (using external Unsplash images)
- Replace with local images if needed

**CSS/JavaScript not working**
- Check file paths in includes/header.php
- Ensure files exist in css/ and js/ directories

## 📝 Academic Use

This project is designed for educational purposes and college projects. It demonstrates:

- **Frontend Development**: HTML5, CSS3, JavaScript
- **Backend Development**: PHP server-side scripting
- **Database Design**: MySQL database structure
- **Responsive Design**: Mobile-first approach
- **User Experience**: Ecommerce best practices
- **Project Organization**: Clean file structure

## 🎯 Future Enhancements

Potential features for expansion:
- User authentication and login system
- Payment integration (PayPal, Stripe)
- Order management system
- Product reviews and ratings
- Search functionality
- Admin panel for product management
- Email notifications
- Inventory management

## 📄 License

This project is for educational use only. Product images are from Unsplash and used under their license terms.

## 👨‍💻 Author

Created for college project demonstration.

---

**Note**: This is a demo ecommerce website for educational purposes. No real transactions are processed.
