<?php
/**
 * Skinora - Privacy Policy
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Privacy Policy';
require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Privacy Policy</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px; max-width: 850px;">
    <h1 style="font-size: 2.4rem; margin-bottom: 20px;">Privacy Policy</h1>
    <p style="font-size: 0.85rem; color: #726264;">Last updated: <?= date('F Y') ?></p>

    <div style="line-height: 1.8; color: #443739;">
        <h3 style="margin-top: 24px; font-size: 1.25rem;">1. Information We Collect</h3>
        <p>When you browse, create an account, or place a Cash on Delivery order with Skinora, we collect essential information required to deliver your parcel including your name, email address, telephone number, and physical shipping address.</p>

        <h3 style="margin-top: 24px; font-size: 1.25rem;">2. Payment Security</h3>
        <p>We take your privacy seriously. Skinora never asks for or stores credit/debit card numbers for Cash on Delivery transactions. All data submitted via our website is secured via industry-standard SSL encryption and protected PDO database statements.</p>

        <h3 style="margin-top: 24px; font-size: 1.25rem;">3. Use of Cookies</h3>
        <p>We use session cookies solely to preserve your active shopping bag contents and secure your customer login state between page visits. We do not sell your personal data to third parties.</p>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
