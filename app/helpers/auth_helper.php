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
 * Cek apakah akun sedang dibatasi karena terlalu banyak percobaan login.
 * Maksimal 5 kegagalan dalam 5 menit.
 */
function login_terkunci(int $maks = 5, int $jendela = 300): bool {
    $state = $_SESSION['login_attempts'] ?? null;
    if (!is_array($state) || empty($state['first'])) {
        return false;
    }

    if (time() - (int) $state['first'] > $jendela) {
        unset($_SESSION['login_attempts']);
        return false;
    }

    return (int) ($state['count'] ?? 0) >= $maks;
}

/**
 * Catat satu percobaan login yang gagal.
 */
function catat_login_gagal(): void {
    $state = $_SESSION['login_attempts'] ?? ['count' => 0, 'first' => time()];

    if (empty($state['first']) || time() - (int) $state['first'] > 300) {
        $state = ['count' => 0, 'first' => time()];
    }

    $state['count'] = (int) ($state['count'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $state;
}

/**
 * Hapus catatan percobaan login setelah berhasil.
 */
function reset_login_gagal(): void {
    unset($_SESSION['login_attempts']);
}

/**
 * Kebijakan panjang kata sandi minimal.
 */
function password_memenuhi_kebijakan(string $password): bool {
    return strlen($password) >= 8;
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
