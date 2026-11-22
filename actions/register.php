<?php
/**
 * Skinora - Action: Register Customer
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

validateCsrfOrDie();

$fullName = cleanInput($_POST['full_name'] ?? '');
$email = cleanInput($_POST['email'] ?? '');
$phone = cleanInput($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Basic validations
if (empty($fullName) || empty($email) || empty($password)) {
    setFlash('error', 'Please fill in all required fields.');
    redirect('register.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    redirect('register.php');
}

if (strlen($password) < 6) {
    setFlash('error', 'Password must be at least 6 characters long.');
    redirect('register.php');
}

if ($password !== $confirmPassword) {
    setFlash('error', 'Passwords do not match. Please re-enter.');
    redirect('register.php');
}

$db = getDBConnection();

// Check duplicate email
$stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    setFlash('error', 'An account with this email address already exists. Please log in.');
    redirect('login.php');
}

// Hash password securely with bcrypt
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insert = $db->prepare("
    INSERT INTO users (full_name, email, password, phone, role, status, created_at) 
    VALUES (?, ?, ?, ?, 'customer', 'active', NOW())
");
$insert->execute([$fullName, $email, $hashedPassword, $phone]);
$newUserId = (int)$db->lastInsertId();

// Automatically sign in the customer
$_SESSION['user_id'] = $newUserId;
$_SESSION['user_name'] = $fullName;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = 'customer';
session_regenerate_id(true);

// Associate existing guest cart if any
getOrCreateCartId($db, $newUserId);

setFlash('success', "Welcome to Skinora, {$fullName}! Your account has been created.");
redirect('shop.php');
