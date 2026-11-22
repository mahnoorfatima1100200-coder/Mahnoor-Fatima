<?php
/**
 * Skinora - Action: Apply Discount Coupon
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

validateCsrfOrDie();

$couponCode = cleanInput($_POST['coupon_code'] ?? '');
if (empty($couponCode)) {
    unset($_SESSION['applied_coupon']);
    setFlash('info', 'Coupon removed.');
    redirect('cart.php');
}

$couponCode = strtoupper($couponCode);
$db = getDBConnection();

$stmt = $db->prepare("
    SELECT * FROM coupons 
    WHERE code = ? AND status = 'active' 
    AND (expiry_date IS NULL OR expiry_date >= CURDATE())
    LIMIT 1
");
$stmt->execute([$couponCode]);
$coupon = $stmt->fetch();

if (!$coupon) {
    unset($_SESSION['applied_coupon']);
    setFlash('error', 'Invalid or expired coupon code.');
    redirect('cart.php');
}

// Check usage limit
if (!empty($coupon['usage_limit']) && $coupon['usage_count'] >= $coupon['usage_limit']) {
    unset($_SESSION['applied_coupon']);
    setFlash('error', 'This coupon has reached its maximum usage limit.');
    redirect('cart.php');
}

// Check min spend against cart subtotal
$cartDetails = getCartDetails($db, $_SESSION['user_id'] ?? null);
if ($cartDetails['subtotal'] < $coupon['min_spend']) {
    setFlash('error', 'This coupon requires a minimum spend of ' . formatPrice($coupon['min_spend']));
    redirect('cart.php');
}

// Save in session
$_SESSION['applied_coupon'] = [
    'id' => $coupon['id'],
    'code' => $coupon['code'],
    'discount_type' => $coupon['discount_type'],
    'discount_value' => (float)$coupon['discount_value'],
    'min_spend' => (float)$coupon['min_spend'],
    'max_discount' => !empty($coupon['max_discount']) ? (float)$coupon['max_discount'] : null
];

setFlash('success', "Coupon '{$couponCode}' applied successfully!");
redirect('cart.php');
