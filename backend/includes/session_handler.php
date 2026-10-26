<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return isset($_SESSION['u_id']);
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin(string $redirect = '/php/web/Soba-Project/frontend/modules/customer/login.php'): void {
        if (!isLoggedIn()) {
            header("Location: $redirect");
            exit;
        }
    }
}

if (!function_exists('requireRole')) {
    function requireRole(string $role): void {
        if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== $role) {
            http_response_code(403);
            exit('Access denied');
        }
    }
}