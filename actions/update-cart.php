<?php
/**
 * Skinora - Action: Update Cart Item Quantity
 */
require_once __DIR__ . '/../config/config.php';

validateCsrfOrDie();

$itemId = isset($_POST['item_id']) && is_numeric($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

$db = getDBConnection();
$userId = $_SESSION['user_id'] ?? null;
$cartId = getOrCreateCartId($db, $userId);

if ($quantity <= 0) {
    $del = $db->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
    $del->execute([$itemId, $cartId]);
} else {
    // Check product stock limit
    $check = $db->prepare("
        SELECT p.stock_quantity 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.id = ? AND ci.cart_id = ?
    ");
    $check->execute([$itemId, $cartId]);
    $stock = $check->fetchColumn();

    if ($stock !== false && $quantity > $stock) {
        $quantity = (int)$stock;
    }

    $update = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?");
    $update->execute([$quantity, $itemId, $cartId]);
}

if (isAjaxRequest()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Cart updated.']);
    exit;
}

redirect('cart.php');
