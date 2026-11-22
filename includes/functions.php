<?php
/**
 * Skinora - Helper Functions
 */

/**
 * Escape output for safe HTML display (XSS prevention)
 */
function e(?string $string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price in Pakistani Rupees
 */
function formatPrice($amount): string {
    return SITE_CURRENCY_SYMBOL . number_format((float)$amount, 0);
}

/**
 * Clean & sanitize user input string
 */
function cleanInput(?string $data): string {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Redirect to a specific URL
 */
function redirect(string $path): void {
    if (!filter_var($path, FILTER_VALIDATE_URL)) {
        $path = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
    header("Location: " . $path);
    exit;
}

/**
 * Check if current request is AJAX
 */
function isAjaxRequest(): bool {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

/**
 * Flash message helper: set message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Flash message helper: get and clear message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message as HTML alert
 */
function displayFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    
    $typeClass = $flash['type'] === 'success' ? 'alert-success' : ($flash['type'] === 'error' ? 'alert-error' : 'alert-info');
    $icon = $flash['type'] === 'success' ? '✓' : ($flash['type'] === 'error' ? '✕' : 'ℹ');
    
    return '<div class="alert ' . $typeClass . '">
        <span class="alert-icon">' . $icon . '</span>
        <span class="alert-text">' . e($flash['message']) . '</span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>';
}

/**
 * Generate a unique, professional order number
 * Example: SKN-2026-8941
 */
function generateOrderNumber(): string {
    $year = date('Y');
    $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    return "SKN-{$year}-{$rand}";
}

/**
 * Create URL slug from string
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Get Cart Session ID (for guests or logged-in users)
 */
function getCartSessionId(): string {
    if (empty($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['cart_session_id'];
}

/**
 * Get active Cart ID from database
 */
function getOrCreateCartId(PDO $db, ?int $userId = null): int {
    $sessionId = getCartSessionId();

    if ($userId) {
        // Look up by user_id
        $stmt = $db->prepare("SELECT id FROM cart WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $cart = $stmt->fetch();
        if ($cart) {
            return (int)$cart['id'];
        }
        
        // If guest cart exists for this session, associate it with user
        $stmt = $db->prepare("SELECT id FROM cart WHERE session_id = ? AND user_id IS NULL LIMIT 1");
        $stmt->execute([$sessionId]);
        $guestCart = $stmt->fetch();
        if ($guestCart) {
            $update = $db->prepare("UPDATE cart SET user_id = ? WHERE id = ?");
            $update->execute([$userId, $guestCart['id']]);
            return (int)$guestCart['id'];
        }

        // Create new cart for user
        $stmt = $db->prepare("INSERT INTO cart (user_id, session_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $sessionId]);
        return (int)$db->lastInsertId();
    } else {
        // Look up guest cart by session_id
        $stmt = $db->prepare("SELECT id FROM cart WHERE session_id = ? AND user_id IS NULL LIMIT 1");
        $stmt->execute([$sessionId]);
        $cart = $stmt->fetch();
        if ($cart) {
            return (int)$cart['id'];
        }

        // Create new guest cart
        $stmt = $db->prepare("INSERT INTO cart (user_id, session_id, created_at) VALUES (NULL, ?, NOW())");
        $stmt->execute([$sessionId]);
        return (int)$db->lastInsertId();
    }
}

/**
 * Get Cart items and totals
 */
function getCartDetails(PDO $db, ?int $userId = null): array {
    $cartId = getOrCreateCartId($db, $userId);

    $stmt = $db->prepare("
        SELECT ci.id AS item_id, ci.quantity, ci.price AS item_price,
               p.id AS product_id, p.name, p.slug, p.price, p.sale_price, p.stock_quantity, p.main_image,
               c.name AS category_name
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE ci.cart_id = ?
        ORDER BY ci.id DESC
    ");
    $stmt->execute([$cartId]);
    $items = $stmt->fetchAll();

    $subtotal = 0;
    $totalCount = 0;

    foreach ($items as &$item) {
        $effectivePrice = ($item['sale_price'] && $item['sale_price'] > 0) ? (float)$item['sale_price'] : (float)$item['price'];
        $item['effective_price'] = $effectivePrice;
        $item['line_total'] = $effectivePrice * (int)$item['quantity'];
        $subtotal += $item['line_total'];
        $totalCount += (int)$item['quantity'];
    }

    // Shipping calculation
    $shippingFee = ($subtotal >= FREE_SHIPPING_THRESHOLD || $subtotal == 0) ? 0 : DEFAULT_SHIPPING_FEE;
    
    // Coupon discount calculation
    $discountAmount = 0;
    $couponCode = $_SESSION['applied_coupon']['code'] ?? null;
    if ($couponCode && !empty($_SESSION['applied_coupon'])) {
        $coupon = $_SESSION['applied_coupon'];
        if ($subtotal >= ($coupon['min_spend'] ?? 0)) {
            if ($coupon['discount_type'] === 'percent') {
                $discountAmount = ($subtotal * (float)$coupon['discount_value']) / 100;
                if (!empty($coupon['max_discount']) && $discountAmount > $coupon['max_discount']) {
                    $discountAmount = (float)$coupon['max_discount'];
                }
            } else {
                $discountAmount = (float)$coupon['discount_value'];
            }
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        } else {
            // Min spend not met anymore
            unset($_SESSION['applied_coupon']);
        }
    }

    $finalTotal = max(0, ($subtotal - $discountAmount) + $shippingFee);

    return [
        'cart_id' => $cartId,
        'items' => $items,
        'item_count' => $totalCount,
        'subtotal' => $subtotal,
        'shipping_fee' => $shippingFee,
        'discount_amount' => $discountAmount,
        'coupon' => $_SESSION['applied_coupon'] ?? null,
        'final_total' => $finalTotal
    ];
}

/**
 * Get total quantity in cart
 */
function getCartCount(PDO $db, ?int $userId = null): int {
    $cartDetails = getCartDetails($db, $userId);
    return $cartDetails['item_count'];
}

/**
 * Get customer wishlist count
 */
function getWishlistCount(PDO $db, ?int $userId = null): int {
    if (!$userId) return 0;
    $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

/**
 * Star Rating Display HTML helper
 */
function renderRatingStars(float $rating, int $reviewCount = 0): string {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - ($full + $half);

    $html = '<div class="star-rating" title="' . number_format($rating, 1) . ' out of 5 stars">';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<span class="star star-filled">★</span>';
    }
    if ($half) {
        $html .= '<span class="star star-half">★</span>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<span class="star star-empty">☆</span>';
    }
    if ($reviewCount > 0) {
        $html .= '<span class="review-count">(' . $reviewCount . ')</span>';
    }
    $html .= '</div>';
    return $html;
}
