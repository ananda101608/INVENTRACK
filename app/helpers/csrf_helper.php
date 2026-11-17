<?php
// ====================================================================
// HELPER PROTEKSI CSRF (Cross-Site Request Forgery)
// ====================================================================

// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Menghasilkan token CSRF unik jika belum ada di session
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Menghasilkan input hidden HTML berisi token CSRF untuk formulir
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Memvalidasi token CSRF yang dikirim dari form
 */
function verify_csrf(?string $token = null): bool {
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
