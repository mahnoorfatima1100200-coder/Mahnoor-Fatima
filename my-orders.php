<?php
/**
 * Skinora - Customer Orders History
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

$pageTitle = 'My Orders';
$db = getDBConnection();
$userId = (int)$_SESSION['user_id'];

// Fetch customer orders
$stmt = $db->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS item_count
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.id DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">My Orders</span>
    </div>
</div>

<div class="container" style="padding: 50px 20px;">
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 36px;">
        <!-- Account Sidebar -->
        <div class="shop-sidebar">
            <ul class="category-filter-list">
                <li><a href="<?= BASE_URL ?>my-orders.php" class="active"><span>📦 My Orders</span></a></li>
                <li><a href="<?= BASE_URL ?>profile.php"><span>👤 Account Profile</span></a></li>
                <li><a href="<?= BASE_URL ?>wishlist.php"><span>♥ Saved Wishlist</span></a></li>
                <li><a href="<?= BASE_URL ?>logout.php" style="color: #d32f2f;"><span>🚪 Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Orders List -->
        <div>
            <?= displayFlash() ?>
            <h2 style="font-size: 1.8rem; margin-bottom: 24px;">Your Skincare Orders</h2>

            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px solid #ede4e0;">
                    <div style="font-size: 3rem; margin-bottom: 12px;">🛍️</div>
                    <h3>You haven't placed any orders yet</h3>
                    <p>Discover our range of clean botanical skincare and place your first order today!</p>
                    <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary" style="margin-top: 14px;">Shop Now</a>
                </div>
            <?php else: ?>
                <div class="cart-table-wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total (PKR)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($order['order_number']) ?></strong><br>
                                        <span style="font-size: 0.78rem; color: #726264;">Cash on Delivery</span>
                                    </td>
                                    <td style="font-size: 0.88rem;">
                                        <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?= $order['item_count'] ?> item(s)
                                    </td>
                                    <td>
                                        <strong><?= formatPrice($order['total_amount']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?= e($order['order_status']) ?>">
                                            <?= ucfirst(e($order['order_status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>order-details.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
