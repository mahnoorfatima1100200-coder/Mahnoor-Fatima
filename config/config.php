<?php
/**
 * Skinora - Application Configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Secure session cookie settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Set default timezone
date_default_timezone_set('Asia/Karachi');

// Brand details
define('SITE_NAME', 'Skinora');
define('SITE_TAGLINE', 'Discover Your Natural Glow');
define('SITE_CURRENCY', 'PKR');
define('SITE_CURRENCY_SYMBOL', 'Rs. ');
define('SUPPORT_EMAIL', 'support@skinora.pk');
define('SUPPORT_PHONE', '+92 300 1234567');
define('STORE_ADDRESS', 'Skinora Beauty House, Gulberg III, Lahore, Pakistan');

// Business rules
define('FREE_SHIPPING_THRESHOLD', 3000); // Free shipping on orders above PKR 3,000
define('DEFAULT_SHIPPING_FEE', 200);      // Flat shipping PKR 200

// Automatic Base URL Detection (works on localhost, subfolder, built-in server or free hosting domain)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));
$rootSubdir = preg_replace('#/(admin|actions|includes|config).*$#', '', $scriptDir);
$rootSubdir = rtrim($rootSubdir, '/\\.');

if (empty($rootSubdir) || $rootSubdir === '/') {
    define('BASE_URL', $protocol . $host . '/');
} else {
    define('BASE_URL', $protocol . $host . $rootSubdir . '/');
}
define('ROOT_PATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
define('UPLOAD_DIR', ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR);

// Include database and helper functions
require_once __DIR__ . '/database.php';
require_once ROOT_PATH . 'includes/functions.php';
require_once ROOT_PATH . 'includes/csrf.php';
require_once ROOT_PATH . 'includes/auth.php';
