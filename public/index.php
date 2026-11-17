<?php
// ====================================================================
// INVENTRACK - FRONT CONTROLLER & ROUTER SEDERHANA
// ====================================================================

// 1. Muat Konfigurasi & Helper
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/helpers/session_helper.php';
require_once __DIR__ . '/../app/helpers/format_helper.php';
require_once __DIR__ . '/../app/helpers/csrf_helper.php';
require_once __DIR__ . '/../app/helpers/auth_helper.php';

// 2. Tentukan Halaman yang Dituju
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// 3. Routing Khusus Autentikasi
if ($page === 'login') {
    $judul_halaman = 'Login Pengguna';
    require_once __DIR__ . '/../views/auth/login.php';
    exit;
}

if ($page === 'login_proses') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            set_flash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            redirect('index.php?page=login');
        }

        $email = bersihkan($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            set_flash('error', 'Email dan kata sandi wajib diisi!');
            redirect('index.php?page=login');
        }

        // Demo Login (karena database sedang dibuat oleh tim)
        // Nanti saat database selesai, kode ini diganti query ke PenggunaModel
        $_SESSION['pengguna'] = [
            'id_pengguna' => 1,
            'nama'        => 'Admin Inventrack',
            'email'       => $email,
            'peran'       => 'admin',
            'status'      => 'aktif'
        ];

        set_flash('success', 'Selamat datang kembali, Admin!');
        redirect('index.php?page=dashboard');
    }
    redirect('index.php?page=login');
}

if ($page === 'logout') {
    unset($_SESSION['pengguna']);
    session_destroy();
    session_start();
    set_flash('info', 'Anda telah berhasil keluar dari sistem.');
    redirect('index.php?page=login');
}

// 4. Daftar Judul Halaman
$daftar_judul = [
    'dashboard'     => 'Dashboard Ringkasan',
    'produk'        => 'Manajemen Produk',
    'kategori'      => 'Kategori Barang',
    'satuan'        => 'Satuan Barang',
    'pemasok'       => 'Data Pemasok (Supplier)',
    'gudang'        => 'Daftar Gudang',
    'barang_masuk'  => 'Transaksi Barang Masuk',
    'barang_keluar' => 'Transaksi Barang Keluar',
    'transfer'      => 'Transfer Antar Gudang',
    'opname'        => 'Stok Opname Fisik',
    'laporan'       => 'Laporan & Rekapitulasi',
    'pengguna'      => 'Manajemen Pengguna',
    'pengaturan'    => 'Pengaturan Akun & Profil'
];

$judul_halaman = $daftar_judul[$page] ?? 'INVENTRACK';

// 5. Muat Template Layout Atas
require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../views/layouts/sidebar.php';
require_once __DIR__ . '/../views/layouts/navbar.php';

// 6. Muat Konten Halaman Sesuai Permintaan
$view_path = __DIR__ . "/../views/{$page}/{$action}.php";
$view_fallback = __DIR__ . "/../views/{$page}/index.php";

if (file_exists($view_path) && filesize($view_path) > 0) {
    require_once $view_path;
} elseif (file_exists($view_fallback) && filesize($view_fallback) > 0) {
    require_once $view_fallback;
} else {
    // Tampilan placeholder rapi jika file view masih kosong (sedang dibuat oleh anggota tim)
    ?>
    <div class="bg-white rounded-2xl border border-slate-200 p-8 card-shadow text-center max-w-xl mx-auto my-12">
        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <h2 class="text-lg font-bold text-slate-800"><?= bersihkan($judul_halaman) ?></h2>
        <p class="text-xs text-slate-500 mt-2">
            Halaman ini berada di file <code class="bg-slate-100 text-blue-600 px-2 py-0.5 rounded font-mono text-xs">views/<?= bersihkan($page) ?>/<?= bersihkan($action) ?>.php</code> dan sedang dalam proses pengerjaan oleh tim Anda.
        </p>
        <div class="mt-6">
            <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </div>
    <?php
}

// 7. Muat Template Layout Bawah
require_once __DIR__ . '/../views/layouts/footer.php';
