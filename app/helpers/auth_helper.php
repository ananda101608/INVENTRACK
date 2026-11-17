<?php
// ====================================================================
// HELPER OTORISASI & KEAMANAN AKUN
// ====================================================================

require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/format_helper.php';

/**
 * Wajibkan pengguna login terlebih dahulu sebelum membuka halaman
 */
function require_login(): void {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        redirect('index.php?page=login');
    }
}

/**
 * Wajibkan pengguna belum login (khusus halaman login)
 */
function require_guest(): void {
    if (is_logged_in()) {
        redirect('index.php?page=dashboard');
    }
}

/**
 * Batasi akses halaman berdasarkan peran pengguna (admin, petugas, manager)
 * @param string|array $peran_diizinkan Contoh: 'admin' atau ['admin', 'manager']
 */
function require_role(string|array $peran_diizinkan): void {
    require_login();

    $roles = (array) $peran_diizinkan;
    $current_role = user_role();

    if (!in_array($current_role, $roles, true)) {
        set_flash('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        redirect('index.php?page=dashboard');
    }
}

/**
 * Enkripsi kata sandi secara aman
 */
function enkripsi_password(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Cek kecocokan kata sandi
 */
function cek_password(string $password, string $hash): bool {
    return password_verify($password, $hash);
}
