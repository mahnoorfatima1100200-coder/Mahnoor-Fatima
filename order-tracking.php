<?php
/**
 * Skinora - Public Order Tracking
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Track Your Order';
$db = getDBConnection();

$orderNumber = cleanInput($_GET['order_number'] ?? '');
$contact = cleanInput($_GET['contact'] ?? '');
$trackedOrder = null;
$searched = false;

if (!empty($orderNumber)) {
    $searched = true;
    $stmt = $db->prepare("
        SELECT * FROM orders 
        WHERE order_number = ? 
        AND (customer_email = ? OR customer_phone = ? OR ? = '')
        LIMIT 1
    ");
    $stmt->execute([$orderNumber, $contact, $contact, $contact]);
    $trackedOrder = $stmt->fetch();
}

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Track Order</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px;">
    <div style="max-width: 650px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 30px;">
            <span class="section-subtitle">Real-Time Updates</span>
            <h1 style="font-size: 2.2rem;">Track Your Skincare Delivery</h1>
            <p>Enter your unique order number (e.g. <code>SKN-2026-9214A</code>) to see current status.</p>
        </div>

        <div class="form-card" style="margin-bottom: 30px;">
            <form action="<?= BASE_URL ?>order-tracking.php" method="GET">
                <div class="form-group">
                    <label class="form-label">Order Number *</label>
                    <input type="text" name="order_number" class="form-control" placeholder="e.g. SKN-2026-9214A" value="<?= e($orderNumber) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number or Email (Optional)</label>
                    <input type="text" name="contact" class="form-control" placeholder="Verification phone or email" value="<?= e($contact) ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Track Order Status</button>
            </form>
        </div>

        <?php if ($searched): ?>
            <?php if ($trackedOrder): ?>
                <div class="form-card" style="background: #fffdfa; border: 1px solid #c59b97;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #ede4e0; padding-bottom: 14px;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.25rem;">Order: <?= e($trackedOrder['order_number']) ?></h3>
                            <span style="font-size: 0.8rem; color: #726264;">Placed on <?= date('M d, Y', strtotime($trackedOrder['created_at'])) ?></span>
                        </div>
                        <span class="status-pill status-<?= e($trackedOrder['order_status']) ?>" style="font-size: 0.85rem; padding: 6px 14px;">
                            <?= ucfirst(e($trackedOrder['order_status'])) ?>
                        </span>
                    </div>

                    <!-- Progress bar -->
                    <?php
                    $stages = ['pending', 'processing', 'shipped', 'delivered'];
                    $currentStageIndex = array_search($trackedOrder['order_status'], $stages);
                    if ($currentStageIndex === false) $currentStageIndex = 0;
                    ?>
                    <div style="display: flex; justify-content: space-between; position: relative; margin: 30px 10px 40px;">
                        <div style="position: absolute; top: 14px; left: 0; right: 0; height: 3px; background: #ede4e0; z-index: 1;"></div>
                        <div style="position: absolute; top: 14px; left: 0; width: <?= ($currentStageIndex / 3) * 100 ?>%; height: 3px; background: #b37d7a; z-index: 2; transition: width 0.5s;"></div>
                        
                        <?php foreach (['Confirmed', 'Processing', 'Dispatched', 'Delivered'] as $idx => $label): ?>
                            <div style="text-align: center; position: relative; z-index: 3;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: <?= $idx <= $currentStageIndex ? '#b37d7a' : '#fff' ?>; color: <?= $idx <= $currentStageIndex ? '#fff' : '#726264' ?>; border: 2px solid <?= $idx <= $currentStageIndex ? '#b37d7a' : '#ede4e0' ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; font-weight: 600; font-size: 0.8rem;">
                                    <?= $idx + 1 ?>
                                </div>
                                <span style="font-size: 0.76rem; font-weight: <?= $idx <= $currentStageIndex ? '600' : '400' ?>; color: <?= $idx <= $currentStageIndex ? '#2c2425' : '#726264' ?>;"><?= $label ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="font-size: 0.9rem; line-height: 1.8;">
                        <div><strong>Recipient:</strong> <?= e($trackedOrder['customer_name']) ?></div>
                        <div><strong>Delivery Destination:</strong> <?= e($trackedOrder['shipping_address']) ?>, <?= e($trackedOrder['city']) ?></div>
                        <div><strong>Total Amount:</strong> <?= formatPrice($trackedOrder['total_amount']) ?> (Cash on Delivery)</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    No order found matching order number "<strong><?= e($orderNumber) ?></strong>". Please verify your order number and try again.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
