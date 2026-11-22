<?php
/**
 * Skinora - Action: Newsletter Subscription
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

validateCsrfOrDie();

$email = cleanInput($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    redirect('index.php');
}

$db = getDBConnection();

$stmt = $db->prepare("SELECT id FROM newsletter_subscribers WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    setFlash('info', 'You are already subscribed to the Skinora Glow Club!');
    redirect('index.php');
}

$insert = $db->prepare("INSERT INTO newsletter_subscribers (email, status, subscribed_at) VALUES (?, 'active', NOW())");
$insert->execute([$email]);

setFlash('success', 'Welcome to the Skinora Glow Club! Check your inbox for your 10% discount code: GLOW10');
redirect('index.php');
