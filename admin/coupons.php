<?php
/**
 * Skinora - Admin Coupons Management
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Discount Coupons';
$db = getDBConnection();

// Add Coupon POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    validateCsrfOrDie();

    $code = strtoupper(cleanInput($_POST['code'] ?? ''));
    $type = cleanInput($_POST['discount_type'] ?? 'percent');
    $value = (float)($_POST['discount_value'] ?? 0);
    $minSpend = (float)($_POST['min_spend'] ?? 0);
    $expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

    if (!empty($code) && $value > 0) {
        $ins = $db->prepare("
            INSERT INTO coupons (code, discount_type, discount_value, min_spend, expiry_date, usage_limit, usage_count, status, created_at)
            VALUES (?, ?, ?, ?, ?, 1000, 0, 'active', NOW())
        ");
        $ins->execute([$code, $type, $value, $minSpend, $expiryDate]);
        setFlash('success', "Coupon '{$code}' created successfully!");
        redirect('admin/coupons.php');
    }
}

// Delete Coupon
if (isset($_GET['delete'])) {
    validateCsrfOrDie();
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM coupons WHERE id = ?")->execute([$id]);
    setFlash('info', 'Coupon removed.');
    redirect('admin/coupons.php');
}

$coupons = $db->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 24px;">
    <!-- Add Coupon Form -->
    <div class="admin-card">
        <h3 class="admin-card-title" style="margin-bottom: 16px;">Create Coupon</h3>
        <form action="<?= BASE_URL ?>admin/coupons.php" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="add_coupon" value="1">

            <div class="admin-form-group">
                <label class="admin-label">Coupon Code *</label>
                <input type="text" name="code" class="admin-input" placeholder="e.g. GLOW30" style="text-transform: uppercase;" required>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-label">Type *</label>
                    <select name="discount_type" class="admin-select">
                        <option value="percent">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (PKR)</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Value *</label>
                    <input type="number" step="0.01" name="discount_value" class="admin-input" placeholder="e.g. 10 or 300" required>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-label">Minimum Order Spend (PKR)</label>
                <input type="number" step="0.01" name="min_spend" class="admin-input" value="1500">
            </div>

            <div class="admin-form-group">
                <label class="admin-label">Expiry Date (Optional)</label>
                <input type="date" name="expiry_date" class="admin-input" value="<?= date('Y-12-31') ?>">
            </div>

            <button type="submit" class="btn-admin btn-admin-primary btn-block">Publish Coupon</button>
        </form>
    </div>

    <!-- Coupons List -->
    <div class="admin-card">
        <h3 class="admin-card-title" style="margin-bottom: 16px;">Active Promo Codes (<?= count($coupons) ?>)</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Min Spend</th>
                    <th>Used</th>
                    <th>Expires</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $c): ?>
                    <tr>
                        <td><strong><?= e($c['code']) ?></strong></td>
                        <td>
                            <?= $c['discount_type'] === 'percent' ? $c['discount_value'] . '%' : formatPrice($c['discount_value']) ?>
                        </td>
                        <td><?= formatPrice($c['min_spend']) ?></td>
                        <td><?= $c['usage_count'] ?> times</td>
                        <td><?= $c['expiry_date'] ?? 'Never' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/coupons.php?delete=<?= $c['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.76rem;" onclick="return confirm('Delete coupon?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
