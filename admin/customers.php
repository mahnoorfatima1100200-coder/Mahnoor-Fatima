<?php
/**
 * Skinora - Admin Customers Management
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Registered Customers';
$db = getDBConnection();

$stmt = $db->query("
    SELECT u.*, 
           COUNT(o.id) AS total_orders, 
           COALESCE(SUM(o.total_amount), 0) AS total_spent
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    WHERE u.role = 'customer' 
    GROUP BY u.id 
    ORDER BY u.id DESC
");
$customers = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title">Registered Customer Accounts (<?= count($customers) ?>)</h3>
            <p style="font-size: 0.84rem; color: #6c757d; margin: 0;">View customer contact details, lifetime purchases, and account status.</p>
        </div>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Registered</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #6c757d;">No registered customers yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td>
                            <strong><?= e($c['full_name']) ?></strong><br>
                            <span style="font-size: 0.75rem; color: #6c757d;"><?= e($c['email']) ?></span>
                        </td>
                        <td><?= e($c['phone'] ?? '—') ?></td>
                        <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                        <td><strong><?= $c['total_orders'] ?></strong></td>
                        <td><strong><?= formatPrice($c['total_spent']) ?></strong></td>
                        <td>
                            <span class="status-pill status-<?= e($c['status']) ?>">
                                <?= ucfirst(e($c['status'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
