<?php
/**
 * Skinora - Customer Sign In
 */
require_once __DIR__ . '/config/config.php';
requireGuest();

$pageTitle = 'Sign In to Your Account';
$redirect = cleanInput($_GET['redirect'] ?? 'my-orders.php');

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">Sign In</span>
    </div>
</div>

<div class="container" style="padding: 60px 20px;">
    <div style="max-width: 460px; margin: 0 auto;">
        <?= displayFlash() ?>

        <div class="form-card">
            <div style="text-align: center; margin-bottom: 28px;">
                <span class="section-subtitle">Welcome Back</span>
                <h2 style="font-size: 1.8rem;">Sign In to Skinora</h2>
                <p style="font-size: 0.9rem;">Access your orders, track shipments, and manage wishlists.</p>
            </div>

            <form action="<?= BASE_URL ?>actions/login.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin: 0;">Password *</label>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                    Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; border-top: 1px solid #ede4e0; padding-top: 18px;">
                Don't have an account? <a href="<?= BASE_URL ?>register.php" style="color: #b37d7a; font-weight: 600;">Create One Here</a>
            </div>

            <div style="margin-top: 16px; background: #faf6f4; border-radius: 8px; padding: 12px; font-size: 0.8rem; color: #726264; text-align: center;">
                <strong>Demo Customer:</strong> ayesha@example.com / customer123<br>
                <strong>Admin Login:</strong> <a href="<?= BASE_URL ?>admin/index.php" style="color: #b37d7a;">Click here for Admin Portal</a>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
