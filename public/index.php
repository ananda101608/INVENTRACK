<?php
// ====================================================================
// INVENTRACK - FRONT CONTROLLER
// ====================================================================

// 1. Muat konfigurasi umum & helper backend
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/helpers/session_helper.php';
require_once __DIR__ . '/../app/helpers/format_helper.php';
require_once __DIR__ . '/../app/helpers/csrf_helper.php';
require_once __DIR__ . '/../app/helpers/auth_helper.php';

// 2. Tentukan halaman & aksi yang diminta
$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// 3. Routing ke Controller atau View
$view_path     = __DIR__ . "/../views/{$page}/{$action}.php";
$view_fallback = __DIR__ . "/../views/{$page}/index.php";

if (file_exists($view_path) && filesize($view_path) > 0) {
    require_once $view_path;
} elseif (file_exists($view_fallback) && filesize($view_fallback) > 0) {
    require_once $view_fallback;
} else {
    echo "INVENTRACK - Halaman " . htmlspecialchars($page) . " (Frontend belum diisi oleh tim)";
}
