<?php
/**
 * Skinora - Product Details Page
 * Image gallery, stock, ingredients, usage instructions, reviews & related products
 */
require_once __DIR__ . '/config/config.php';

$slug = cleanInput($_GET['slug'] ?? '');
if (empty($slug)) {
    redirect('shop.php');
}

$db = getDBConnection();

// Fetch product with category info
$stmt = $db->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.slug = ? AND p.status = 'active' 
    LIMIT 1
");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Product not found or currently unavailable.');
    redirect('shop.php');
}

$pageTitle = $product['name'];
$metaDescription = $product['short_description'];

// Fetch product gallery images
$galleryStmt = $db->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
$galleryStmt->execute([$product['id']]);
$galleryImages = $galleryStmt->fetchAll(PDO::FETCH_COLUMN);

// Ensure main image is included in gallery
if (empty($galleryImages)) {
    $galleryImages = [$product['main_image']];
} elseif (!in_array($product['main_image'], $galleryImages)) {
    array_unshift($galleryImages, $product['main_image']);
}

// Fetch approved reviews
$revStmt = $db->prepare("
    SELECT * FROM reviews 
    WHERE product_id = ? AND status = 'approved' 
    ORDER BY created_at DESC
");
$revStmt->execute([$product['id']]);
$reviews = $revStmt->fetchAll();

// Calculate average rating
$totalReviews = count($reviews);
$averageRating = 5.0;
if ($totalReviews > 0) {
    $sum = array_sum(array_column($reviews, 'rating'));
    $averageRating = round($sum / $totalReviews, 1);
}

// Fetch related products from same category
$relStmt = $db->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
    ORDER BY p.id DESC LIMIT 4
");
$relStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?= BASE_URL ?>shop.php">Shop</a>
        <span class="separator">/</span>
        <a href="<?= BASE_URL ?>category.php?slug=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a>
        <span class="separator">/</span>
        <span class="current"><?= e($product['name']) ?></span>
    </div>
</div>

<div class="container">
    <?= displayFlash() ?>

    <div class="product-details-grid">
        <!-- Gallery Column -->
        <div class="product-gallery">
            <div class="gallery-main-img">
                <img id="gallery-main-display" src="<?= BASE_URL ?>uploads/products/<?= e($galleryImages[0]) ?>" alt="<?= e($product['name']) ?>">
            </div>
            <?php if (count($galleryImages) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="thumb-item <?= $index === 0 ? 'active' : '' ?>" data-full-src="<?= BASE_URL ?>uploads/products/<?= e($img) ?>">
                            <img src="<?= BASE_URL ?>uploads/products/<?= e($img) ?>" alt="Product angle">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details Column -->
        <div class="product-info-col">
            <span class="product-category-link"><?= e($product['category_name']) ?></span>
            <h1><?= e($product['name']) ?></h1>

            <div class="product-meta-row">
                <?= renderRatingStars($averageRating, $totalReviews) ?>
                <span style="color: #c9bcbe;">|</span>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="stock-badge stock-in">In Stock (<?= $product['stock_quantity'] ?> available)</span>
                <?php else: ?>
                    <span class="stock-badge stock-out">Out of Stock</span>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <span style="font-size: 0.8rem; color: #726264;">SKU: <?= e($product['sku']) ?></span>
                <?php endif; ?>
            </div>

            <div class="product-single-price">
                <?php if (!empty($product['sale_price'])): ?>
                    <span style="color: #b37d7a;"><?= formatPrice($product['sale_price']) ?></span>
                    <span class="old-price"><?= formatPrice($product['price']) ?></span>
                <?php else: ?>
                    <span><?= formatPrice($product['price']) ?></span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.98rem; line-height: 1.8; margin-bottom: 24px;">
                <?= e($product['short_description']) ?>
            </p>

            <!-- Quantity & Add to Cart -->
            <?php if ($product['stock_quantity'] > 0): ?>
                <div class="product-qty-row">
                    <div class="qty-control">
                        <button type="button" class="qty-btn qty-minus">&minus;</button>
                        <input type="number" id="single-product-qty" class="qty-input" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                        <button type="button" class="qty-btn qty-plus">&plus;</button>
                    </div>

                    <button type="button" class="btn btn-primary btn-add-to-cart" data-product-id="<?= $product['id'] ?>" style="flex-grow: 1;">
                        Add to Shopping Cart
                    </button>

                    <button type="button" class="product-wishlist-btn" data-product-id="<?= $product['id'] ?>" title="Save to wishlist" style="position: static; width: 48px; height: 48px; border: 1px solid #ede4e0;">
                        ♥
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-error">This product is currently out of stock. Please check back soon!</div>
            <?php endif; ?>

            <div style="background: #faf6f4; border: 1px solid #ede4e0; border-radius: 8px; padding: 16px; font-size: 0.86rem; line-height: 1.7;">
                <div>✨ <strong>Free nationwide delivery</strong> on orders over <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></div>
                <div>🌿 <strong>Clean Formula:</strong> Dermatologist tested, 100% cruelty-free</div>
                <div>💵 <strong>Payment:</strong> Cash on Delivery (COD) available at checkout</div>
            </div>
        </div>
    </div>

    <!-- Product Tabs: Full Description, Ingredients, How to Use, Reviews -->
    <div class="product-tabs-section">
        <div class="tabs-nav">
            <button type="button" class="tab-btn active" data-tab="tab-desc">Description</button>
            <button type="button" class="tab-btn" data-tab="tab-ingredients">Ingredients</button>
            <button type="button" class="tab-btn" data-tab="tab-usage">How To Use</button>
            <button type="button" class="tab-btn" data-tab="tab-reviews">Customer Reviews (<?= $totalReviews ?>)</button>
        </div>

        <div id="tab-desc" class="tab-pane active">
            <div style="max-width: 800px; color: #443739;">
                <p><?= nl2br(e($product['full_description'])) ?></p>
            </div>
        </div>

        <div id="tab-ingredients" class="tab-pane">
            <div style="max-width: 800px; color: #443739;">
                <h4 style="margin-bottom: 8px;">Key Actives & Botanical Extracts</h4>
                <p><?= nl2br(e($product['ingredients'])) ?></p>
            </div>
        </div>

        <div id="tab-usage" class="tab-pane">
            <div style="max-width: 800px; color: #443739;">
                <h4 style="margin-bottom: 8px;">Recommended Application Ritual</h4>
                <p><?= nl2br(e($product['how_to_use'])) ?></p>
            </div>
        </div>

        <div id="tab-reviews" class="tab-pane">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <!-- Review List -->
                <div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 20px;">Customer Feedback</h3>
                    <?php if (empty($reviews)): ?>
                        <p>No customer reviews yet. Be the first to review this formula!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div style="border-bottom: 1px solid #ede4e0; padding-bottom: 16px; margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <strong><?= e($rev['reviewer_name']) ?></strong>
                                    <span style="font-size: 0.76rem; color: #726264;"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <div class="star-rating" style="margin-bottom: 6px;">
                                    <?= renderRatingStars($rev['rating']) ?>
                                </div>
                                <p style="font-size: 0.92rem; margin: 0;"><?= e($rev['review_text']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Review Form -->
                <div style="background: #ffffff; border: 1px solid #ede4e0; border-radius: 12px; padding: 26px;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 16px;">Write a Review</h3>
                    <form action="<?= BASE_URL ?>actions/submit-review.php" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div class="form-group">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="reviewer_name" class="form-control" value="<?= e($_SESSION['user_name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Your Rating *</label>
                            <select name="rating" class="form-control" required>
                                <option value="5">★★★★★ - Excellent (5 Stars)</option>
                                <option value="4">★★★★☆ - Very Good (4 Stars)</option>
                                <option value="3">★★★☆☆ - Average (3 Stars)</option>
                                <option value="2">★★☆☆☆ - Poor (2 Stars)</option>
                                <option value="1">★☆☆☆☆ - Terrible (1 Star)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Your Review *</label>
                            <textarea name="review_text" rows="4" class="form-control" placeholder="Share your experience with this formulation..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="section" style="border-top: 1px solid #ede4e0; margin-top: 50px;">
            <div class="section-header">
                <span class="section-subtitle">Complete Your Ritual</span>
                <h2 class="section-title">You May Also Love</h2>
            </div>

            <div class="products-grid">
                <?php foreach ($relatedProducts as $prod): ?>
                    <div class="product-card">
                        <div class="product-thumb">
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
        </section>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
