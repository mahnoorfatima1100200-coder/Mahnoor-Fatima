<?php
/**
 * Skinora - Customer Wishlist Page
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

$pageTitle = 'My Saved Skincare Wishlist';
$db = getDBConnection();
$userId = (int)$_SESSION['user_id'];

// Fetch wishlist items
$stmt = $db->prepare("
    SELECT w.id AS wishlist_id, p.*, c.name AS category_name 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE w.user_id = ? 
    ORDER BY w.id DESC
");
$stmt->execute([$userId]);
$wishlistItems = $stmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">My Wishlist</span>
    </div>
</div>

<div class="container" style="padding: 40px 20px;">
    <?= displayFlash() ?>

    <h1 style="font-size: 2.2rem; margin-bottom: 24px;">Saved Skincare (<?= count($wishlistItems) ?>)</h1>

    <?php if (empty($wishlistItems)): ?>
        <div style="text-align: center; padding: 70px 20px; background: #fff; border-radius: 12px; border: 1px solid #ede4e0; max-width: 600px; margin: 0 auto;">
            <div style="font-size: 3.5rem; margin-bottom: 12px;">♥</div>
            <h2>Your Wishlist is Empty</h2>
            <p>Save your favorite formulas here to easily find and purchase them later.</p>
            <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary" style="margin-top: 14px;">Explore The Shop</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($wishlistItems as $prod): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <form action="<?= BASE_URL ?>actions/remove-wishlist.php" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                            <button type="submit" class="product-wishlist-btn active" title="Remove from wishlist">&times;</button>
                        </form>
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
                                Move to Bag
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
