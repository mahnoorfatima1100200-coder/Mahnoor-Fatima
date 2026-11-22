<?php
/**
 * Skinora - Checkout Page
 * Cash on Delivery (COD), Delivery Address, Order Summary
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Secure Checkout';
$db = getDBConnection();
$userId = $_SESSION['user_id'] ?? null;
$cartDetails = getCartDetails($db, $userId);

if (empty($cartDetails['items'])) {
    setFlash('warning', 'Your shopping bag is empty. Please add items before checking out.');
    redirect('shop.php');
}

// Fetch user default address if logged in
$user = null;
$defaultAddress = null;
if ($userId) {
    $uStmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $uStmt->execute([$userId]);
    $user = $uStmt->fetch();

    $addrStmt = $db->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC LIMIT 1");
    $addrStmt->execute([$userId]);
    $defaultAddress = $addrStmt->fetch();
}

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?= BASE_URL ?>cart.php">Shopping Bag</a>
        <span class="separator">/</span>
        <span class="current">Checkout</span>
    </div>
</div>

<div class="container" style="padding: 40px 20px;">
    <?= displayFlash() ?>

    <h1 style="font-size: 2.2rem; margin-bottom: 24px;">Complete Your Order</h1>

    <form action="<?= BASE_URL ?>actions/place-order.php" method="POST">
        <?= csrfField() ?>

        <div class="checkout-grid">
            <!-- Left: Delivery Information Form -->
            <div>
                <div class="form-card" style="margin-bottom: 24px;">
                    <h3 style="font-size: 1.35rem; margin-bottom: 20px; border-bottom: 1px solid #ede4e0; padding-bottom: 12px;">
                        1. Contact & Delivery Details
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Recipient's Name" 
                                   value="<?= e($defaultAddress['full_name'] ?? $user['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="customer_phone" class="form-control" placeholder="e.g. 0300 1234567" 
                                   value="<?= e($defaultAddress['phone'] ?? $user['phone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="Order updates will be sent here" 
                               value="<?= e($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Complete Street Address *</label>
                        <input type="text" name="shipping_address" class="form-control" placeholder="House/Flat #, Street, Phase/Sector, Area" 
                               value="<?= e($defaultAddress['address_line1'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Lahore, Karachi, Islamabad" 
                                   value="<?= e($defaultAddress['city'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal / ZIP Code</label>
                            <input type="text" name="postal_code" class="form-control" placeholder="e.g. 54000" 
                                   value="<?= e($defaultAddress['postal_code'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Order Notes / Landmark (Optional)</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Specific delivery instructions or landmarks..."></textarea>
                    </div>
                </div>

                <!-- Payment Selection -->
                <div class="form-card">
                    <h3 style="font-size: 1.35rem; margin-bottom: 20px; border-bottom: 1px solid #ede4e0; padding-bottom: 12px;">
                        2. Payment Method
                    </h3>

                    <div style="border: 2px solid #b37d7a; background: #fffdfa; border-radius: 8px; padding: 18px; margin-bottom: 14px; display: flex; align-items: center; gap: 14px;">
                        <input type="radio" name="payment_method" value="cod" id="pay-cod" checked style="accent-color: #b37d7a; width: 18px; height: 18px;">
                        <label for="pay-cod" style="font-weight: 600; cursor: pointer; flex-grow: 1;">
                            💵 Cash on Delivery (COD)
                            <span style="display: block; font-size: 0.82rem; font-weight: normal; color: #726264; margin-top: 2px;">
                                Pay securely in cash when your parcel is delivered at your doorstep.
                            </span>
                        </label>
                    </div>

                    <div style="border: 1px solid #ede4e0; background: #faf6f4; border-radius: 8px; padding: 18px; opacity: 0.75; display: flex; align-items: center; gap: 14px;">
                        <input type="radio" name="payment_method" value="online" id="pay-online" disabled style="width: 18px; height: 18px;">
                        <label for="pay-online" style="cursor: not-allowed; flex-grow: 1;">
                            💳 Debit / Credit Card & Bank Transfer
                            <span style="display: block; font-size: 0.8rem; color: #726264; margin-top: 2px;">
                                (Online payment gateway integration coming soon - Please select Cash on Delivery)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div>
                <div class="cart-summary-box">
                    <h3 style="font-size: 1.25rem; margin-bottom: 18px;">Items In Your Order</h3>

                    <div style="max-height: 280px; overflow-y: auto; margin-bottom: 18px; padding-right: 6px;">
                        <?php foreach ($cartDetails['items'] as $item): ?>
                            <div style="display: flex; gap: 12px; margin-bottom: 14px; align-items: center;">
                                <img src="<?= BASE_URL ?>uploads/products/<?= e($item['main_image']) ?>" alt="<?= e($item['name']) ?>" style="width: 48px; height: 48px; border-radius: 6px; background: #fceee9; padding: 4px;">
                                <div style="flex-grow: 1;">
                                    <div style="font-size: 0.88rem; font-weight: 500;"><?= e($item['name']) ?></div>
                                    <span style="font-size: 0.78rem; color: #726264;"><?= $item['quantity'] ?> &times; <?= formatPrice($item['effective_price']) ?></span>
                                </div>
                                <div style="font-size: 0.9rem; font-weight: 600;">
                                    <?= formatPrice($item['line_total']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?= formatPrice($cartDetails['subtotal']) ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping Delivery Fee</span>
                        <span><?= $cartDetails['shipping_fee'] > 0 ? formatPrice($cartDetails['shipping_fee']) : 'FREE' ?></span>
                    </div>

                    <?php if ($cartDetails['discount_amount'] > 0): ?>
                        <div class="summary-row" style="color: #2e7d32;">
                            <span>Discount (<?= e($cartDetails['coupon']['code']) ?>)</span>
                            <span>- <?= formatPrice($cartDetails['discount_amount']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row summary-total">
                        <span>Total Due (PKR)</span>
                        <span style="color: #b37d7a;"><?= formatPrice($cartDetails['final_total']) ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 24px; font-size: 1.05rem; padding: 14px;">
                        Place Order (Cash on Delivery)
                    </button>

                    <p style="text-align: center; font-size: 0.76rem; color: #8c7e80; margin-top: 14px; line-height: 1.5;">
                        🔒 By placing your order you agree to Skinora's Terms & Conditions and Privacy Policy.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
