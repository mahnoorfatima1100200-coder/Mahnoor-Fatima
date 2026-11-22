<?php
/**
 * Skinora - Admin Sign Out
 */
require_once __DIR__ . '/../config/config.php';

unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_role']);
session_regenerate_id(true);

setFlash('success', 'Admin logged out successfully.');
redirect('admin/index.php');
