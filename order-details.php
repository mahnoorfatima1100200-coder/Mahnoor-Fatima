<?php
/**
 * Skinora - Customer Order Details / Invoice Breakdown
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

$orderId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = (int)$_SESSION['user_id'];

$db = getDBConnection();

// Fetch order verifying ownership
$stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('my-orders.php');
}

$pageTitle = 'Order Details - ' . $order['order_number'];

// Fetch order items snapshots
$itemsStmt = $db->prepare("
    SELECT oi.*, p.main_image, p.slug 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?= BASE_URL ?>my-orders.php">My Orders</a>
        <span class="separator">/</span>
        <span class="current"><?= e($order['order_number']) ?></span>
    </div>
</div>

<div class="container" style="padding: 50px 20px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 1.8rem;">Order #<?= e($order['order_number']) ?></h2>
                <span style="color: #726264; font-size: 0.88rem;">Placed on <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div>
                <span class="status-pill status-<?= e($order['order_status']) ?>" style="font-size: 0.85rem; padding: 6px 14px;">
                    Status: <?= ucfirst(e($order['order_status'])) ?>
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px;">
            <div class="form-card">
                <h4 style="margin-bottom: 10px;">Delivery Address</h4>
                <p style="font-size: 0.9rem; margin: 0; line-height: 1.7;">
                    <strong><?= e($order['customer_name']) ?></strong><br>
                    <?= e($order['shipping_address']) ?><br>
                    <?= e($order['city']) ?><?= !empty($order['postal_code']) ? ' - ' . e($order['postal_code']) : '' ?><br>
                    📞 Phone: <?= e($order['customer_phone']) ?><br>
                    ✉️ Email: <?= e($order['customer_email']) ?>
                </p>
            </div>

            <div class="form-card">
                <h4 style="margin-bottom: 10px;">Payment & Summary</h4>
                <p style="font-size: 0.9rem; margin: 0; line-height: 1.7;">
                    <strong>Payment Method:</strong> Cash on Delivery (COD)<br>
                    <strong>Payment Status:</strong> <?= ucfirst(e($order['payment_status'])) ?><br>
                    <?php if (!empty($order['coupon_code'])): ?>
                        <strong>Coupon Applied:</strong> <?= e($order['coupon_code']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($order['notes'])): ?>
                        <strong>Special Notes:</strong> <?= e($order['notes']) ?><br>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="cart-table-wrapper" style="margin-bottom: 30px;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="cart-item-info">
                                    <?php if (!empty($item['main_image'])): ?>
                                        <img src="<?= BASE_URL ?>uploads/products/<?= e($item['main_image']) ?>" alt="<?= e($item['product_name']) ?>" class="cart-item-img">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= e($item['product_name']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?= formatPrice($item['product_price']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><strong><?= formatPrice($item['subtotal']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals breakdown -->
        <div class="cart-summary-box" style="max-width: 400px; margin-left: auto;">
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= formatPrice($order['subtotal']) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping Charges</span>
                <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'FREE' ?></span>
            </div>
            <?php if ($order['discount_amount'] > 0): ?>
                <div class="summary-row" style="color: #2e7d32;">
                    <span>Discount</span>
                    <span>- <?= formatPrice($order['discount_amount']) ?></span>
                </div>
            <?php endif; ?>
            <div class="summary-row summary-total">
                <span>Grand Total</span>
                <span style="color: #b37d7a;"><?= formatPrice($order['total_amount']) ?></span>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: space-between;">
            <a href="<?= BASE_URL ?>my-orders.php" class="btn btn-outline">&larr; Back to Orders</a>
            <button onclick="window.print()" class="btn btn-primary">Print Order Invoice</button>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
