<?php
/**
 * Skinora - Action: Remove Item from Wishlist
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('wishlist.php');
}

validateCsrfOrDie();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    redirect('login.php');
}

$productId = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$db = getDBConnection();

$del = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$del->execute([$userId, $productId]);

setFlash('info', 'Item removed from your wishlist.');
redirect('wishlist.php');
