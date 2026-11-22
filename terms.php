<?php
/**
 * Skinora - Terms & Conditions
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Terms & Conditions';
require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Terms & Conditions</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px; max-width: 850px;">
    <h1 style="font-size: 2.4rem; margin-bottom: 20px;">Terms & Conditions</h1>
    <p style="font-size: 0.85rem; color: #726264;">Last updated: <?= date('F Y') ?></p>

    <div style="line-height: 1.8; color: #443739;">
        <h3 style="margin-top: 24px; font-size: 1.25rem;">1. Orders & Pricing</h3>
        <p>All prices displayed on Skinora are quoted in Pakistani Rupees (PKR) and include applicable taxes. We reserve the right to modify prices or discontinue products without prior notice.</p>

        <h3 style="margin-top: 24px; font-size: 1.25rem;">2. Cash on Delivery Terms</h3>
        <p>By placing an order via Cash on Delivery, you commit to receiving the parcel and paying the full invoice amount to the courier agent upon arrival. False orders or refusal to accept verified parcels may result in account termination.</p>

        <h3 style="margin-top: 24px; font-size: 1.25rem;">3. Skincare Disclaimers</h3>
        <p>Skinora products are intended for cosmetic and external topical use only. While our ingredients are dermatologist-tested and formulated for sensitivity, individual skin reactions can vary. We recommend patch-testing all new formulations prior to full facial application.</p>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
