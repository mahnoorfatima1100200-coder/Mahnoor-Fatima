<?php
/**
 * Skinora - Customer Profile & Saved Addresses
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

$pageTitle = 'My Profile & Account Settings';
$db = getDBConnection();
$userId = (int)$_SESSION['user_id'];

// Fetch user profile
$userStmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

// Handle profile update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    validateCsrfOrDie();

    $fullName = cleanInput($_POST['full_name'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($fullName)) {
        setFlash('error', 'Full Name is required.');
    } else {
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                setFlash('error', 'Password must be at least 6 characters.');
                redirect('profile.php');
            }
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
            $stmt->execute([$fullName, $phone, $hash, $userId]);
        } else {
            $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$fullName, $phone, $userId]);
        }

        $_SESSION['user_name'] = $fullName;
        setFlash('success', 'Profile updated successfully.');
        redirect('profile.php');
    }
}

// Fetch saved addresses
$addrStmt = $db->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");
$addrStmt->execute([$userId]);
$addresses = $addrStmt->fetchAll();

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">My Profile</span>
    </div>
</div>

<div class="container" style="padding: 50px 20px;">
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 36px;">
        <!-- Account Sidebar -->
        <div class="shop-sidebar">
            <div style="text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ede4e0;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #fceee9; color: #b37d7a; font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                </div>
                <h4 style="margin-bottom: 4px;"><?= e($user['full_name']) ?></h4>
                <span style="font-size: 0.8rem; color: #726264;"><?= e($user['email']) ?></span>
            </div>
            <ul class="category-filter-list">
                <li><a href="<?= BASE_URL ?>my-orders.php"><span>📦 My Orders</span></a></li>
                <li><a href="<?= BASE_URL ?>profile.php" class="active"><span>👤 Account Profile</span></a></li>
                <li><a href="<?= BASE_URL ?>wishlist.php"><span>♥ Saved Wishlist</span></a></li>
                <li><a href="<?= BASE_URL ?>logout.php" style="color: #d32f2f;"><span>🚪 Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Profile Settings Form -->
        <div>
            <?= displayFlash() ?>

            <div class="form-card" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px; font-size: 1.4rem;">Personal Information</h3>
                <form action="<?= BASE_URL ?>profile.php" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="update_profile" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled style="background: #faf6f4;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Change Password (Leave blank to keep current)</label>
                            <input type="password" name="new_password" class="form-control" minlength="6" placeholder="New password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Profile Changes</button>
                </form>
            </div>

            <!-- Saved Delivery Addresses -->
            <div class="form-card">
                <h3 style="margin-bottom: 20px; font-size: 1.4rem;">Saved Shipping Addresses</h3>
                <?php if (empty($addresses)): ?>
                    <p>No saved addresses yet. Addresses are saved automatically during checkout.</p>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <?php foreach ($addresses as $addr): ?>
                            <div style="border: 1px solid #ede4e0; border-radius: 8px; padding: 18px; background: #fffdfa;">
                                <strong><?= e($addr['full_name']) ?></strong>
                                <?php if ($addr['is_default']): ?>
                                    <span style="font-size: 0.7rem; background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 10px; margin-left: 6px;">Default</span>
                                <?php endif; ?>
                                <p style="font-size: 0.88rem; margin: 8px 0;">
                                    <?= e($addr['address_line1']) ?><br>
                                    <?= e($addr['city']) ?><?= !empty($addr['postal_code']) ? ' - ' . e($addr['postal_code']) : '' ?>
                                </p>
                                <span style="font-size: 0.84rem; color: #726264;">📞 <?= e($addr['phone']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
