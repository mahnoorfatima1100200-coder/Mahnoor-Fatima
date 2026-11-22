<?php
/**
 * Skinora - Admin Sidebar Navigation
 */
$currentAdminScript = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <h2>Skinora<span>.</span></h2>
        <small>E-Commerce Admin Panel</small>
    </div>

    <ul class="admin-menu">
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/dashboard.php" class="admin-menu-link <?= $currentAdminScript === 'dashboard.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">📊</span>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/orders.php" class="admin-menu-link <?= in_array($currentAdminScript, ['orders.php', 'order-details.php']) ? 'active' : '' ?>">
                <span class="admin-menu-icon">📦</span>
                <span>Orders</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/products.php" class="admin-menu-link <?= in_array($currentAdminScript, ['products.php', 'add-product.php', 'edit-product.php']) ? 'active' : '' ?>">
                <span class="admin-menu-icon">🧴</span>
                <span>Products</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/categories.php" class="admin-menu-link <?= $currentAdminScript === 'categories.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">🏷️</span>
                <span>Categories</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/customers.php" class="admin-menu-link <?= $currentAdminScript === 'customers.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">👥</span>
                <span>Customers</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/reviews.php" class="admin-menu-link <?= $currentAdminScript === 'reviews.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">⭐</span>
                <span>Reviews</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/coupons.php" class="admin-menu-link <?= $currentAdminScript === 'coupons.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">🎟️</span>
                <span>Discount Coupons</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/reports.php" class="admin-menu-link <?= $currentAdminScript === 'reports.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">📈</span>
                <span>Reports</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/messages.php" class="admin-menu-link <?= $currentAdminScript === 'messages.php' ? 'active' : '' ?>">
                <span class="admin-menu-icon">✉️</span>
                <span>Messages</span>
            </a>
        </li>

        <li style="border-top: 1px solid rgba(255,255,255,0.08); margin: 15px 0;"></li>

        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>index.php" target="_blank" class="admin-menu-link">
                <span class="admin-menu-icon">🌐</span>
                <span>View Live Store &rarr;</span>
            </a>
        </li>
        <li class="admin-menu-item">
            <a href="<?= BASE_URL ?>admin/logout.php" class="admin-menu-link" style="color: #ff8a80;">
                <span class="admin-menu-icon">🚪</span>
                <span>Sign Out</span>
            </a>
        </li>
    </ul>
</aside>
