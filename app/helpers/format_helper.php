<?php
// ====================================================================
// HELPER FORMAT & KEAMANAN INPUT
// ====================================================================

/**
 * Format angka menjadi mata uang Rupiah
 * Contoh: rupiah(50000) -> "Rp 50.000"
 */
function rupiah(float|int|string|null $angka): string {
    $nilai = (float) ($angka ?? 0);
    return 'Rp ' . number_format($nilai, 0, ',', '.');
}

/**
 * Format tanggal ke format Bahasa Indonesia
 * Contoh: tanggal_indonesia("2026-11-17") -> "17 November 2026"
 */
function tanggal_indonesia(?string $tanggal, bool $cetak_hari = false): string {
    if (!$tanggal || $tanggal === '0000-00-00') {
        return '-';
    }

    $hari = [
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
    ];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $time  = strtotime($tanggal);
    $tahun = date('Y', $time);
    $bln   = (int) date('m', $time);
    $tgl   = date('d', $time);

    $hasil = $tgl . ' ' . $bulan[$bln] . ' ' . $tahun;

    if ($cetak_hari) {
        $nama_hari = $hari[(int) date('w', $time)];
        $hasil = $nama_hari . ', ' . $hasil;
    }

    return $hasil;
}

/**
 * Membersihkan input dari potensi serangan XSS
 */
function bersihkan(string|null $data): string {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Mengambil nilai input sebelumnya (untuk form saat validasi gagal)
 */
function old(string $field, mixed $default = ''): mixed {
    return isset($_POST[$field]) ? bersihkan((string) $_POST[$field]) : $default;
}

/**
 * Helper redirect URL
 */
function redirect(string $path): void {
    if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
        $path = BASE_URL . '/' . ltrim($path, '/');
    }
    header("Location: " . $path);
    exit;
}

/**
 * Helper pembentuk URL lengkap
 */
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
