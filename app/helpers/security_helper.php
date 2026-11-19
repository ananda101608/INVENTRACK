<?php
// ====================================================================
// HELPER KEAMANAN: HEADER HTTP & CSP NONCE
// ====================================================================

/**
 * Nonce untuk mengizinkan blok <script> inline tertentu lewat CSP.
 */
function csp_nonce(): string {
    if (empty($GLOBALS['csp_nonce'])) {
        $GLOBALS['csp_nonce'] = base64_encode(random_bytes(16));
    }
    return $GLOBALS['csp_nonce'];
}

/**
 * Kirim header keamanan standar. Aman dipanggil berkali-kali.
 */
function kirim_header_keamanan(): void {
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    $nonce = csp_nonce();
    header(
        "Content-Security-Policy: default-src 'self'; " .
        "img-src 'self' data: blob:; " .
        "style-src 'self' 'unsafe-inline'; " .
        "script-src 'self' 'nonce-{$nonce}'; " .
        "object-src 'none'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'"
    );
}
