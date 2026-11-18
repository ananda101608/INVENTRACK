<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/session_helper.php';
require_once __DIR__ . '/../app/helpers/format_helper.php';
require_once __DIR__ . '/../app/helpers/csrf_helper.php';
require_once __DIR__ . '/../app/helpers/auth_helper.php';

$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

if ($page === 'login' || $page === 'login_proses') {
    $page   = 'auth';
    $action = ($_SERVER['REQUEST_METHOD'] === 'POST') ? 'login' : 'index';
} elseif ($page === 'logout') {
    $page   = 'auth';
    $action = 'logout';
}

$routeMap = [
    'auth'          => 'AuthController',
    'dashboard'     => 'DashboardController',
    'produk'        => 'ProdukController',
    'kategori'      => 'KategoriController',
    'satuan'        => 'SatuanController',
    'pemasok'       => 'PemasokController',
    'gudang'        => 'GudangController',
    'barang_masuk'  => 'BarangMasukController',
    'barang_keluar' => 'BarangKeluarController',
    'transfer'      => 'TransferController',
    'opname'        => 'OpnameController',
    'laporan'       => 'LaporanController',
    'pengguna'      => 'PenggunaController',
    'pengaturan'    => 'PengaturanController',
];

$controllerName = $routeMap[$page] ?? null;

if ($controllerName) {
    $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $action)) {
                $controller->$action();
                exit;
            }
        }
    }
}

$view_path     = __DIR__ . "/../views/{$page}/{$action}.php";
$view_fallback = __DIR__ . "/../views/{$page}/index.php";

if (file_exists($view_path) && filesize($view_path) > 0) {
    require_once $view_path;
} elseif (file_exists($view_fallback) && filesize($view_fallback) > 0) {
    require_once $view_fallback;
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Halaman tidak ditemukan.";
}
