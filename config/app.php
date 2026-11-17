<?php
// ====================================================================
// KONFIGURASI UMUM APLIKASI INVENTRACK
// ====================================================================

// Nama dan Tagline
define('APP_NAME', 'INVENTRACK');
define('APP_TAGLINE', 'Track Every Item. Control Every Stock.');
define('APP_VERSION', '1.0.0');

// Base URL (otomatis menyesuaikan lingkungan XAMPP lokal)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$rootPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl  = rtrim($protocol . $host . $rootPath, '/');

// Base URL utama aplikasi
define('BASE_URL', $baseUrl);

// Base URL root (jika base URL saat ini berada di /public)
define('ROOT_URL', preg_replace('/\/public$/', '', $baseUrl));
