<?php
/**
 * Skinora - Admin Order Details & Status Updater
 */
require_once __DIR__ . '/includes/auth.php';

$orderId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$db = getDBConnection();

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('admin/orders.php');
}

$adminPageTitle = 'Manage Order #' . $order['order_number'];

// Update Order Status POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    validateCsrfOrDie();

    $newStatus = cleanInput($_POST['order_status'] ?? 'pending');
    $paymentStatus = cleanInput($_POST['payment_status'] ?? 'pending');

    $upd = $db->prepare("UPDATE orders SET order_status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?");
    $upd->execute([$newStatus, $paymentStatus, $orderId]);

    setFlash('success', "Order status updated to '" . ucfirst($newStatus) . "'.");
    redirect('admin/order-details.php?id=' . $orderId);
}

// Fetch order items
$itemsStmt = $db->prepare("
    SELECT oi.*, p.main_image, p.sku 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$orderId]);
$orderItems = $itemsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Order Breakdown -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h3 class="admin-card-title">Order #<?= e($order['order_number']) ?></h3>
                    <span style="font-size: 0.8rem; color: #6c757d;">Placed: <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <a href="<?= BASE_URL ?>admin/orders.php" class="btn-admin btn-admin-outline">&larr; Back to Orders</a>
            </div>

            <!-- Items Table -->
            <table class="admin-table" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e($item['product_name']) ?></strong>
                                <?php if (!empty($item['sku'])): ?>
                                    <br><span style="font-size: 0.74rem; color: #6c757d;">SKU: <?= e($item['sku']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatPrice($item['product_price']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><strong><?= formatPrice($item['subtotal']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="max-width: 350px; margin-left: auto; font-size: 0.9rem; line-height: 1.8;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Subtotal:</span>
                    <span><?= formatPrice($order['subtotal']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Shipping Charges:</span>
                    <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'FREE' ?></span>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; color: #2e7d32;">
                        <span>Coupon Discount (<?= e($order['coupon_code']) ?>):</span>
                        <span>- <?= formatPrice($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 700; color: #b37d7a; border-top: 1px solid #e9ecef; padding-top: 8px; margin-top: 8px;">
                    <span>Total PKR:</span>
                    <span><?= formatPrice($order['total_amount']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Customer Info & Status Update -->
    <div>
        <div class="admin-card">
            <h4 style="margin-bottom: 14px; font-size: 1.05rem;">Update Order Status</h4>
            <form action="<?= BASE_URL ?>admin/order-details.php?id=<?= $orderId ?>" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="update_status" value="1">

                <div class="admin-form-group">
                    <label class="admin-label">Fulfillment Status</label>
                    <select name="order_status" class="admin-select">
                        <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending (New Order)</option>
                        <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing (Packing)</option>
                        <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped (Dispatched with Courier)</option>
                        <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered (Completed)</option>
                        <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Payment Status</label>
                    <select name="payment_status" class="admin-select">
                        <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending (COD unpaid)</option>
                        <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Cash collected)</option>
                        <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>

                <button type="submit" class="btn-admin btn-admin-primary btn-block">Save Status Changes</button>
            </form>
        </div>

        <div class="admin-card">
            <h4 style="margin-bottom: 12px; font-size: 1.05rem;">Delivery Destination</h4>
            <p style="font-size: 0.88rem; line-height: 1.7; margin: 0;">
                <strong><?= e($order['customer_name']) ?></strong><br>
                <?= e($order['shipping_address']) ?><br>
                <?= e($order['city']) ?><?= !empty($order['postal_code']) ? ' - ' . e($order['postal_code']) : '' ?><br>
                📞 Phone: <?= e($order['customer_phone']) ?><br>
                ✉️ Email: <?= e($order['customer_email']) ?><br>
                <?php if (!empty($order['notes'])): ?>
                    <em style="color: #6c757d;">Notes: <?= e($order['notes']) ?></em>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
