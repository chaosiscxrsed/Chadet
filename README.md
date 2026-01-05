# 🎨 CHADET Cosmetics - Complete E-commerce Platform

A full-featured cosmetics e-commerce website built with PHP, MySQL, and modern web technologies. Features user authentication, product management, shopping cart, and admin dashboard.

## 🌐 Live Demo
*Coming soon - Deploy with XAMPP for local testing*

## ✨ Features

### 👥 User Features
- **User Registration & Login** - Secure authentication system
- **User Profiles** - Personal information management
- **Shopping Cart** - Add, remove, and update products
- **Order Management** - View and track orders
- **Wishlist** - Save favorite products
- **Product Search** - Find products quickly
- **Responsive Design** - Mobile-friendly interface

### 👑 Admin Features
- **Admin Dashboard** - Overview of store performance
- **Product Management** - Add, edit, and delete products
- **Order Management** - Process and track customer orders
- **Customer Management** - View and manage user accounts
- **Secure Admin Login** - Protected admin area

## 🛠️ Tech Stack

### **Frontend**
- HTML5, CSS3 (Custom responsive design)
- JavaScript (Vanilla JS for interactivity)
- Font Awesome Icons

### **Backend**
- PHP 7.4+ (Procedural & Object-Oriented)
- MySQL Database
- PDO for database operations
- Session-based authentication

### **Server**
- XAMPP (Apache, MySQL, PHP)
- Localhost development environment

## 🚀 Installation Guide

### **Prerequisites**
1. [XAMPP](https://www.apachefriends.org/) installed
2. PHP 7.4 or higher
3. MySQL 5.7 or higher
4. Web browser (Chrome, Firefox, etc.)

### **Step 1: Clone the Repository**
```bash
git clone https://github.com/chaosiscxrsed/Chadet.git
cd ChadetSet Up XAMPP
```

### **Step 2: Clone the Repository**
1. Copy the Chadet folder to:
C:/xampp/htdocs/Chadet

2. Start XAMPP Control Panel

3. Start Apache and MySQL services

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

- **Color Scheme**: 

Primary: #4e4934 (Dark Brown)

Secondary: #e0c6ad (Beige)

Accent: #d63384 (Pink)

Background: #faf8f5 (Off-white)

- **Typography**: Poppins font family for modern look
- **Layout**: Grid-based responsive design
- **Images**: High-quality cosmetic product imagery
- **Animations**: Smooth hover effects and transitions

## 🔐 Security Features

Password Hashing - bcrypt password encryption

SQL Injection Prevention - PDO prepared statements

XSS Protection - htmlspecialchars() output encoding

CSRF Protection - Token-based form validation

Session Management - Secure session handling

Input Validation - Server-side validation

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
- Payment integration (PayPal, Stripe)
- Order management system
- Product reviews and ratings
- Search functionality
- Email notifications
- Inventory management

## 📄 License

This project is for educational use only. Product images are from Chadet Nepal and used under their license terms.

## 👨‍💻 Author

Created for college project demonstration.

---

**Note**: This is a demo ecommerce website for educational purposes. No real transactions are processed.
