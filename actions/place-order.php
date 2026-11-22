<?php
/**
 * Skinora - Action: Place Order (PDO Transaction & Stock Deduction)
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout.php');
}

validateCsrfOrDie();

$db = getDBConnection();
$userId = $_SESSION['user_id'] ?? null;
$cartDetails = getCartDetails($db, $userId);

if (empty($cartDetails['items'])) {
    setFlash('error', 'Your shopping bag is empty.');
    redirect('shop.php');
}

$customerName = cleanInput($_POST['customer_name'] ?? '');
$customerPhone = cleanInput($_POST['customer_phone'] ?? '');
$customerEmail = cleanInput($_POST['customer_email'] ?? '');
$shippingAddress = cleanInput($_POST['shipping_address'] ?? '');
$city = cleanInput($_POST['city'] ?? '');
$postalCode = cleanInput($_POST['postal_code'] ?? '');
$notes = cleanInput($_POST['notes'] ?? '');
$paymentMethod = cleanInput($_POST['payment_method'] ?? 'cod');

if (empty($customerName) || empty($customerPhone) || empty($customerEmail) || empty($shippingAddress) || empty($city)) {
    setFlash('error', 'Please fill in all required delivery information.');
    redirect('checkout.php');
}

if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    redirect('checkout.php');
}

try {
    // Begin safe database transaction
    $db->beginTransaction();

    // 1. Verify stock for each cart item
    foreach ($cartDetails['items'] as $item) {
        $stkStmt = $db->prepare("SELECT stock_quantity, name FROM products WHERE id = ? FOR UPDATE");
        $stkStmt->execute([$item['product_id']]);
        $prodRow = $stkStmt->fetch();

        if (!$prodRow || $prodRow['stock_quantity'] < $item['quantity']) {
            throw new Exception("Product '{$item['name']}' only has {$prodRow['stock_quantity']} units remaining. Please adjust your bag.");
        }
    }

    // 2. Generate unique order number
    $orderNumber = generateOrderNumber();

    // Check collision just in case
    $numCheck = $db->prepare("SELECT id FROM orders WHERE order_number = ? LIMIT 1");
    $numCheck->execute([$orderNumber]);
    if ($numCheck->fetch()) {
        $orderNumber .= 'A';
    }

    $couponCode = $cartDetails['coupon']['code'] ?? null;

    // 3. Insert into orders table
    $orderInsert = $db->prepare("
        INSERT INTO orders (
            order_number, user_id, customer_name, customer_email, customer_phone,
            shipping_address, city, postal_code, subtotal, shipping_fee, discount_amount,
            total_amount, payment_method, payment_status, order_status, notes, coupon_code,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, 'pending', 'pending', ?, ?,
            NOW(), NOW()
        )
    ");
    $orderInsert->execute([
        $orderNumber,
        $userId,
        $customerName,
        $customerEmail,
        $customerPhone,
        $shippingAddress,
        $city,
        $postalCode,
        $cartDetails['subtotal'],
        $cartDetails['shipping_fee'],
        $cartDetails['discount_amount'],
        $cartDetails['final_total'],
        $paymentMethod,
        $notes,
        $couponCode
    ]);
    $newOrderId = (int)$db->lastInsertId();

    // 4. Insert order items & deduct product stock
    $itemInsert = $db->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stockUpdate = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

    foreach ($cartDetails['items'] as $item) {
        $itemInsert->execute([
            $newOrderId,
            $item['product_id'],
            $item['name'],
            $item['effective_price'],
            $item['quantity'],
            $item['line_total']
        ]);

        $stockUpdate->execute([$item['quantity'], $item['product_id']]);
    }

    // 5. Update coupon usage count if coupon applied
    if ($couponCode && !empty($cartDetails['coupon']['id'])) {
        $db->prepare("UPDATE coupons SET usage_count = usage_count + 1 WHERE id = ?")
           ->execute([$cartDetails['coupon']['id']]);
    }

    // 6. Save address for logged in customer if not already saved
    if ($userId) {
        $chkAddr = $db->prepare("SELECT id FROM addresses WHERE user_id = ? AND address_line1 = ? LIMIT 1");
        $chkAddr->execute([$userId, $shippingAddress]);
        if (!$chkAddr->fetch()) {
            $insAddr = $db->prepare("
                INSERT INTO addresses (user_id, full_name, phone, address_line1, city, postal_code, is_default, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
            ");
            $insAddr->execute([$userId, $customerName, $customerPhone, $shippingAddress, $city, $postalCode]);
        }
    }

    // 7. Clear the cart items
    $db->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cartDetails['cart_id']]);
    unset($_SESSION['applied_coupon']);

    // Commit transaction
    $db->commit();

    // Redirect to Order Confirmation page
    redirect('order-confirmation.php?order=' . urlencode($orderNumber));

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setFlash('error', $e->getMessage());
    redirect('checkout.php');
}
