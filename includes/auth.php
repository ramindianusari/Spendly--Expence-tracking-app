<?php
/**
 * Auth helper — shared session / redirect utilities
 */

session_start();

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Return true if a user is currently logged in.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Require the user to be logged in; redirect to login if not.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}

/**
 * Return the session user array (id, name, email).
 */
function currentUser(): array {
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'name'  => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
    ];
}

/**
 * Sanitise output for safe HTML display.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
