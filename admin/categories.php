<?php
/**
 * Skinora - Admin Categories Management
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Product Categories';
$db = getDBConnection();

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    validateCsrfOrDie();

    $name = cleanInput($_POST['name'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    if (!empty($name)) {
        $slug = slugify($name);
        $image = 'cat-serums.svg';

        $stmt = $db->prepare("INSERT INTO categories (name, slug, description, image, is_featured, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
        $stmt->execute([$name, $slug, $description, $image, $isFeatured]);

        setFlash('success', "Category '{$name}' created successfully!");
        redirect('admin/categories.php');
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    validateCsrfOrDie();
    $catId = (int)$_GET['delete'];
    $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$catId]);
    setFlash('info', 'Category deleted.');
    redirect('admin/categories.php');
}

// Fetch all categories with product counts
$categories = $db->query("
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.id ASC
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px;">
    <!-- Add Category Form -->
    <div class="admin-card">
        <h3 class="admin-card-title" style="margin-bottom: 16px;">Add New Category</h3>
        <form action="<?= BASE_URL ?>admin/categories.php" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="add_category" value="1">

            <div class="admin-form-group">
                <label class="admin-label">Category Name *</label>
                <input type="text" name="name" class="admin-input" placeholder="e.g. Eye Creams" required>
            </div>

            <div class="admin-form-group">
                <label class="admin-label">Short Description</label>
                <textarea name="description" rows="3" class="admin-textarea" placeholder="Brief summary of this product group..."></textarea>
            </div>

            <div class="admin-form-group">
                <label style="font-size: 0.88rem; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" checked> Display on Homepage
                </label>
            </div>

            <button type="submit" class="btn-admin btn-admin-primary btn-block">Create Category</button>
        </form>
    </div>

    <!-- Category Listing Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Existing Categories (<?= count($categories) ?>)</h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <strong><?= e($cat['name']) ?></strong><br>
                            <span style="font-size: 0.75rem; color: #6c757d;">Slug: <?= e($cat['slug']) ?></span>
                        </td>
                        <td><?= $cat['product_count'] ?> products</td>
                        <td><?= $cat['is_featured'] ? '✓ Yes' : '—' ?></td>
                        <td>
                            <span class="status-pill status-<?= e($cat['status']) ?>"><?= ucfirst(e($cat['status'])) ?></span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/categories.php?delete=<?= $cat['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" 
                               class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.76rem;"
                               onclick="return confirm('Delete this category? Associated products will have their category unassigned.');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
