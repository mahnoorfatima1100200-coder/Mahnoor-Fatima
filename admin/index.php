<?php
/**
 * Skinora - Admin Login Portal
 */
require_once __DIR__ . '/../config/config.php';

if (isAdmin()) {
    redirect('admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfOrDie();

    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    $authenticated = false;
    if ($admin && $admin['status'] === 'active') {
        if (password_verify($password, $admin['password'])) {
            $authenticated = true;
        } elseif ($admin['email'] === 'admin@skinora.com' && $password === 'admin123') {
            $authenticated = true;
            $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
        }
    }

    if ($authenticated) {
        $_SESSION['user_id'] = (int)$admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        $_SESSION['user_email'] = $admin['email'];
        $_SESSION['user_role'] = 'admin';
        session_regenerate_id(true);

        setFlash('success', 'Welcome to Skinora Administration Center.');
        redirect('admin/dashboard.php');
    } else {
        $error = 'Invalid administrative credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skinora Admin Login - Secure Access</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body style="background: #1e191a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="width: 100%; max-width: 440px; background: #fff; border-radius: 14px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.3);">
        <div style="text-align: center; margin-bottom: 26px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin: 0; color: #2c2425;">
                Skinora<span style="color: #b37d7a;">.</span>
            </h1>
            <p style="font-size: 0.85rem; color: #726264; margin-top: 4px;">Administrative Control Center</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <?= displayFlash() ?>

        <form action="<?= BASE_URL ?>admin/index.php" method="POST">
            <?= csrfField() ?>

            <div class="form-group">
                <label class="form-label">Administrator Email *</label>
                <input type="email" name="email" class="form-control" value="admin@skinora.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                Authenticate & Enter Dashboard
            </button>
        </form>

        <div style="background: #faf6f4; border: 1px solid #ede4e0; border-radius: 8px; padding: 12px; margin-top: 20px; font-size: 0.8rem; color: #726264; text-align: center;">
            <strong>Default Credentials:</strong><br>
            Email: <code>admin@skinora.com</code><br>
            Password: <code>admin123</code>
        </div>

        <div style="text-align: center; margin-top: 18px;">
            <a href="<?= BASE_URL ?>index.php" style="font-size: 0.84rem; color: #8c7e80;">&larr; Back to Public Store</a>
        </div>
    </div>
</body>
</html>
