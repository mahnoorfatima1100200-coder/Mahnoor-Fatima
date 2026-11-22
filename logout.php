<?php
/**
 * Skinora - Sign Out Handler
 */
require_once __DIR__ . '/config/config.php';

// Unset user specific session data
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_role']);

// Regenerate session id for security
session_regenerate_id(true);

setFlash('success', 'You have been successfully signed out.');
redirect('index.php');
