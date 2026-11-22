<?php
/**
 * Skinora - Action: Add Product to Cart
 */
require_once __DIR__ . '/../config/config.php';

validateCsrfOrDie();

$productId = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

$db = getDBConnection();

// Check product validity & stock
$stmt = $db->prepare("SELECT id, name, price, sale_price, stock_quantity, status FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product || $product['status'] !== 'active') {
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product is not available.']);
        exit;
    }
    setFlash('error', 'Product not available.');
    redirect('shop.php');
}

if ($product['stock_quantity'] < 1) {
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Sorry, this product is currently out of stock.']);
        exit;
    }
    setFlash('error', 'Sorry, this item is out of stock.');
    redirect('product.php?slug=' . urlencode($product['slug']));
}

$userId = $_SESSION['user_id'] ?? null;
$cartId = getOrCreateCartId($db, $userId);

// Effective price
$price = ($product['sale_price'] && $product['sale_price'] > 0) ? (float)$product['sale_price'] : (float)$product['price'];

// Check if item already exists in cart
$checkStmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1");
$checkStmt->execute([$cartId, $productId]);
$existing = $checkStmt->fetch();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    if ($newQty > $product['stock_quantity']) {
        $newQty = $product['stock_quantity'];
    }
    $updateStmt = $db->prepare("UPDATE cart_items SET quantity = ?, price = ? WHERE id = ?");
    $updateStmt->execute([$newQty, $price, $existing['id']]);
} else {
    if ($quantity > $product['stock_quantity']) {
        $quantity = $product['stock_quantity'];
    }
    $insertStmt = $db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, price, created_at) VALUES (?, ?, ?, ?, NOW())");
    $insertStmt->execute([$cartId, $productId, $quantity, $price]);
}

$cartCount = getCartCount($db, $userId);

if (isAjaxRequest()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => "Added {$product['name']} to your bag!",
        'cart_count' => $cartCount
    ]);
    exit;
}

setFlash('success', "Added {$product['name']} to your shopping bag.");
redirect('cart.php');
