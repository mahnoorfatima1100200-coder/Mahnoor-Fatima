<?php
/**
 * Skinora - Frequently Asked Questions (FAQ)
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Frequently Asked Questions (FAQ)';
$metaDescription = 'Find answers to common questions about Skinora skincare products, orders, shipping timelines, Cash on Delivery, and returns.';

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">FAQ</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px; max-width: 850px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <span class="section-subtitle">Got Questions?</span>
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Frequently Asked Questions</h1>
        <p>Everything you need to know about our products, delivery, and skincare rituals.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="background: #fff; border: 1px solid #ede4e0; border-radius: 10px; padding: 22px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 8px; color: #b37d7a;">How long does delivery take across Pakistan?</h3>
            <p style="margin: 0; font-size: 0.94rem;">We dispatch all orders within 24 hours. For Lahore, deliveries typically arrive within 1-2 business days. For Karachi, Islamabad, Rawalpindi, and other major cities, orders arrive in 2-3 business days. Remote locations may take 3-4 days.</p>
        </div>

        <div style="background: #fff; border: 1px solid #ede4e0; border-radius: 10px; padding: 22px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 8px; color: #b37d7a;">How does Cash on Delivery (COD) work?</h3>
            <p style="margin: 0; font-size: 0.94rem;">With Cash on Delivery, you don't need to enter any bank card details online. Simply place your order and have the exact cash amount ready to pay the courier delivery rider when your parcel arrives.</p>
        </div>

        <div style="background: #fff; border: 1px solid #ede4e0; border-radius: 10px; padding: 22px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 8px; color: #b37d7a;">Are Skinora formulas suitable for sensitive skin?</h3>
            <p style="margin: 0; font-size: 0.94rem;">Yes! All Skinora formulas are dermatologically developed with soothing botanical extracts and gentle barrier-repairing ceramides. We formulate without drying alcohols, harsh artificial fragrances, and sulphates. We always recommend performing a 24-hour patch test before introducing any active serum.</p>
        </div>

        <div style="background: #fff; border: 1px solid #ede4e0; border-radius: 10px; padding: 22px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 8px; color: #b37d7a;">Does the Invisible Shield Sunscreen leave a white cast?</h3>
            <p style="margin: 0; font-size: 0.94rem;">Absolutely not! Our Invisible Shield SPF 50+ is engineered with micronized filters specifically tested on South Asian skin tones to dry down completely clear with zero greasiness and zero white cast.</p>
        </div>

        <div style="background: #fff; border: 1px solid #ede4e0; border-radius: 10px; padding: 22px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 8px; color: #b37d7a;">What is your return and exchange policy?</h3>
            <p style="margin: 0; font-size: 0.94rem;">If an item arrives damaged or incorrect, please notify our customer support team within 48 hours of delivery along with photographic proof. We will dispatch an immediate replacement free of charge.</p>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
