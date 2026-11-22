<?php
/**
 * Skinora - Category Landing Page
 */
require_once __DIR__ . '/config/config.php';

$slug = cleanInput($_GET['slug'] ?? '');
if (empty($slug)) {
    redirect('shop.php');
}

$db = getDBConnection();

$catStmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active' LIMIT 1");
$catStmt->execute([$slug]);
$category = $catStmt->fetch();

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect('shop.php');
}

$pageTitle = $category['name'] . ' - Skincare Collection';
$metaDescription = 'Shop our curated collection of ' . $category['name'] . '. ' . $category['description'];

// Fetch category products
$prodStmt = $db->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE c.id = ? AND p.status = 'active' 
    ORDER BY p.id DESC
");
$prodStmt->execute([$category['id']]);
$products = $prodStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?= BASE_URL ?>shop.php">Shop</a>
        <span class="separator">/</span>
        <span class="current"><?= e($category['name']) ?></span>
    </div>
</div>

<!-- Category Banner Header -->
<section style="background: linear-gradient(135deg, #fff7f5 0%, #f7ebe8 100%); padding: 50px 0; border-bottom: 1px solid #ede4e0; text-align: center;">
    <div class="container">
        <span class="section-subtitle">Skinora Collection</span>
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;"><?= e($category['name']) ?></h1>
        <p style="max-width: 600px; margin: 0 auto;"><?= e($category['description']) ?></p>
    </div>
</section>

<!-- Category Products Grid -->
<div class="container" style="padding: 50px 20px;">
    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #ede4e0;">
            <p>No products currently available in this category.</p>
            <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary">Browse All Skincare</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $prod): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <div class="product-badges">
                            <?php if (!empty($prod['sale_price'])): ?>
                                <span class="badge-tag badge-sale">Sale</span>
                            <?php endif; ?>
                            <?php if ($prod['is_best_seller']): ?>
                                <span class="badge-tag" style="background:#b37d7a; color:#fff;">Hot</span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="product-wishlist-btn" data-product-id="<?= $prod['id'] ?>" title="Save to wishlist">♥</button>
                        <a href="<?= BASE_URL ?>product.php?slug=<?= e($prod['slug']) ?>">
                            <img src="<?= BASE_URL ?>uploads/products/<?= e($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <span class="product-category-link"><?= e($category['name']) ?></span>
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
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
