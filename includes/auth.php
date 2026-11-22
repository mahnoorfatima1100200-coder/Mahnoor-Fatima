<?php
/**
 * Skinora - Authentication & Role Helpers
 */

/**
 * Check if a customer or user is logged in
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if currently logged in user is an admin
 */
function isAdmin(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get currently logged in user data from session or database
 */
function currentUser(?PDO $db = null): ?array {
    if (!isLoggedIn()) {
        return null;
    }

    if ($db) {
        $stmt = $db->prepare("SELECT id, full_name, email, phone, role, status FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'full_name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'customer'
    ];
}

/**
 * Require customer login; redirect to login page if unauthenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to access this page.');
        $redirectUrl = urlencode($_SERVER['REQUEST_URI']);
        redirect('login.php?redirect=' . $redirectUrl);
    }
}

/**
 * Require administrator privilege
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        setFlash('error', 'Administrator access required.');
        redirect('admin/index.php');
    }
}

/**
 * Require guest (redirect logged-in users away from login/register)
 */
function requireGuest(): void {
    if (isLoggedIn()) {
        if (isAdmin()) {
            redirect('admin/dashboard.php');
        } else {
            redirect('my-orders.php');
        }
    }
}
