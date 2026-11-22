<?php
/**
 * Skinora - Customer Registration
 */
require_once __DIR__ . '/config/config.php';
requireGuest();

$pageTitle = 'Create a Skinora Account';

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Create Account</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px;">
    <div style="max-width: 500px; margin: 0 auto;">
        <?= displayFlash() ?>

        <div class="form-card">
            <div style="text-align: center; margin-bottom: 28px;">
                <span class="section-subtitle">Join Skinora</span>
                <h2 style="font-size: 1.8rem;">Create An Account</h2>
                <p style="font-size: 0.9rem;">Register today for special member offers and seamless checkout.</p>
            </div>

            <form action="<?= BASE_URL ?>actions/register.php" method="POST">
                <?= csrfField() ?>

                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" placeholder="e.g. Ayesha Khan" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. ayesha@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" placeholder="e.g. 0300 1234567" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password * (Min. 6 characters)</label>
                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Choose a secure password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6" placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                    Create My Account
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; border-top: 1px solid #ede4e0; padding-top: 18px;">
                Already have an account? <a href="<?= BASE_URL ?>login.php" style="color: #b37d7a; font-weight: 600;">Sign In Here</a>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
