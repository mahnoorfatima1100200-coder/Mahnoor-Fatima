<?php
/**
 * Skinora - Shop Catalog Page
 * Search, Category Filtering, Price Filtering, Sorting & Pagination
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Shop Skincare & Beauty Formulations';
$metaDescription = 'Explore Skinora’s complete collection of cleansers, toners, serums, moisturizers, and sunscreens with nationwide COD delivery across Pakistan.';

$db = getDBConnection();

// Filters & Sorting parameters
$search = cleanInput($_GET['search'] ?? '');
$categorySlug = cleanInput($_GET['category'] ?? '');
$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;
$sort = cleanInput($_GET['sort'] ?? 'newest');
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Build query
$where = ["p.status = 'active'"];
$params = [];

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.short_description LIKE ? OR p.ingredients LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($categorySlug)) {
    $where[] = "c.slug = ?";
    $params[] = $categorySlug;
}

if ($minPrice > 0) {
    $where[] = "COALESCE(p.sale_price, p.price) >= ?";
    $params[] = $minPrice;
}

if ($maxPrice < 10000) {
    $where[] = "COALESCE(p.sale_price, p.price) <= ?";
    $params[] = $maxPrice;
}

$whereClause = implode(' AND ', $where);

// Sorting
$orderBy = "p.id DESC";
if ($sort === 'price-asc') {
    $orderBy = "COALESCE(p.sale_price, p.price) ASC";
} elseif ($sort === 'price-desc') {
    $orderBy = "COALESCE(p.sale_price, p.price) DESC";
} elseif ($sort === 'popular') {
    $orderBy = "p.is_best_seller DESC, p.id DESC";
}

// Count total matching products
$countSql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE {$whereClause}";
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Fetch paginated products
$sql = "
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE {$whereClause} 
    ORDER BY {$orderBy} 
    LIMIT {$limit} OFFSET {$offset}
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch all categories for sidebar filter
$catListStmt = $db->query("
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active' 
    WHERE c.status = 'active' 
    GROUP BY c.id 
    ORDER BY c.name ASC
");
$allCategories = $catListStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Shop All Skincare</span>
    </div>
</div>

<div class="container">
    <div class="shop-layout">
        <!-- Sidebar Filters -->
        <aside class="shop-sidebar">
            <!-- Search Widget -->
            <div class="sidebar-widget">
                <h4 class="widget-title">Search Skincare</h4>
                <form action="<?= BASE_URL ?>shop.php" method="GET">
                    <?php if ($categorySlug): ?>
                        <input type="hidden" name="category" value="<?= e($categorySlug) ?>">
                    <?php endif; ?>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, active..." value="<?= e($search) ?>" style="margin-bottom: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm btn-block">Search</button>
                </form>
            </div>

            <!-- Categories Filter -->
            <div class="sidebar-widget">
                <h4 class="widget-title">Categories</h4>
                <ul class="category-filter-list">
                    <li>
                        <a href="<?= BASE_URL ?>shop.php" class="<?= empty($categorySlug) ? 'active' : '' ?>">
                            <span>All Products</span>
                            <span>(<?= $totalProducts ?>)</span>
                        </a>
                    </li>
                    <?php foreach ($allCategories as $cat): ?>
                        <li>
                            <a href="<?= BASE_URL ?>shop.php?category=<?= e($cat['slug']) ?>" class="<?= $categorySlug === $cat['slug'] ? 'active' : '' ?>">
                                <span><?= e($cat['name']) ?></span>
                                <span>(<?= $cat['product_count'] ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Price Filter -->
            <div class="sidebar-widget">
                <h4 class="widget-title">Price Range</h4>
                <form action="<?= BASE_URL ?>shop.php" method="GET">
                    <?php if ($categorySlug): ?>
                        <input type="hidden" name="category" value="<?= e($categorySlug) ?>">
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <input type="hidden" name="search" value="<?= e($search) ?>">
                    <?php endif; ?>
                    <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                        <input type="number" name="min_price" class="form-control" placeholder="Min Rs." value="<?= $minPrice ?: '' ?>">
                        <input type="number" name="max_price" class="form-control" placeholder="Max Rs." value="<?= $maxPrice < 10000 ? $maxPrice : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm btn-block">Apply Filter</button>
                </form>
            </div>
        </aside>

        <!-- Product Listing Grid -->
        <main class="shop-main">
            <!-- Top bar -->
            <div class="shop-top-bar">
                <div>
                    Showing <strong><?= count($products) ?></strong> of <strong><?= $totalProducts ?></strong> items
                    <?php if ($search): ?>
                        for "<em><?= e($search) ?></em>"
                    <?php endif; ?>
                </div>
                <div>
                    <form method="GET" action="<?= BASE_URL ?>shop.php" id="sort-form">
                        <?php if ($categorySlug): ?><input type="hidden" name="category" value="<?= e($categorySlug) ?>"><?php endif; ?>
                        <?php if ($search): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
                        <label for="sort-select" style="font-size: 0.88rem; margin-right: 6px;">Sort By:</label>
                        <select name="sort" id="sort-select" class="sort-select" onchange="this.form.submit()">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                            <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                            <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #ede4e0;">
                    <div style="font-size: 3rem; margin-bottom: 12px;">🌿</div>
                    <h3>No products found</h3>
                    <p>Try adjusting your search keywords or price filters.</p>
                    <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary" style="margin-top: 10px;">Clear All Filters</a>
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= BASE_URL ?>shop.php?page=<?= $i ?><?= $categorySlug ? '&category=' . urlencode($categorySlug) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>&sort=<?= urlencode($sort) ?>" 
                               class="btn btn-sm <?= $page === $i ? 'btn-primary' : 'btn-outline' ?>" style="min-width: 40px;">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
