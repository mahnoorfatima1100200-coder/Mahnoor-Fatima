<?php
/**
 * Skinora - Admin Dashboard
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Dashboard Overview';
$db = getDBConnection();

// Fetch KPI metrics
$totalSales = (float)$db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'cancelled'")->fetchColumn();
$totalOrders = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCustomers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalProducts = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();

$pendingOrdersCount = (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();

// Low stock products (< 10 units)
$lowStockStmt = $db->query("SELECT id, name, sku, stock_quantity, price FROM products WHERE stock_quantity <= 15 ORDER BY stock_quantity ASC LIMIT 5");
$lowStockProducts = $lowStockStmt->fetchAll();

// Recent 5 Orders
$recentOrdersStmt = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
$recentOrders = $recentOrdersStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- KPI Metric Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-title">Total Revenue</div>
            <div class="metric-value"><?= formatPrice($totalSales) ?></div>
        </div>
        <div class="metric-icon-box icon-sales">₨</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-title">Total Orders</div>
            <div class="metric-value"><?= $totalOrders ?></div>
            <small style="color: #ed6c02; font-size: 0.75rem;"><?= $pendingOrdersCount ?> Pending</small>
        </div>
        <div class="metric-icon-box icon-orders">📦</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-title">Registered Customers</div>
            <div class="metric-value"><?= $totalCustomers ?></div>
        </div>
        <div class="metric-icon-box icon-customers">👥</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-title">Total Skincare Formulations</div>
            <div class="metric-value"><?= $totalProducts ?></div>
        </div>
        <div class="metric-icon-box icon-products">✨</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px;">
    <!-- Recent Orders -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Recent Customer Orders</h3>
            <a href="<?= BASE_URL ?>admin/orders.php" class="btn-admin btn-admin-outline">View All &rarr;</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <p style="color: #6c757d; font-size: 0.9rem;">No orders placed yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ord): ?>
                        <tr>
                            <td><strong><?= e($ord['order_number']) ?></strong></td>
                            <td><?= e($ord['customer_name']) ?></td>
                            <td><?= formatPrice($ord['total_amount']) ?></td>
                            <td>
                                <span class="status-pill status-<?= e($ord['order_status']) ?>">
                                    <?= ucfirst(e($ord['order_status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/order-details.php?id=<?= $ord['id'] ?>" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.76rem;">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Low Stock Inventory Alerts -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">⚠️ Inventory Alert (Low Stock)</h3>
            <a href="<?= BASE_URL ?>admin/products.php" class="btn-admin btn-admin-outline">Products &rarr;</a>
        </div>

        <?php if (empty($lowStockProducts)): ?>
            <p style="color: #2e7d32; font-size: 0.9rem;">✓ All product inventory levels are healthy!</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockProducts as $low): ?>
                        <tr>
                            <td>
                                <strong><?= e($low['name']) ?></strong><br>
                                <span style="font-size: 0.75rem; color: #6c757d;">SKU: <?= e($low['sku']) ?></span>
                            </td>
                            <td>
                                <span style="color: <?= $low['stock_quantity'] <= 5 ? '#d32f2f' : '#ed6c02' ?>; font-weight: 700;">
                                    <?= $low['stock_quantity'] ?> left
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/edit-product.php?id=<?= $low['id'] ?>" class="btn-admin btn-admin-primary" style="padding: 4px 8px; font-size: 0.76rem;">
                                    Restock
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
