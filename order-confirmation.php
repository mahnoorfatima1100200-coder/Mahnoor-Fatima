<?php
/**
 * Skinora - Order Confirmation Page
 */
require_once __DIR__ . '/config/config.php';

$orderNumber = cleanInput($_GET['order'] ?? '');
if (empty($orderNumber)) {
    redirect('index.php');
}

$db = getDBConnection();

$stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

$pageTitle = 'Order Confirmed - ' . $order['order_number'];

// Fetch order items
$itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$order['id']]);
$orderItems = $itemStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="container" style="padding: 60px 20px;">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; font-size: 2.4rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            ✓
        </div>

        <span class="section-subtitle">Thank You For Your Order!</span>
        <h1 style="font-size: 2.4rem; margin-bottom: 12px;">Your Glow is On Its Way</h1>
        <p style="font-size: 1rem; color: #726264; margin-bottom: 24px;">
            We've received your order and are carefully preparing your skincare treats. A confirmation receipt has been logged under order <strong>#<?= e($order['order_number']) ?></strong>.
        </p>

        <div class="form-card" style="text-align: left; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ede4e0; padding-bottom: 14px; margin-bottom: 18px;">
                <div>
                    <span style="font-size: 0.8rem; color: #726264;">Order Number:</span><br>
                    <strong><?= e($order['order_number']) ?></strong>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 0.8rem; color: #726264;">Date:</span><br>
                    <strong><?= date('M d, Y', strtotime($order['created_at'])) ?></strong>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 8px;">Delivery Details</h4>
                <p style="font-size: 0.9rem; margin: 0; line-height: 1.7;">
                    <strong><?= e($order['customer_name']) ?></strong><br>
                    <?= e($order['shipping_address']) ?>, <?= e($order['city']) ?><?= !empty($order['postal_code']) ? ' - ' . e($order['postal_code']) : '' ?><br>
                    📞 Phone: <?= e($order['customer_phone']) ?><br>
                    ✉️ Email: <?= e($order['customer_email']) ?>
                </p>
            </div>

            <div style="border-top: 1px solid #ede4e0; padding-top: 16px; margin-bottom: 16px;">
                <h4 style="margin-bottom: 12px;">Ordered Products</h4>
                <?php foreach ($orderItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px;">
                        <span><?= e($item['product_name']) ?> &times; <?= $item['quantity'] ?></span>
                        <strong><?= formatPrice($item['subtotal']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 1px solid #ede4e0; padding-top: 14px; font-size: 0.94rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Subtotal:</span>
                    <span><?= formatPrice($order['subtotal']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Shipping:</span>
                    <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'FREE' ?></span>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #2e7d32;">
                        <span>Discount:</span>
                        <span>- <?= formatPrice($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: #b37d7a; border-top: 1px solid #ede4e0; padding-top: 10px; margin-top: 10px;">
                    <span>Total (Cash on Delivery):</span>
                    <span><?= formatPrice($order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 14px;">
            <a href="<?= BASE_URL ?>order-tracking.php?order_number=<?= urlencode($order['order_number']) ?>" class="btn btn-outline">
                Track Order
            </a>
            <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary">
                Continue Shopping
            </a>
            <button onclick="window.print()" class="btn btn-outline">
                Print Receipt
            </button>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
