<?php
/**
 * Skinora - Action: Remove Item from Cart
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

validateCsrfOrDie();

$itemId = isset($_POST['item_id']) && is_numeric($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$db = getDBConnection();
$userId = $_SESSION['user_id'] ?? null;
$cartId = getOrCreateCartId($db, $userId);

$del = $db->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
$del->execute([$itemId, $cartId]);

setFlash('info', 'Item removed from your shopping bag.');
redirect('cart.php');
