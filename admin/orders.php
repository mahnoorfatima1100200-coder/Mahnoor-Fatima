<?php
/**
 * Skinora - Admin Orders Management
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Customer Orders';
$db = getDBConnection();

$statusFilter = cleanInput($_GET['status'] ?? '');
$params = [];
$whereSql = "";

if (!empty($statusFilter)) {
    $whereSql = "WHERE order_status = ?";
    $params[] = $statusFilter;
}

$stmt = $db->prepare("
    SELECT o.*, COUNT(oi.id) AS item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    {$whereSql} 
    GROUP BY o.id 
    ORDER BY o.id DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Counts for filter pills
$counts = [
    'all' => (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn(),
    'processing' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'processing'")->fetchColumn(),
    'shipped' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'shipped'")->fetchColumn(),
    'delivered' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'delivered'")->fetchColumn(),
    'cancelled' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'cancelled'")->fetchColumn(),
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title">Manage Customer Orders</h3>
            <p style="font-size: 0.84rem; color: #6c757d; margin: 0;">Track orders, update shipping progress, and review invoices.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>admin/orders.php" class="btn-admin <?= empty($statusFilter) ? 'btn-admin-primary' : 'btn-admin-outline' ?>">
            All Orders (<?= $counts['all'] ?>)
        </a>
        <a href="<?= BASE_URL ?>admin/orders.php?status=pending" class="btn-admin <?= $statusFilter === 'pending' ? 'btn-admin-primary' : 'btn-admin-outline' ?>">
            Pending (<?= $counts['pending'] ?>)
        </a>
        <a href="<?= BASE_URL ?>admin/orders.php?status=processing" class="btn-admin <?= $statusFilter === 'processing' ? 'btn-admin-primary' : 'btn-admin-outline' ?>">
            Processing (<?= $counts['processing'] ?>)
        </a>
        <a href="<?= BASE_URL ?>admin/orders.php?status=shipped" class="btn-admin <?= $statusFilter === 'shipped' ? 'btn-admin-primary' : 'btn-admin-outline' ?>">
            Shipped (<?= $counts['shipped'] ?>)
        </a>
        <a href="<?= BASE_URL ?>admin/orders.php?status=delivered" class="btn-admin <?= $statusFilter === 'delivered' ? 'btn-admin-primary' : 'btn-admin-outline' ?>">
            Delivered (<?= $counts['delivered'] ?>)
        </a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer & City</th>
                <th>Items</th>
                <th>Amount (PKR)</th>
                <th>Order Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #6c757d;">No orders found matching this filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>
                            <strong><?= e($o['order_number']) ?></strong><br>
                            <span style="font-size: 0.75rem; color: #6c757d;">COD</span>
                        </td>
                        <td style="font-size: 0.82rem;">
                            <?= date('M d, Y h:i A', strtotime($o['created_at'])) ?>
                        </td>
                        <td>
                            <strong><?= e($o['customer_name']) ?></strong><br>
                            <span style="font-size: 0.78rem; color: #6c757d;"><?= e($o['city']) ?> &bull; <?= e($o['customer_phone']) ?></span>
                        </td>
                        <td><?= $o['item_count'] ?> item(s)</td>
                        <td><strong><?= formatPrice($o['total_amount']) ?></strong></td>
                        <td>
                            <span class="status-pill status-<?= e($o['order_status']) ?>">
                                <?= ucfirst(e($o['order_status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/order-details.php?id=<?= $o['id'] ?>" class="btn-admin btn-admin-primary" style="padding: 4px 10px; font-size: 0.78rem;">
                                Manage &rarr;
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
