<?php
/**
 * Skinora - CSRF Protection Helpers
 */

/**
 * Generate and return CSRF token
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF token input tag
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify CSRF token from POST or GET request
 */
function verifyCsrfToken(?string $token = null): bool {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verify CSRF or exit with error
 */
function validateCsrfOrDie(): void {
    if (!verifyCsrfToken()) {
        http_response_code(403);
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Please refresh the page.']);
            exit;
        }
        die("Security validation failed (CSRF token mismatch). Please go back and try again.");
    }
}
