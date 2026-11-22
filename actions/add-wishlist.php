<?php
/**
 * Skinora - Action: Add / Toggle Wishlist Item
 */
require_once __DIR__ . '/../config/config.php';

validateCsrfOrDie();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in to add items to your wishlist.']);
        exit;
    }
    setFlash('warning', 'Please log in to manage your wishlist.');
    redirect('login.php');
}

$productId = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$db = getDBConnection();

// Check if item exists in wishlist
$check = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ? LIMIT 1");
$check->execute([$userId, $productId]);
$exists = $check->fetch();

$inWishlist = false;
if ($exists) {
    $del = $db->prepare("DELETE FROM wishlist WHERE id = ?");
    $del->execute([$exists['id']]);
    $msg = 'Removed from your wishlist.';
    $inWishlist = false;
} else {
    $ins = $db->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
    $ins->execute([$userId, $productId]);
    $msg = 'Added to your wishlist!';
    $inWishlist = true;
}

$count = getWishlistCount($db, $userId);

if (isAjaxRequest()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $msg,
        'in_wishlist' => $inWishlist,
        'wishlist_count' => $count
    ]);
    exit;
}

setFlash('success', $msg);
redirect('wishlist.php');
