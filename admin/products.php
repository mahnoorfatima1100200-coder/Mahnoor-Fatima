<?php
/**
 * Skinora - Admin Products Management
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Product Catalog Management';
$db = getDBConnection();

$stmt = $db->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title">All Skincare Products (<?= count($products) ?>)</h3>
            <p style="font-size: 0.84rem; color: #6c757d; margin: 0;">Add, update, adjust prices, stock levels, and promotional badges.</p>
        </div>
        <a href="<?= BASE_URL ?>admin/add-product.php" class="btn-admin btn-admin-primary">
            + Add New Product
        </a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name & SKU</th>
                <th>Category</th>
                <th>Price (PKR)</th>
                <th>Sale Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td style="width: 60px;">
                        <img src="<?= BASE_URL ?>uploads/products/<?= e($p['main_image']) ?>" alt="Thumbnail" style="width: 44px; height: 44px; border-radius: 6px; background: #fceee9; object-fit: contain; padding: 2px;">
                    </td>
                    <td>
                        <strong><?= e($p['name']) ?></strong><br>
                        <span style="font-size: 0.75rem; color: #6c757d;">SKU: <?= e($p['sku']) ?></span>
                    </td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= formatPrice($p['price']) ?></td>
                    <td>
                        <?= !empty($p['sale_price']) ? '<span style="color:#b37d7a; font-weight:600;">' . formatPrice($p['sale_price']) . '</span>' : '—' ?>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: <?= $p['stock_quantity'] < 10 ? '#d32f2f' : '#2e7d32' ?>;">
                            <?= $p['stock_quantity'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-pill status-<?= e($p['status']) ?>">
                            <?= ucfirst(e($p['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="<?= BASE_URL ?>admin/edit-product.php?id=<?= $p['id'] ?>" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.76rem;">
                                Edit
                            </a>
                            <a href="<?= BASE_URL ?>admin/delete-product.php?id=<?= $p['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" 
                               class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.76rem;"
                               onclick="return confirm('Are you sure you want to permanently delete this product?');">
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
