<?php
/**
 * Skinora - Admin Delete Product
 */
require_once __DIR__ . '/includes/auth.php';

validateCsrfOrDie();

$productId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$db = getDBConnection();

$stmt = $db->prepare("SELECT id, name FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$productId]);
$prod = $stmt->fetch();

if ($prod) {
    $del = $db->prepare("DELETE FROM products WHERE id = ?");
    $del->execute([$productId]);
    setFlash('info', "Product '{$prod['name']}' has been removed.");
}

redirect('admin/products.php');
