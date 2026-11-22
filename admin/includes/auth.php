<?php
/**
 * Skinora - Admin Authentication Guard
 */
require_once __DIR__ . '/../../config/config.php';

if (!isAdmin()) {
    setFlash('error', 'Administrator access required. Please sign in with an admin account.');
    redirect('admin/index.php');
}
