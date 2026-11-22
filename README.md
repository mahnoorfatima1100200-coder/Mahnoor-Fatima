# Skinora – Beauty & Skincare E-Commerce Website
> **Discover Your Natural Glow.**

A modern, luxurious, database-driven beauty and skincare e-commerce web platform developed with pure **HTML5, CSS3, Vanilla JavaScript, PHP 8+, and MySQL PDO**.

---

## 🌸 Key Features

- **Luxury Brand Aesthetic:** Blush pink, soft beige, cream, rose gold accents, paired with *Playfair Display* and *Poppins* typography.
- **Dynamic Database Architecture:** Full PDO prepared statements, relational schema, and foreign keys across 14 tables in `skinora_db`.
- **Customer Experience:**
  - Promotional announcement banner with discount promo code.
  - Category filtering, price range slider, keyword search, and multi-option sorting (price asc/desc, popularity, newest).
  - Product detail gallery with thumbnail selector, stock indicator, ingredients, application instructions, and customer reviews.
  - Interactive AJAX shopping bag and wishlist with live badge counter updates.
  - Coupon code redemption (`GLOW10`, `SKIN20`, `FREESHIP`).
  - Cash on Delivery (COD) checkout with atomic database transactions and automatic stock deduction.
  - Unique order tracking with multi-stage fulfillment progress bar.
- **Dedicated Admin Control Panel:**
  - Role-protected administrative dashboard at `/admin/`.
  - Real-time business metrics (revenue, total orders, customers, inventory alerts).
  - Full CRUD operations for Products, Categories, Customer Accounts, Order status fulfillment, Reviews moderation, and Discount Coupons.
  - Store sales and inventory valuation analytics.

---

## 📁 Project Structure

```text
skinora/
├── index.php                 # Homepage with hero, categories, best-sellers, reviews
├── shop.php                  # Shop catalog with filters, search, sorting & pagination
├── product.php               # Product details, image gallery, reviews, ingredients
├── category.php              # Category landing page
├── about.php                 # Brand philosophy, clean standards & story
├── contact.php               # Contact form and flagship studio details
├── faq.php                   # Skincare and shipping FAQ
├── privacy-policy.php        # Privacy policy
├── terms.php                 # Terms & conditions
├── login.php                 # Customer login
├── register.php              # Customer registration
├── logout.php                # Session logout
├── cart.php                  # Shopping bag & coupon discounts
├── wishlist.php              # Customer wishlist
├── checkout.php              # Cash on Delivery checkout
├── order-confirmation.php    # Order success invoice
├── my-orders.php             # Customer order history
├── order-details.php         # Customer order breakdown
├── order-tracking.php        # Public order status tracker
├── profile.php               # Profile and saved addresses
├── robots.txt                # Search engine crawler instructions
├── sitemap.xml               # Search engine sitemap
│
├── config/
│   ├── config.php            # Global constants & auto-detect base URL
│   └── database.php          # PDO connection singleton
│
├── includes/
│   ├── header.php            # HTML head, Google Fonts, global CSS & JS
│   ├── navbar.php            # Announcement bar, logo, menus & counters
│   ├── footer.php            # Footer links, newsletter, payment badges
│   ├── functions.php         # Helpers, currency formatting & sanitization
│   ├── auth.php              # Role guards (requireLogin, requireAdmin)
│   └── csrf.php              # CSRF token generation & validation
│
├── actions/
│   ├── register.php          # Customer registration handler
│   ├── login.php             # Customer login handler
│   ├── add-to-cart.php       # AJAX / form add-to-cart
│   ├── update-cart.php       # Cart quantity updater
│   ├── remove-cart.php       # Remove item from cart
│   ├── add-wishlist.php      # Wishlist toggle handler
│   ├── remove-wishlist.php   # Wishlist remover
│   ├── apply-coupon.php      # Coupon discount calculator
│   ├── place-order.php       # PDO transaction checkout & stock decrement
│   ├── submit-review.php     # Product review submission
│   ├── contact.php           # Inquiries handler
│   └── newsletter.php        # Glow Club newsletter subscription
│
├── admin/
│   ├── index.php             # Admin login portal
│   ├── logout.php            # Admin logout
│   ├── dashboard.php         # KPI cards, pending orders, low-stock alerts
│   ├── products.php          # Manage products catalog
│   ├── add-product.php       # Add new product & upload image
│   ├── edit-product.php      # Edit product details
│   ├── delete-product.php    # Safe product deletion
│   ├── categories.php        # Manage categories
│   ├── orders.php            # Orders list with status filters
│   ├── order-details.php     # Admin order invoice & status updater
│   ├── customers.php         # Registered customer directory
│   ├── reviews.php           # Review moderation (approve / reject)
│   ├── coupons.php           # Create and manage discount codes
│   ├── reports.php           # Revenue & inventory valuation reports
│   ├── messages.php          # Customer contact messages
│   └── includes/
│       ├── header.php        # Admin topbar & navigation
│       ├── sidebar.php       # Admin navigation menu
│       ├── footer.php        # Admin scripts
│       └── auth.php          # Administrator authorization guard
│
├── assets/
│   ├── css/
│   │   ├── style.css         # Luxury storefront stylesheet
│   │   ├── responsive.css    # Mobile drawer & responsive breakpoints
│   │   └── admin.css         # Admin dashboard design system
│   ├── js/
│   │   ├── script.js         # Mobile drawer, tabs, thumbnails, toast
│   │   └── cart.js           # AJAX cart, wishlist & quantity controls
│   └── images/               # Vectors, category icons & logo
│
├── uploads/
│   ├── products/             # Product images & vectors
│   └── categories/           # Category badges & icons
│
└── database/
    └── skinora.sql           # Complete schema and realistic sample dataset
```

---

## 🛠️ Local Installation Guide (XAMPP)

1. **Install XAMPP**
   - Download and install XAMPP with Apache and MySQL from [apachefriends.org](https://www.apachefriends.org/).

2. **Copy the Project Files**
   - Copy the `skinora` folder into your XAMPP `htdocs` directory:
     `C:\xampp\htdocs\skinora`

3. **Start Apache & MySQL**
   - Open the **XAMPP Control Panel** and click **Start** for both **Apache** and **MySQL**.

4. **Import the Database**
   - Open your browser and navigate to `http://localhost/phpmyadmin/`.
   - Click on the **Import** tab (or create a database named `skinora_db` first).
   - Select `skinora/database/skinora.sql` and click **Import**.
   - All 14 tables and realistic skincare sample data will be created automatically.

5. **Open the Website**
   - Customer Storefront: [http://localhost/skinora/index.php](http://localhost/skinora/index.php)
   - Admin Control Center: [http://localhost/skinora/admin/index.php](http://localhost/skinora/admin/index.php)

---

## 🌐 Free Hosting Deployment Guide

The code is 100% compatible with free PHP & MySQL hosting providers such as **InfinityFree**, **000webhost**, or any standard cPanel hosting:

1. **Sign Up for Free Hosting**
   - Create a free account on [InfinityFree](https://www.infinityfree.com/) or another provider.
2. **Create MySQL Database**
   - In your hosting control panel, open **MySQL Databases**.
   - Create a new database (e.g. `epiz_xxxx_skinora`).
   - Note down:
     - **Database Host** (e.g., `sql123.infinityfree.com`)
     - **Database Name**
     - **Database Username**
     - **Database Password**
3. **Import SQL File**
   - Open **phpMyAdmin** from your hosting panel.
   - Select your newly created database and click **Import**.
   - Upload `skinora.sql`.
4. **Update Credentials**
   - Open `skinora/config/database.php` and update the constants:
     ```php
     define('DB_HOST', 'sql123.infinityfree.com');
     define('DB_NAME', 'epiz_xxxx_skinora');
     define('DB_USER', 'epiz_xxxx');
     define('DB_PASS', 'your_hosting_password');
     ```
5. **Upload Files via FTP / File Manager**
   - Upload all files from `skinora/` into the `htdocs/` or `public_html/` folder.
   - Ensure `uploads/products/` and `uploads/categories/` have write permissions (chmod `755` or `777`).
6. **Launch Your Store**
   - Visit your assigned free domain (e.g. `https://yourskinora.rf.gd/`).

---

## 🔑 Default Credentials

### Administrator Portal
- **URL:** `http://localhost/skinora/admin/index.php`
- **Email:** `admin@skinora.com`
- **Password:** `admin123`

### Demo Customer Account
- **URL:** `http://localhost/skinora/login.php`
- **Email:** `ayesha@example.com`
- **Password:** `customer123`

### Active Discount Coupons
- `GLOW10` — 10% discount on orders above Rs. 1,500
- `SKIN20` — 20% discount on orders above Rs. 3,500
- `FREESHIP` — Rs. 200 discount for free delivery on orders above Rs. 1,000
