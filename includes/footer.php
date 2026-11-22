<?php
/**
 * Skinora - Global Customer Footer
 */
?>
<!-- Newsletter Subscription Strip -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-box">
            <span class="section-subtitle">Join The Glow Club</span>
            <h2 style="font-size: 2rem; margin-bottom: 8px;">Enjoy 10% Off Your First Skincare Order</h2>
            <p>Subscribe for exclusive beauty tips, seasonal sales, and early access to new product formulations.</p>
            <form action="<?= BASE_URL ?>actions/newsletter.php" method="POST" class="newsletter-form">
                <?= csrfField() ?>
                <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address..." required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<!-- Footer Main -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?= BASE_URL ?>index.php" class="brand-logo" style="color: #fff; margin-bottom: 8px;">
                    Skinora<span style="color: #b37d7a;">.</span>
                </a>
                <p>Pure, dermatologically backed skincare formulated to nurture and restore your skin’s barrier. Handcrafted with clean actives to unveil your effortless, natural glow.</p>
                <p style="margin-top: 14px; font-size: 0.84rem; color: #b37d7a;">
                    🌱 100% Cruelty-Free &bull; Paraben-Free &bull; Halal Compliant
                </p>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Customer Care</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>order-tracking.php">Track Your Order</a></li>
                    <li><a href="<?= BASE_URL ?>my-orders.php">Order History</a></li>
                    <li><a href="<?= BASE_URL ?>faq.php">Frequently Asked Questions</a></li>
                    <li><a href="<?= BASE_URL ?>privacy-policy.php">Privacy & Cookie Policy</a></li>
                    <li><a href="<?= BASE_URL ?>terms.php">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Shop Categories -->
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>category.php?slug=serums">Brightening Serums</a></li>
                    <li><a href="<?= BASE_URL ?>category.php?slug=moisturizers">Barrier Moisturizers</a></li>
                    <li><a href="<?= BASE_URL ?>category.php?slug=face-wash">Gentle Face Washes</a></li>
                    <li><a href="<?= BASE_URL ?>category.php?slug=sunscreens">Invisible Sunscreens</a></li>
                    <li><a href="<?= BASE_URL ?>category.php?slug=toners">Balancing Toners</a></li>
                    <li><a href="<?= BASE_URL ?>category.php?slug=lip-care">Nourishing Lip Care</a></li>
                </ul>
            </div>

            <!-- Contact & Store Info -->
            <div class="footer-col footer-contact">
                <h4>Skinora Studio</h4>
                <ul>
                    <li>
                        <span>📍</span>
                        <span>Skinora Flagship, MM Alam Road, Gulberg III, Lahore, Pakistan</span>
                    </li>
                    <li>
                        <span>📞</span>
                        <span><?= SUPPORT_PHONE ?> (Mon-Sat, 10am-7pm)</span>
                    </li>
                    <li>
                        <span>✉️</span>
                        <span><?= SUPPORT_EMAIL ?></span>
                    </li>
                    <li>
                        <span>📦</span>
                        <span>Nationwide Cash on Delivery (COD) available</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div>
                &copy; <?= date('Y') ?> <strong><?= SITE_NAME ?></strong>. All rights reserved. Discover Your Natural Glow.
            </div>
            <div class="payment-methods">
                <span style="font-size: 0.78rem; margin-right: 6px;">Accepted Payments:</span>
                <span class="payment-badge">Cash on Delivery (COD)</span>
                <span class="payment-badge">Bank Transfer</span>
                <span class="payment-badge">Credit / Debit Card</span>
            </div>
        </div>
    </div>
</footer>

<!-- Toast notification container -->
<div id="toast-container"></div>

<!-- JavaScripts -->
<script src="<?= BASE_URL ?>assets/js/script.js"></script>
<script src="<?= BASE_URL ?>assets/js/cart.js"></script>
</body>
</html>
