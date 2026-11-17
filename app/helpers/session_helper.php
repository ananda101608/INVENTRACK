<?php
// ====================================================================
// HELPER SESSION & NOTIFIKASI FLASH
// ====================================================================

// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Menyimpan pesan flash untuk ditampilkan sekali pada request berikutnya
 * @param string $tipe ('success', 'error', 'warning', 'info')
 * @param string $pesan Isi pesan
 */
function set_flash(string $tipe, string $pesan): void {
    $_SESSION['flash'] = [
        'tipe'  => $tipe,
        'pesan' => $pesan
    ];
}

/**
 * Mengambil dan langsung menghapus pesan flash dari session
 * @return array|null [ 'tipe' => ..., 'pesan' => ... ]
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Cek apakah ada notifikasi flash
 */
function has_flash(): bool {
    return isset($_SESSION['flash']);
}

/**
 * Cek apakah pengguna sudah login
 */
function is_logged_in(): bool {
    return isset($_SESSION['pengguna']) && !empty($_SESSION['pengguna']['id_pengguna']);
}

/**
 * Mengambil data pengguna yang sedang login
 */
function current_user(): ?array {
    return $_SESSION['pengguna'] ?? null;
}

/**
 * Mengambil peran/role pengguna yang sedang login (admin, petugas, manager)
 */
function user_role(): ?string {
    return $_SESSION['pengguna']['peran'] ?? null;
}
