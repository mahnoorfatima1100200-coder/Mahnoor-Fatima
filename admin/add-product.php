<?php
/**
 * Skinora - Admin Add New Product
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Add New Skincare Product';
$db = getDBConnection();

// Fetch categories for dropdown
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

    if (empty($name) || $price <= 0) {
        setFlash('error', 'Product name and valid price are required.');
    } else {
        $slug = slugify($name);
        
        // Handle image upload or fallback
        $mainImage = 'prod-vit-c-serum.svg';
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

        $stmt = $db->prepare("
            INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description,
                ingredients, how_to_use, price, sale_price, stock_quantity, main_image,
                is_featured, is_best_seller, is_new_arrival, status, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, NOW(), NOW()
            )
        ");
        $stmt->execute([
            $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
            $ingredients, $howToUse, $price, $salePrice, $stockQuantity, $mainImage,
            $isFeatured, $isBestSeller, $isNewArrival, $status
        ]);

        $newProdId = (int)$db->lastInsertId();
        // Add entry to product_images
        $db->prepare("INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, 1)")
           ->execute([$newProdId, $mainImage]);

        setFlash('success', "Product '{$name}' created successfully!");
        redirect('admin/products.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width: 900px; margin: 0 auto;">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Add New Skincare Product</h3>
        <a href="<?= BASE_URL ?>admin/products.php" class="btn-admin btn-admin-outline">&larr; Back to Catalog</a>
    </div>

    <form action="<?= BASE_URL ?>admin/add-product.php" method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Product Name *</label>
                <input type="text" name="name" class="admin-input" placeholder="e.g. Niacinamide 10% Clarifying Booster" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Category *</label>
                <select name="category_id" class="admin-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">SKU (Stock Keeping Unit)</label>
                <input type="text" name="sku" class="admin-input" placeholder="e.g. SKN-SRM-008">
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Stock Units Available *</label>
                <input type="number" name="stock_quantity" class="admin-input" value="50" min="0" required>
            </div>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Regular Price (PKR) *</label>
                <input type="number" step="0.01" name="price" class="admin-input" placeholder="2200" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">Sale / Discount Price (Optional)</label>
                <input type="number" step="0.01" name="sale_price" class="admin-input" placeholder="1999">
            </div>
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Primary Product Image</label>
            <input type="file" name="main_image" class="admin-input" accept="image/png, image/jpeg, image/webp, image/svg+xml">
            <small style="color: #6c757d;">Accepts JPG, PNG, WEBP, or SVG vectors.</small>
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Short Tagline Description</label>
            <input type="text" name="short_description" class="admin-input" placeholder="Brief 1-line benefit for product cards...">
        </div>

        <div class="admin-form-group">
            <label class="admin-label">Full Product Story & Clinical Benefits</label>
            <textarea name="full_description" rows="4" class="admin-textarea" placeholder="Detailed description of what this formulation does..."></textarea>
        </div>

        <div class="form-row">
            <div class="admin-form-group">
                <label class="admin-label">Key Ingredients</label>
                <textarea name="ingredients" rows="3" class="admin-textarea" placeholder="e.g. Aqua, Niacinamide, Zinc PCA..."></textarea>
            </div>
            <div class="admin-form-group">
                <label class="admin-label">How To Use Instructions</label>
                <textarea name="how_to_use" rows="3" class="admin-textarea" placeholder="e.g. Apply 2-3 drops to damp skin morning and night..."></textarea>
            </div>
        </div>

        <div style="background: #faf6f4; border: 1px solid #ede4e0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <strong style="display:block; margin-bottom: 10px; font-size: 0.9rem;">Storefront Display Badges:</strong>
            <label style="margin-right: 20px; font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1"> Featured on Homepage
            </label>
            <label style="margin-right: 20px; font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_best_seller" value="1"> Best Seller Badge
            </label>
            <label style="font-size: 0.88rem; cursor: pointer;">
                <input type="checkbox" name="is_new_arrival" value="1" checked> New Arrival Badge
            </label>
        </div>

        <button type="submit" class="btn-admin btn-admin-primary" style="padding: 12px 24px; font-size: 0.95rem;">
            Save & Publish Skincare Product
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
