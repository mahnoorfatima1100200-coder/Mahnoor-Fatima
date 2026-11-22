<?php
/**
 * Skinora - Admin Analytics & Reports
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Store Sales & Inventory Reports';
$db = getDBConnection();

// Summary stats
$totalRevenue = (float)$db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'cancelled'")->fetchColumn();
$completedOrders = (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'delivered'")->fetchColumn();
$averageOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

// Top Selling Products
$topStmt = $db->query("
    SELECT p.name, p.sku, p.price, SUM(oi.quantity) AS total_sold, SUM(oi.subtotal) AS total_earned
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.order_status != 'cancelled'
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 5
");
$topSelling = $topStmt->fetchAll();

// Inventory Status breakdown
$invStmt = $db->query("
    SELECT p.name, p.sku, c.name AS category_name, p.stock_quantity, p.price,
           (p.stock_quantity * p.price) AS inventory_value
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.stock_quantity ASC
");
$inventoryList = $invStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-title">Gross Revenue (PKR)</div>
            <div class="metric-value"><?= formatPrice($totalRevenue) ?></div>
        </div>
        <div class="metric-icon-box icon-sales">💰</div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-title">Delivered Orders</div>
            <div class="metric-value"><?= $completedOrders ?></div>
        </div>
        <div class="metric-icon-box icon-orders">✓</div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-title">Average Order Value</div>
            <div class="metric-value"><?= formatPrice($averageOrderValue) ?></div>
        </div>
        <div class="metric-icon-box icon-customers">📊</div>
    </div>
</div>

<!-- Top Selling Products -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Top Selling Skincare Formulations</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Unit Price</th>
                <th>Units Sold</th>
                <th>Revenue Generated</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($topSelling)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 20px; color:#6c757d;">No sales data recorded yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($topSelling as $tp): ?>
                    <tr>
                        <td><strong><?= e($tp['name']) ?></strong></td>
                        <td><?= e($tp['sku']) ?></td>
                        <td><?= formatPrice($tp['price']) ?></td>
                        <td><strong><?= $tp['total_sold'] ?></strong></td>
                        <td><strong style="color: #2e7d32;"><?= formatPrice($tp['total_earned']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Full Inventory Valuation Report -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Inventory Health & Valuation Report</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price (PKR)</th>
                <th>Stock Quantity</th>
                <th>Stock Value (PKR)</th>
                <th>Health</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventoryList as $inv): ?>
                <tr>
                    <td><strong><?= e($inv['name']) ?></strong></td>
                    <td><?= e($inv['category_name']) ?></td>
                    <td><?= formatPrice($inv['price']) ?></td>
                    <td><?= $inv['stock_quantity'] ?></td>
                    <td><?= formatPrice($inv['inventory_value']) ?></td>
                    <td>
                        <?php if ($inv['stock_quantity'] <= 5): ?>
                            <span class="status-pill status-cancelled">Critical Low</span>
                        <?php elseif ($inv['stock_quantity'] <= 15): ?>
                            <span class="status-pill status-pending">Re-order Soon</span>
                        <?php else: ?>
                            <span class="status-pill status-active">Optimal</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
