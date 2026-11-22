<?php
/**
 * Skinora - Admin Global Header
 */
require_once __DIR__ . '/auth.php';

$adminPageTitle = $adminPageTitle ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminPageTitle) ?> - Skinora Control Center</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="admin-body">
<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
        <h2 class="admin-topbar-title"><?= e($adminPageTitle) ?></h2>
        <div class="admin-user-info">
            <div class="admin-avatar">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <strong style="font-size: 0.9rem;"><?= e($_SESSION['user_name'] ?? 'Administrator') ?></strong>
                <span style="display:block; font-size: 0.75rem; color: #6c757d;">Store Admin</span>
            </div>
            <a href="<?= BASE_URL ?>admin/logout.php" class="btn-admin btn-admin-outline" style="margin-left: 14px;">Sign Out</a>
        </div>
    </header>

    <div class="admin-content">
        <?= displayFlash() ?>
