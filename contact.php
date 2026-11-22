<?php
/**
 * Skinora - Contact Us
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Contact Us - Skinora Beauty House';
$metaDescription = 'Get in touch with Skinora customer support for product consultations, order tracking, and skincare advice.';

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Contact Us</span>
    </div>
</div>

<div class="container" style="padding: 50px 20px;">
    <?= displayFlash() ?>

    <div style="text-align: center; margin-bottom: 40px;">
        <span class="section-subtitle">We Love Hearing From You</span>
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Get In Touch With Skinora</h1>
        <p style="max-width: 600px; margin: 0 auto;">Have a question about an ingredient, your order status, or need personalized skincare advice? Our beauty advisors are here to help.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 40px;">
        <!-- Left: Studio Details -->
        <div>
            <div class="form-card" style="margin-bottom: 24px;">
                <h3 style="font-size: 1.3rem; margin-bottom: 16px;">Flagship Studio</h3>
                <p style="line-height: 1.8; font-size: 0.92rem;">
                    📍 <strong>Address:</strong><br>
                    <?= STORE_ADDRESS ?><br><br>
                    📞 <strong>Phone & WhatsApp:</strong><br>
                    <?= SUPPORT_PHONE ?><br><br>
                    ✉️ <strong>Customer Email:</strong><br>
                    <?= SUPPORT_EMAIL ?><br><br>
                    ⏰ <strong>Opening Hours:</strong><br>
                    Monday – Saturday: 10:00 AM – 7:00 PM<br>
                    Sunday: Closed
                </p>
            </div>

            <div class="form-card" style="background: #faf6f4;">
                <h4 style="margin-bottom: 8px;">Order Questions?</h4>
                <p style="font-size: 0.88rem; margin-bottom: 12px;">If you already have an order number, you can check its live delivery status online instantly.</p>
                <a href="<?= BASE_URL ?>order-tracking.php" class="btn btn-outline btn-sm">Track Order Online &rarr;</a>
            </div>
        </div>

        <!-- Right: Contact Form -->
        <div class="form-card">
            <h3 style="font-size: 1.3rem; margin-bottom: 20px;">Send Us A Message</h3>

            <form action="<?= BASE_URL ?>actions/contact.php" method="POST">
                <?= csrfField() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Your Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ayesha Khan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="ayesha@example.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="0300 1234567">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Product recommendation" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Message *</label>
                    <textarea name="message" rows="5" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
