<?php
/**
 * Skinora - Navigation Bar & Announcement Strip
 */
$db = getDBConnection();
$currentUserId = $_SESSION['user_id'] ?? null;
$cartCount = getCartCount($db, $currentUserId);
$wishlistCount = getWishlistCount($db, $currentUserId);

// Fetch categories for menu dropdown
$navCategoriesStmt = $db->query("SELECT name, slug FROM categories WHERE status = 'active' ORDER BY name ASC LIMIT 8");
$navCategories = $navCategoriesStmt->fetchAll();

$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!-- Promotional Announcement Bar -->
<div class="announcement-bar">
    <div class="container">
        ✨ <strong>Free Delivery</strong> across Pakistan on orders over <strong><?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></strong> | Use Code: <strong>GLOW10</strong> for 10% OFF!
    </div>
</div>

<!-- Header Navigation -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <!-- Mobile Menu Toggle Button -->
            <button type="button" class="menu-toggle" aria-label="Open Navigation Menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <!-- Brand Logo -->
            <div class="logo-wrapper">
                <a href="<?= BASE_URL ?>index.php" class="brand-logo">
                    Skinora<span class="dot">.</span>
                </a>
                <span class="brand-tagline">Natural Glow</span>
            </div>

            <!-- Desktop Navigation Menu -->
            <nav class="main-nav">
                <button type="button" class="nav-close-btn" aria-label="Close Menu">&times;</button>
                <ul>
                    <li><a href="<?= BASE_URL ?>index.php" class="<?= $currentScript === 'index.php' ? 'active' : '' ?>">Home</a></li>
                    <li><a href="<?= BASE_URL ?>shop.php" class="<?= $currentScript === 'shop.php' ? 'active' : '' ?>">Shop</a></li>
                    <li class="user-dropdown">
                        <a href="<?= BASE_URL ?>shop.php">Categories ▾</a>
                        <div class="user-dropdown-menu">
                            <?php foreach ($navCategories as $cat): ?>
                                <a href="<?= BASE_URL ?>category.php?slug=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li><a href="<?= BASE_URL ?>about.php" class="<?= $currentScript === 'about.php' ? 'active' : '' ?>">About Us</a></li>
                    <li><a href="<?= BASE_URL ?>contact.php" class="<?= $currentScript === 'contact.php' ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="<?= BASE_URL ?>order-tracking.php" class="<?= $currentScript === 'order-tracking.php' ? 'active' : '' ?>">Track Order</a></li>
                </ul>
            </nav>

            <!-- Backdrop for Mobile Drawer -->
            <div class="nav-backdrop"></div>

            <!-- Action Icons: Search, Wishlist, Cart, User Account -->
            <div class="header-actions">
                <!-- Search Icon Link to Shop -->
                <a href="<?= BASE_URL ?>shop.php" class="action-icon" title="Search Skincare">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </a>

                <!-- Wishlist Icon -->
                <a href="<?= BASE_URL ?>wishlist.php" class="action-icon" title="My Wishlist">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span class="badge-count wishlist-count-badge"><?= $wishlistCount ?></span>
                </a>

                <!-- Cart Icon -->
                <a href="<?= BASE_URL ?>cart.php" class="action-icon" title="Shopping Cart">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span class="badge-count cart-count-badge"><?= $cartCount ?></span>
                </a>

                <!-- Customer Account Menu -->
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="action-icon" title="Account">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                    <div class="user-dropdown-menu">
                        <?php if (isLoggedIn()): ?>
                            <div style="padding: 8px 18px; border-bottom: 1px solid #ede4e0; font-size: 0.8rem; color: #726264;">
                                Signed in as<br><strong><?= e($_SESSION['user_name'] ?? 'Customer') ?></strong>
                            </div>
                            <a href="<?= BASE_URL ?>my-orders.php">My Orders</a>
                            <a href="<?= BASE_URL ?>profile.php">Account Profile</a>
                            <a href="<?= BASE_URL ?>wishlist.php">Saved Wishlist</a>
                            <?php if (isAdmin()): ?>
                                <a href="<?= BASE_URL ?>admin/dashboard.php" style="color: #b37d7a; font-weight: 600;">Admin Dashboard</a>
                            <?php endif; ?>
                            <div style="border-top: 1px solid #ede4e0; margin-top: 4px;"></div>
                            <a href="<?= BASE_URL ?>logout.php" style="color: #d32f2f;">Sign Out</a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>login.php">Sign In</a>
                            <a href="<?= BASE_URL ?>register.php">Create Account</a>
                            <div style="border-top: 1px solid #ede4e0; margin-top: 4px;"></div>
                            <a href="<?= BASE_URL ?>admin/index.php" style="font-size: 0.8rem; color: #8c7e80;">Admin Portal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
