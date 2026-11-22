<?php
/**
 * Skinora - Action: Submit Product Review
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('shop.php');
}

validateCsrfOrDie();

$productId = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$reviewerName = cleanInput($_POST['reviewer_name'] ?? '');
$rating = isset($_POST['rating']) && is_numeric($_POST['rating']) ? max(1, min(5, (int)$_POST['rating'])) : 5;
$reviewText = cleanInput($_POST['review_text'] ?? '');

if (empty($productId) || empty($reviewerName) || empty($reviewText)) {
    setFlash('error', 'Please provide your name, rating, and review text.');
    redirect('shop.php');
}

$db = getDBConnection();

// Verify product
$prodStmt = $db->prepare("SELECT slug FROM products WHERE id = ? LIMIT 1");
$prodStmt->execute([$productId]);
$product = $prodStmt->fetch();

if (!$product) {
    redirect('shop.php');
}

$userId = $_SESSION['user_id'] ?? null;

$insert = $db->prepare("
    INSERT INTO reviews (product_id, user_id, reviewer_name, rating, review_text, status, created_at)
    VALUES (?, ?, ?, ?, ?, 'approved', NOW())
");
$insert->execute([$productId, $userId, $reviewerName, $rating, $reviewText]);

setFlash('success', 'Thank you! Your verified review has been posted.');
redirect('product.php?slug=' . urlencode($product['slug']));
