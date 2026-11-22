<?php
/**
 * Skinora - Shopping Cart Page
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Your Shopping Cart';
$db = getDBConnection();

$userId = $_SESSION['user_id'] ?? null;
$cartDetails = getCartDetails($db, $userId);

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Shopping Bag</span>
    </div>
</div>

<div class="container" style="padding: 40px 20px;">
    <?= displayFlash() ?>

    <h1 style="font-size: 2.2rem; margin-bottom: 24px;">Your Skincare Bag (<?= $cartDetails['item_count'] ?>)</h1>

    <?php if (empty($cartDetails['items'])): ?>
        <div style="text-align: center; padding: 70px 20px; background: #fff; border-radius: 12px; border: 1px solid #ede4e0; max-width: 650px; margin: 0 auto;">
            <div style="font-size: 3.5rem; margin-bottom: 12px;">🛍️</div>
            <h2>Your Bag is Currently Empty</h2>
            <p>Looks like you haven't added any glowing skincare treats yet.</p>
            <a href="<?= BASE_URL ?>shop.php" class="btn btn-primary" style="margin-top: 16px;">Explore The Shop</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Items Table -->
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartDetails['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="cart-item-info">
                                        <img src="<?= BASE_URL ?>uploads/products/<?= e($item['main_image']) ?>" alt="<?= e($item['name']) ?>" class="cart-item-img">
                                        <div>
                                            <span style="font-size: 0.74rem; color: #b37d7a; text-transform: uppercase; font-weight: 600;"><?= e($item['category_name']) ?></span>
                                            <h4 style="margin: 2px 0 4px; font-size: 0.95rem;">
                                                <a href="<?= BASE_URL ?>product.php?slug=<?= e($item['slug']) ?>"><?= e($item['name']) ?></a>
                                            </h4>
                                            <?php if ($item['stock_quantity'] < $item['quantity']): ?>
                                                <span style="font-size: 0.74rem; color: #d32f2f;">Only <?= $item['stock_quantity'] ?> left in stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= formatPrice($item['effective_price']) ?></td>
                                <td>
                                    <div class="qty-control" style="width: fit-content;">
                                        <button type="button" class="qty-btn qty-minus">&minus;</button>
                                        <input type="number" class="qty-input cart-qty-change" data-item-id="<?= $item['item_id'] ?>" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>">
                                        <button type="button" class="qty-btn qty-plus">&plus;</button>
                                    </div>
                                </td>
                                <td><strong><?= formatPrice($item['line_total']) ?></strong></td>
                                <td>
                                    <form action="<?= BASE_URL ?>actions/remove-cart.php" method="POST" onsubmit="return confirm('Remove this product from your bag?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                        <button type="submit" style="background:none; border:none; color:#9e8e90; cursor:pointer; font-size:1.2rem;" title="Remove Item">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary & Coupon Sidebar -->
            <div class="cart-summary-box">
                <h3 style="font-size: 1.25rem; margin-bottom: 18px;">Order Summary</h3>

                <!-- Free shipping progress bar -->
                <?php if ($cartDetails['subtotal'] < FREE_SHIPPING_THRESHOLD): ?>
                    <div style="background: #faf6f4; border: 1px solid #ede4e0; border-radius: 8px; padding: 12px; margin-bottom: 20px; font-size: 0.82rem;">
                        Add <strong><?= formatPrice(FREE_SHIPPING_THRESHOLD - $cartDetails['subtotal']) ?></strong> more to get <strong>FREE Delivery</strong>!
                    </div>
                <?php else: ?>
                    <div style="background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; padding: 10px; margin-bottom: 20px; font-size: 0.82rem; text-align: center;">
                        🎉 You've unlocked <strong>FREE Shipping</strong>!
                    </div>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?= formatPrice($cartDetails['subtotal']) ?></span>
                </div>

                <div class="summary-row">
                    <span>Estimated Shipping</span>
                    <span><?= $cartDetails['shipping_fee'] > 0 ? formatPrice($cartDetails['shipping_fee']) : 'FREE' ?></span>
                </div>

                <?php if ($cartDetails['discount_amount'] > 0): ?>
                    <div class="summary-row" style="color: #2e7d32;">
                        <span>Discount (<?= e($cartDetails['coupon']['code']) ?>)</span>
                        <span>- <?= formatPrice($cartDetails['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row summary-total">
                    <span>Total Amount</span>
                    <span style="color: #b37d7a;"><?= formatPrice($cartDetails['final_total']) ?></span>
                </div>

                <!-- Coupon Form -->
                <div style="margin: 20px 0; border-top: 1px solid #ede4e0; padding-top: 18px;">
                    <form action="<?= BASE_URL ?>actions/apply-coupon.php" method="POST" style="display: flex; gap: 8px;">
                        <?= csrfField() ?>
                        <input type="text" name="coupon_code" class="form-control" placeholder="Promo code (e.g. GLOW10)" value="<?= e($cartDetails['coupon']['code'] ?? '') ?>" style="text-transform: uppercase;">
                        <button type="submit" class="btn btn-outline btn-sm">Apply</button>
                    </form>
                    <?php if (!empty($cartDetails['coupon'])): ?>
                        <div style="margin-top: 8px; font-size: 0.78rem; color: #2e7d32;">
                            ✓ Coupon "<?= e($cartDetails['coupon']['code']) ?>" applied!
                        </div>
                    <?php endif; ?>
                </div>

                <a href="<?= BASE_URL ?>checkout.php" class="btn btn-primary btn-block">
                    Proceed to Checkout &rarr;
                </a>

                <div style="text-align: center; margin-top: 14px;">
                    <a href="<?= BASE_URL ?>shop.php" style="font-size: 0.85rem; color: #726264;">&larr; Continue Shopping</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
