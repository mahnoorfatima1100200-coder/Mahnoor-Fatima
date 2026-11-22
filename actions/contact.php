<?php
/**
 * Skinora - Action: Contact Message
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact.php');
}

validateCsrfOrDie();

$name = cleanInput($_POST['name'] ?? '');
$email = cleanInput($_POST['email'] ?? '');
$phone = cleanInput($_POST['phone'] ?? '');
$subject = cleanInput($_POST['subject'] ?? '');
$message = cleanInput($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    setFlash('error', 'Please fill in all required fields.');
    redirect('contact.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    redirect('contact.php');
}

$db = getDBConnection();
$stmt = $db->prepare("
    INSERT INTO contact_messages (name, email, phone, subject, message, is_read, created_at)
    VALUES (?, ?, ?, ?, ?, 0, NOW())
");
$stmt->execute([$name, $email, $phone, $subject, $message]);

setFlash('success', 'Thank you for reaching out to Skinora! Our skincare consultants will get back to you shortly.');
redirect('contact.php');
