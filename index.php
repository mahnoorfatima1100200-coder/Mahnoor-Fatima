<?php
/**
 * Skinora - Homepage
 * Brand: Skinora - Discover Your Natural Glow
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Luxury Skincare & Clean Beauty Formulations';
$metaDescription = 'Shop Skinora luxury skincare. Natural, dermatologist-tested serums, moisturizers, cleansers, and sunscreens crafted for your radiant glow in Pakistan.';

$db = getDBConnection();

// Fetch featured categories
$catStmt = $db->query("SELECT * FROM categories WHERE status = 'active' AND is_featured = 1 ORDER BY id ASC LIMIT 8");
$featuredCategories = $catStmt->fetchAll();

// Fetch best-selling products
$bestStmt = $db->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'active' AND p.is_best_seller = 1 
    ORDER BY p.id ASC LIMIT 4
");
$bestSellers = $bestStmt->fetchAll();

// Fetch new arrivals
$newStmt = $db->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'active' AND p.is_new_arrival = 1 
    ORDER BY p.id DESC LIMIT 4
");
$newArrivals = $newStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <span class="hero-subtitle">✨ Premium Clean Beauty</span>
                <h1 class="hero-title">Discover Your <span>Natural Glow</span> With Skinora.</h1>
                <p class="hero-desc">Elevate your daily ritual with dermatologically developed, high-potency botanical actives. Safe, clinically proven formulas designed specifically for radiant South Asian skin.</p>
                <div class="hero-btn-group">
                    <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary">Shop The Collection &rarr;</a>
                    <a href="<?= BASE_URL ?>about.php" class="btn btn-outline">Our Philosophy</a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="<?= BASE_URL ?>uploads/products/hero-skincare.svg" alt="Skinora Luxury Skincare Collection" class="hero-image">
                <div class="hero-badge-floating">
                    <div class="floating-icon">★</div>
                    <div>
                        <strong style="display:block; font-size:0.9rem;">4.9 / 5 Rating</strong>
                        <span style="font-size:0.75rem; color:#726264;">From 10,000+ Happy Customers</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Strips -->
<section class="trust-strip">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item">
                <div class="trust-icon">🚚</div>
                <div>
                    <h4>Free Shipping</h4>
                    <p>On all orders above <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></p>
                </div>
            </div>
            <div class="trust-item">
                <div class="trust-icon">🌿</div>
                <div>
                    <h4>100% Clean Actives</h4>
                    <p>Paraben, sulphate & cruelty free</p>
                </div>
            </div>
            <div class="trust-item">
                <div class="trust-icon">💵</div>
                <div>
                    <h4>Cash on Delivery</h4>
                    <p>Pay safely when you receive</p>
                </div>
            </div>
            <div class="trust-item">
                <div class="trust-icon">🔬</div>
                <div>
                    <h4>Dermatologist Backed</h4>
                    <p>Formulated for Pakistani climate</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Targeted Skincare</span>
            <h2 class="section-title">Shop By Category</h2>
            <p class="section-desc">From protective sunscreens to rejuvenating night treatments, find everything your skin needs to thrive.</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($featuredCategories as $cat): ?>
                <a href="<?= BASE_URL ?>category.php?slug=<?= e($cat['slug']) ?>" class="category-card">
                    <div class="category-card-icon">
                        <img src="<?= BASE_URL ?>uploads/categories/<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>" style="width: 50px; height: 50px;">
                    </div>
                    <h3><?= e($cat['name']) ?></h3>
                    <p><?= e($cat['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="section" style="background: #faf6f4;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Most Loved</span>
            <h2 class="section-title">Our Best Sellers</h2>
            <p class="section-desc">Cult favorites tested, loved, and restocked by skincare enthusiasts nationwide.</p>
        </div>

        <div class="products-grid">
            <?php foreach ($bestSellers as $prod): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <div class="product-badges">
                            <?php if (!empty($prod['sale_price'])): ?>
                                <span class="badge-tag badge-sale">Sale</span>
                            <?php endif; ?>
                            <span class="badge-tag" style="background:#b37d7a; color:#fff;">Best Seller</span>
                        </div>
                        <button type="button" class="product-wishlist-btn" data-product-id="<?= $prod['id'] ?>" title="Save to wishlist">♥</button>
                        <a href="<?= BASE_URL ?>product.php?slug=<?= e($prod['slug']) ?>">
                            <img src="<?= BASE_URL ?>uploads/products/<?= e($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <span class="product-category-link"><?= e($prod['category_name']) ?></span>
                        <h3 class="product-name">
                            <a href="<?= BASE_URL ?>product.php?slug=<?= e($prod['slug']) ?>"><?= e($prod['name']) ?></a>
                        </h3>
                        <div class="product-price-box">
                            <?php if (!empty($prod['sale_price'])): ?>
                                <span class="current-price"><?= formatPrice($prod['sale_price']) ?></span>
                                <span class="old-price"><?= formatPrice($prod['price']) ?></span>
                            <?php else: ?>
                                <span class="current-price"><?= formatPrice($prod['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-actions">
                            <button type="button" class="btn btn-primary btn-sm btn-block btn-add-to-cart" data-product-id="<?= $prod['id'] ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Discount Banner -->
<div class="container">
    <div class="promo-banner">
        <div class="promo-content">
            <span style="letter-spacing: 2px; text-transform: uppercase; font-size: 0.85rem; font-weight: 600;">Special Glow Offer</span>
            <h2>Get 10% Off Your Entire Skincare Order</h2>
            <p>Use coupon code <strong>GLOW10</strong> during checkout. Free nationwide shipping on orders over <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?>.</p>
            <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary">Claim My Discount</a>
        </div>
        <div>
            <img src="<?= BASE_URL ?>uploads/products/prod-vit-c-serum.svg" alt="Skinora Glow Deal" style="max-height: 240px; border-radius: 12px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));">
        </div>
    </div>
</div>

<!-- New Arrivals -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Fresh Formulations</span>
            <h2 class="section-title">New In Studio</h2>
            <p class="section-desc">The newest innovations in barrier repair, photoprotection, and active botanical serums.</p>
        </div>

        <div class="products-grid">
            <?php foreach ($newArrivals as $prod): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <div class="product-badges">
                            <span class="badge-tag badge-new">New</span>
                            <?php if (!empty($prod['sale_price'])): ?>
                                <span class="badge-tag badge-sale">Sale</span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="product-wishlist-btn" data-product-id="<?= $prod['id'] ?>" title="Save to wishlist">♥</button>
                        <a href="<?= BASE_URL ?>product.php?slug=<?= e($prod['slug']) ?>">
                            <img src="<?= BASE_URL ?>uploads/products/<?= e($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <span class="product-category-link"><?= e($prod['category_name']) ?></span>
                        <h3 class="product-name">
                            <a href="<?= BASE_URL ?>product.php?slug=<?= e($prod['slug']) ?>"><?= e($prod['name']) ?></a>
                        </h3>
                        <div class="product-price-box">
                            <?php if (!empty($prod['sale_price'])): ?>
                                <span class="current-price"><?= formatPrice($prod['sale_price']) ?></span>
                                <span class="old-price"><?= formatPrice($prod['price']) ?></span>
                            <?php else: ?>
                                <span class="current-price"><?= formatPrice($prod['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-actions">
                            <button type="button" class="btn btn-primary btn-sm btn-block btn-add-to-cart" data-product-id="<?= $prod['id'] ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Customer Reviews Testimonials -->
<section class="section" style="background: #faf6f4;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Real Results</span>
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-desc">Read genuine reviews from skincare lovers who transformed their complexion with Skinora.</p>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="star-rating" style="margin-bottom: 12px;">★★★★★</div>
                <p class="testimonial-quote">"The Vitamin C serum completely faded the stubborn acne marks on my cheeks. I've bought 3 bottles already and always get compliments on my glow!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">A</div>
                    <div class="author-info">
                        <h4>Ayesha K.</h4>
                        <span>Verified Buyer &bull; Lahore</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="star-rating" style="margin-bottom: 12px;">★★★★★</div>
                <p class="testimonial-quote">"The Invisible Shield SPF 50+ is hands down the best sunscreen in Pakistan. Absolutely ZERO white cast, absorbs in 10 seconds, and wears like silk under makeup."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">M</div>
                    <div class="author-info">
                        <h4>Mahnoor Ali</h4>
                        <span>Verified Buyer &bull; Karachi</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="star-rating" style="margin-bottom: 12px;">★★★★★</div>
                <p class="testimonial-quote">"My sensitive skin used to react to everything. Skinora's Barrier Relief Ceramide Moisturizer restored my barrier in a week. Gentle, non-greasy, and so soothing."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">Z</div>
                    <div class="author-info">
                        <h4>Zainab Fatima</h4>
                        <span>Verified Buyer &bull; Islamabad</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
