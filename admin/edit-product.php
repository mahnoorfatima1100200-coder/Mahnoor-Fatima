<?php
/**
 * Skinora - Admin Edit Product
 */
require_once __DIR__ . '/includes/auth.php';

$productId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$db = getDBConnection();

$stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Product not found.');
    redirect('admin/products.php');
}

$adminPageTitle = 'Edit: ' . $product['name'];
$categories = $db->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfOrDie();

    $name = cleanInput($_POST['name'] ?? '');
    $categoryId = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $sku = cleanInput($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stockQuantity = (int)($_POST['stock_quantity'] ?? 0);
    $shortDesc = cleanInput($_POST['short_description'] ?? '');
    $fullDesc = cleanInput($_POST['full_description'] ?? '');
    $ingredients = cleanInput($_POST['ingredients'] ?? '');
    $howToUse = cleanInput($_POST['how_to_use'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;
    $isNewArrival = isset($_POST['is_new_arrival']) ? 1 : 0;
    $status = cleanInput($_POST['status'] ?? 'active');

    $mainImage = $product['main_image'];
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['main_image']['tmp_name'];
        $origName = $_FILES['main_image']['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];

        if (in_array($ext, $allowed)) {
            $newFileName = 'prod-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = UPLOAD_DIR . 'products' . DIRECTORY_SEPARATOR . $newFileName;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $mainImage = $newFileName;
            }
        }
    }

    $update = $db->prepare("
        UPDATE products SET 
            category_id = ?, name = ?, sku = ?, short_description = ?, full_description = ?,
            ingredients = ?, how_to_use = ?, price = ?, sale_price = ?, stock_quantity = ?,
            main_image = ?, is_featured = ?, is_best_seller = ?, is_new_arrival = ?, status = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $update->execute([
        $categoryId, $name, $sku, $shortDesc, $fullDesc,
        $ingredients, $howToUse, $price, $salePrice, $stockQuantity,
        $mainImage, $isFeatured, $isBestSeller, $isNewArrival, $status,
        $productId
    ]);

    setFlash('success', 'Product updated successfully!');
    redirect('admin/products.php');
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width: 900px; margin: 0 auto;">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Edit Skincare Formulation</h3>
        <a href="<?= BASE_URL ?>admin/products.php" class="btn-admin btn-admin-outline">&larr; Back to Catalog</a>
    </div>

    <form action="<?= BASE_URL ?>admin/edit-product.php?id=<?= $productId ?>" method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Product Name *</label>
                <input type="text" name="name" class="admin-input" value="<?= e($product['name']) ?>" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Category *</label>
                <select name="category_id" class="admin-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">SKU</label>
                <input type="text" name="sku" class="admin-input" value="<?= e($product['sku']) ?>">
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Stock Units *</label>
                <input type="number" name="stock_quantity" class="admin-input" value="<?= $product['stock_quantity'] ?>" min="0" required>
            </div>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Regular Price (PKR) *</label>
                <input type="number" step="0.01" name="price" class="admin-input" value="<?= $product['price'] ?>" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Sale Price (Optional)</label>
                <input type="number" step="0.01" name="sale_price" class="admin-input" value="<?= $product['sale_price'] ?? '' ?>">
            </div>
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Current Product Image</label>
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                <img src="<?= BASE_URL ?>uploads/products/<?= e($product['main_image']) ?>" alt="Product" style="width: 60px; height: 60px; border-radius: 8px; background: #fceee9; padding: 4px;">
                <input type="file" name="main_image" class="admin-input" accept="image/png, image/jpeg, image/webp, image/svg+xml">
            </div>
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Short Description</label>
            <input type="text" name="short_description" class="admin-input" value="<?= e($product['short_description']) ?>">
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Full Clinical Description</label>
            <textarea name="full_description" rows="4" class="admin-textarea"><?= e($product['full_description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Ingredients</label>
                <textarea name="ingredients" rows="3" class="admin-textarea"><?= e($product['ingredients']) ?></textarea>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">How To Use</label>
                <textarea name="how_to_use" rows="3" class="admin-textarea"><?= e($product['how_to_use']) ?></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Status</label>
                <select name="status" class="admin-select">
                    <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active (Visible on Store)</option>
                    <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                </select>
            </div>
        </div>

        <div style="background: #faf6f4; border: 1px solid #ede4e0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <strong style="display:block; margin-bottom: 10px; font-size: 0.9rem;">Storefront Display Badges:</strong>
            <label style="margin-right: 20px; font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>> Featured on Homepage
            </label>
            <label style="margin-right: 20px; font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_best_seller" value="1" <?= $product['is_best_seller'] ? 'checked' : '' ?>> Best Seller Badge
            </label>
            <label style="font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_new_arrival" value="1" <?= $product['is_new_arrival'] ? 'checked' : '' ?>> New Arrival Badge
            </label>
        </div>

        <button type="submit" class="btn-admin btn-admin-primary" style="padding: 12px 24px; font-size: 0.95rem;">
            Update Product Information
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
